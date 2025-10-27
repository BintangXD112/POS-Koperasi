<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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

		// Category performance data for chart
		$categoryPerformance = \App\Models\Category::with(['products'])
			->get()
			->map(function ($category) use ($yearStr, $monthStr) {
				$transactionCount = 0;
				foreach ($category->products as $product) {
					$count = $product->transactionDetails()
						->whereHas('transaction', function ($t) use ($yearStr, $monthStr) {
							$t->where('status', 'completed')
								->whereRaw('substr(created_at,1,4) = ?', [$yearStr]);
							if ($monthStr !== '') {
								$t->whereRaw('substr(created_at,6,2) = ?', [$monthStr]);
							}
						})
						->count();
					$transactionCount += $count;
				}
				return [
					'id' => $category->id,
					'name' => $category->name,
					'transaction_count' => $transactionCount
				];
			})
			->sortByDesc('transaction_count')
			->values();

		// Sales trend data for last 12 months
		$salesTrend = Transaction::completed()
			->whereRaw('created_at >= date("now", "-12 months")')
			->selectRaw('substr(created_at,1,7) as month, SUM(CAST(total_amount AS REAL)) as revenue, COUNT(*) as transaction_count')
			->groupBy('month')
			->orderBy('month')
			->get();

		return view('admin.reports', compact('monthlyRevenue','dailyRevenue','topProducts','year','month','totalTransactionsFiltered','totalRevenueSum','categoryPerformance','salesTrend'));
	}

	public function appSettings()
	{
		$lazyLoading = AppSetting::getValue('lazy_loading', true);
		$darkMode = AppSetting::getValue('dark_mode', false);
		$emailNotifications = AppSetting::getValue('email_notifications', true);
		$browserNotifications = AppSetting::getValue('browser_notifications', true);
		$autoExportEnabled = AppSetting::getValue('auto_export_enabled', false);
		$autoExportSchedule = AppSetting::getValue('auto_export_schedule', 'daily');
		$autoExportFormat = AppSetting::getValue('auto_export_format', 'excel');
		$dataCleanupEnabled = AppSetting::getValue('data_cleanup_enabled', false);
		$dataRetentionDays = AppSetting::getValue('data_retention_days', 365);
		
		return view('admin.app-settings', compact(
			'lazyLoading', 'darkMode', 'emailNotifications', 'browserNotifications',
			'autoExportEnabled', 'autoExportSchedule', 'autoExportFormat',
			'dataCleanupEnabled', 'dataRetentionDays'
		));
	}

	public function updateAppSettings(Request $request)
	{
		$request->validate([
			'lazy_loading' => 'boolean',
			'dark_mode' => 'boolean',
			'email_notifications' => 'boolean',
			'browser_notifications' => 'boolean',
			'auto_export_enabled' => 'boolean',
			'auto_export_schedule' => 'string|in:daily,weekly,monthly',
			'auto_export_format' => 'string|in:excel,pdf',
			'data_cleanup_enabled' => 'boolean',
			'data_retention_days' => 'integer|min:30|max:3650'
		]);

		// Performance Settings
		AppSetting::setValue('lazy_loading', $request->boolean('lazy_loading'), 'boolean', 'Enable/disable lazy loading for better user experience');
		
		// Theme Settings
		AppSetting::setValue('dark_mode', $request->boolean('dark_mode'), 'boolean', 'Enable dark mode theme for better viewing experience');
		
		// Notification Settings
		AppSetting::setValue('email_notifications', $request->boolean('email_notifications'), 'boolean', 'Enable email notifications for transactions and updates');
		AppSetting::setValue('browser_notifications', $request->boolean('browser_notifications'), 'boolean', 'Enable browser push notifications');
		
		// Auto Export Settings
		AppSetting::setValue('auto_export_enabled', $request->boolean('auto_export_enabled'), 'boolean', 'Enable automatic export of reports');
		AppSetting::setValue('auto_export_schedule', $request->get('auto_export_schedule'), 'string', 'Auto export schedule: daily, weekly, monthly');
		AppSetting::setValue('auto_export_format', $request->get('auto_export_format'), 'string', 'Default export format: excel, pdf');
		
		// Data Cleanup Settings
		AppSetting::setValue('data_cleanup_enabled', $request->boolean('data_cleanup_enabled'), 'boolean', 'Enable automatic cleanup of old data');
		AppSetting::setValue('data_retention_days', $request->get('data_retention_days'), 'integer', 'Number of days to retain data before cleanup');

		return redirect()->route('admin.app-settings')
			->with('success', 'Pengaturan aplikasi berhasil diperbarui!');
	}

	public function exportReports(Request $request)
	{
		$format = $request->get('format', 'excel');
		$year = (int) ($request->get('year') ?: date('Y'));
		$month = (string) ($request->get('month') ?: '');
		$yearStr = (string) $year;
		$monthStr = $month !== '' ? str_pad($month, 2, '0', STR_PAD_LEFT) : '';

		$transactions = Transaction::completed()
			->with(['user', 'details.product'])
			->whereRaw('substr(created_at,1,4) = ?', [$yearStr])
			->when($monthStr !== '', function ($q) use ($monthStr) { $q->whereRaw('substr(created_at,6,2) = ?', [$monthStr]); })
			->orderBy('created_at', 'asc')
			->get();

		$filename = 'laporan_koperasi_' . $yearStr . ($monthStr !== '' ? '_' . $monthStr : '');

		if ($format === 'pdf') {
			$pdf = Pdf::loadView('admin.reports.export-pdf', compact('transactions', 'year', 'month'));
			$pdf->setPaper('A4', 'landscape');
			return $pdf->download($filename . '.pdf');
		} else {
			return Excel::download(new ReportsExport($transactions, $year, $month), $filename . '.xlsx');
		}
	}
}
