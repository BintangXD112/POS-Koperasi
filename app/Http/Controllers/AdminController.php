<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
	public function dashboard()
	{
		$totalUsers = User::count();
		$totalProducts = Product::count();
		$totalTransactions = Transaction::count();
		$totalRevenue = Transaction::completed()->sum('total_amount');
		$recentTransactions = Transaction::with('user')->latest()->take(5)->get();
		$lowStockProducts = Product::lowStock()->with('category')->get();
		return view('admin.dashboard', compact('totalUsers','totalProducts','totalTransactions','totalRevenue','recentTransactions','lowStockProducts'));
	}

	public function users()
	{
		$users = User::with('role')->paginate(10);
		$roles = Role::all();
		return view('admin.users.index', compact('users', 'roles'));
	}

	public function createUser()
	{
		$roles = Role::all();
		return view('admin.users.create', compact('roles'));
	}

	public function storeUser(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => 'required|string|min:8|confirmed',
			'role_id' => 'required|exists:roles,id'
		]);
		User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'role_id' => $request->role_id
		]);
		return redirect()->route('admin.users')->with('success', 'User berhasil dibuat');
	}

	public function editUser(User $user)
	{
		$roles = Role::all();
		return view('admin.users.edit', compact('user', 'roles'));
	}

	public function updateUser(Request $request, User $user)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
			'role_id' => 'required|exists:roles,id'
		]);
		$user->update(['name'=>$request->name,'email'=>$request->email,'role_id'=>$request->role_id]);
		if ($request->filled('password')) {
			$request->validate(['password' => 'string|min:8|confirmed']);
			$user->update(['password' => Hash::make($request->password)]);
		}
		return redirect()->route('admin.users')->with('success', 'User berhasil diupdate');
	}

	public function deleteUser(User $user)
	{
		if ($user->isAdmin() && User::where('role_id', $user->role_id)->count() === 1) {
			return back()->with('error', 'Tidak dapat menghapus admin terakhir');
		}
		$user->delete();
		return back()->with('success', 'User berhasil dihapus');
	}

	public function reports(Request $request)
	{
		$year = (int) ($request->get('year') ?: date('Y'));
		$month = (string) ($request->get('month') ?: '');

		$yearStr = (string) $year; // substr(created_at,1,4)
		$monthStr = $month !== '' ? str_pad($month, 2, '0', STR_PAD_LEFT) : '';

		// Aggregation: monthly by default; daily when a month is selected
		if ($monthStr !== '') {
			$dailyRevenue = Transaction::completed()
				->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
				->whereRaw('substr(created_at,6,2) = ?', [$monthStr])
				->selectRaw('substr(created_at,1,10) as day, SUM(CAST(total_amount AS REAL)) as revenue')
				->groupBy('day')
				->orderBy('day')
				->get();
			$monthlyRevenue = collect();
		} else {
			$monthlyRevenue = Transaction::completed()
				->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
				->selectRaw('substr(created_at,6,2) as month, SUM(CAST(total_amount AS REAL)) as revenue')
				->groupBy('month')
				->orderBy('month')
				->get();
			$dailyRevenue = collect();
		}

		$totalTransactionsFiltered = Transaction::completed()
			->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
			->when($monthStr !== '', function ($q) use ($monthStr) { $q->whereRaw('substr(created_at,6,2) = ?', [$monthStr]); })
			->count();

		$totalRevenueSum = Transaction::completed()
			->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
			->when($monthStr !== '', function ($q) use ($monthStr) { $q->whereRaw('substr(created_at,6,2) = ?', [$monthStr]); })
			->selectRaw('SUM(CAST(total_amount AS REAL)) as total')
			->value('total') ?? 0;

		$topProducts = Product::withCount(['transactionDetails as transaction_details_count' => function ($q) use ($yearStr, $monthStr) {
			$q->whereHas('transaction', function ($t) use ($yearStr, $monthStr) {
				$t->where('status', 'completed')
					->whereRaw('substr(created_at,1,4) = ?', [$yearStr]);
				if ($monthStr !== '') {
					$t->whereRaw('substr(created_at,6,2) = ?', [$monthStr]);
				}
			});
		}])
			->orderBy('transaction_details_count', 'desc')
			->take(10)
			->get();

		return view('admin.reports', compact('monthlyRevenue','dailyRevenue','topProducts','year','month','totalTransactionsFiltered','totalRevenueSum'));
	}

	public function exportReports(Request $request): StreamedResponse
	{
		$year = (int) ($request->get('year') ?: date('Y'));
		$month = (string) ($request->get('month') ?: '');
		$yearStr = (string) $year;
		$monthStr = $month !== '' ? str_pad($month, 2, '0', STR_PAD_LEFT) : '';

		$transactions = Transaction::completed()
			->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
			->when($monthStr !== '', function ($q) use ($monthStr) { $q->whereRaw('substr(created_at,6,2) = ?', [$monthStr]); })
			->orderBy('created_at', 'asc')
			->get(['transaction_number','user_id','total_amount','status','created_at']);

		$filename = 'laporan_koperasi_' . $yearStr . ($monthStr !== '' ? '_' . $monthStr : '') . '.csv';
		$headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="'.$filename.'"'];
		$callback = function () use ($transactions) {
			$out = fopen('php://output', 'w');
			fputcsv($out, ['No Transaksi','User ID','Total','Status','Tanggal']);
			foreach ($transactions as $t) {
				fputcsv($out, [$t->transaction_number, $t->user_id, number_format((float)$t->total_amount, 2, '.', ''), $t->status, $t->created_at]);
			}
			fclose($out);
		};
		return response()->stream($callback, 200, $headers);
	}
}
