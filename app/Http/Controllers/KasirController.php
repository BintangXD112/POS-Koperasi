<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Category;
use Illuminate\Support\Str;

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
        $products = Product::inStock()->with('category')->get();
        
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

    public function transactions()
    {
        $transactions = Transaction::with('user')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);
            
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
}
