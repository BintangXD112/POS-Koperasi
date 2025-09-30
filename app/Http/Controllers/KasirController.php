<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Exports\TransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class KasirController extends Controller
{

    public function dashboard()
    {
        // Use SQLite compatible date functions
        $todayTransactions = Transaction::whereRaw('date(created_at) = date(?)', [now()])->count();
        $todayRevenue = Transaction::whereRaw('date(created_at) = date(?)', [now()])->sum('total_amount');
        $recentTransactions = Transaction::with('user')->latest()->take(5)->get();
        
        return view('kasir.dashboard', compact('todayTransactions', 'todayRevenue', 'recentTransactions'));
    }

    public function pos()
    {
        $categories = Category::with('products')->get();
        // Include out-of-stock products but order them to the bottom
        $products = Product::with('category')
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
            ->orderBy('name')
            ->get();
        
        return view('kasir.pos', compact('categories', 'products'));
    }

    public function searchProduct(Request $request)
    {
        $query = $request->get('query');
        
        $products = Product::inStock()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('category')
            ->get();
            
        return response()->json($products);
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $totalAmount = 0;
        $items = collect($request->items);

        // Calculate total and check stock
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->with('error', "Stok {$product->name} tidak mencukupi");
            }
            $totalAmount += $product->price * $item['quantity'];
        }

        // Create transaction
        $transaction = Transaction::create([
            'transaction_number' => 'TRX-' . date('Ymd') . '-' . Str::random(6),
            'user_id' => auth()->id(),
            'total_amount' => $totalAmount,
            'status' => 'completed',
            'notes' => $request->notes
        ]);

        // Create transaction details and update stock
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ]);

            // Update stock
            $product->decrement('stock', $item['quantity']);
        }

        return redirect()->route('kasir.transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil dibuat');
    }

    public function transactions(Request $request)
    {
        $query = Transaction::with('user')
            ->where('user_id', auth()->id());

        // Apply filters
        if ($request->has('period')) {
            $period = $request->get('period');
            
            if ($period === 'today') {
                $query->whereRaw('date(created_at) = date(?)', [now()]);
            } elseif ($period === 'week') {
                $query->whereRaw('date(created_at) >= date(?, "-7 days")', [now()]);
            } elseif ($period === 'month') {
                $query->whereRaw('strftime("%Y-%m", created_at) = strftime("%Y-%m", ?)', [now()]);
            } elseif ($period === 'custom') {
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = $request->get('start_date');
                    $endDate = $request->get('end_date');
                    $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }
        }

        $transactions = $query->latest()->paginate(15);
        
        return view('kasir.transactions.index', compact('transactions'));
    }

    public function showTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('kasir.transactions.show', compact('transaction'));
    }

    public function printTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load('details.product', 'user');
        return view('kasir.transactions.print', compact('transaction'));
    }

    public function cancelTransaction(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        if ($transaction->status !== 'completed') {
            return back()->with('error', 'Hanya transaksi yang sudah selesai yang dapat dibatalkan');
        }

        // Return stock
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stock', $detail->quantity);
        }

        $transaction->update(['status' => 'cancelled']);

        return back()->with('success', 'Transaksi berhasil dibatalkan');
    }

    public function exportTransactions(Request $request)
    {
        $format = $request->get('format', 'excel');
        $query = Transaction::with(['user', 'details.product'])
            ->where('user_id', auth()->id());

        // Apply same filters as transactions method
        if ($request->has('period')) {
            $period = $request->get('period');
            
            if ($period === 'today') {
                $query->whereRaw('date(created_at) = date(?)', [now()]);
            } elseif ($period === 'week') {
                $query->whereRaw('date(created_at) >= date(?, "-7 days")', [now()]);
            } elseif ($period === 'month') {
                $query->whereRaw('strftime("%Y-%m", created_at) = strftime("%Y-%m", ?)', [now()]);
            } elseif ($period === 'custom') {
                if ($request->has('start_date') && $request->has('end_date')) {
                    $startDate = $request->get('start_date');
                    $endDate = $request->get('end_date');
                    $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                }
            }
        }

        $transactions = $query->latest()->get();

        // Generate filename based on filter
        $filename = 'riwayat_transaksi_' . date('Y-m-d');
        if ($request->has('period')) {
            $period = $request->get('period');
            if ($period === 'today') {
                $filename .= '_hari_ini';
            } elseif ($period === 'week') {
                $filename .= '_minggu_ini';
            } elseif ($period === 'month') {
                $filename .= '_bulan_ini';
            } elseif ($period === 'custom' && $request->has('start_date') && $request->has('end_date')) {
                $startDate = \Carbon\Carbon::parse($request->get('start_date'))->format('Y-m-d');
                $endDate = \Carbon\Carbon::parse($request->get('end_date'))->format('Y-m-d');
                $filename .= '_' . $startDate . '_sampai_' . $endDate;
            }
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('kasir.transactions.export-pdf', compact('transactions'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($filename . '.pdf');
        } else {
            return Excel::download(new TransactionsExport($transactions), $filename . '.xlsx');
        }
    }
}
