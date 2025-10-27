<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\StoreSettingController;
use App\Http\Controllers\MarketAnalysisController;
use App\Http\Controllers\SmartInventoryController;
use App\Http\Controllers\CustomerIntelligenceController;
use App\Http\Controllers\PredictiveAnalyticsController;
use App\Http\Controllers\AutomatedReportingController;
use App\Http\Controllers\AIChatbotController;

// Authentication routes
Route::middleware('guest')->group(function () {
	Route::get('/login', function () {
		return response()
			->view('auth.login')
			->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
			->header('Pragma', 'no-cache')
			->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
	})->name('login');

	// Forgot password
	Route::get('/forgot-password', function () {
		return view('auth.forgot-password');
	})->name('password.request');

	Route::post('/forgot-password', function () {
		request()->validate(['email' => 'required|email']);
		\Illuminate\Support\Facades\Password::sendResetLink(request()->only('email'));
		return back()->with('success', 'Jika email terdaftar, link reset telah dikirim.');
	})->name('password.email');

	Route::get('/reset-password/{token}', function (string $token) {
		return view('auth.reset-password', ['token' => $token, 'email' => request('email')]);
	})->name('password.reset');

	Route::post('/reset-password', function () {
		request()->validate([
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:8|confirmed',
		]);

		$status = \Illuminate\Support\Facades\Password::reset(
			request(['email', 'password', 'password_confirmation', 'token']),
			function ($user, $password) {
				$user->forceFill([
					'password' => \Illuminate\Support\Facades\Hash::make($password),
					'remember_token' => \Illuminate\Support\Str::random(60),
				])->save();
			}
		);

		return $status == \Illuminate\Support\Facades\Password::PASSWORD_RESET
			? redirect('/login')->with('success', 'Password berhasil direset, silakan login.')
			: back()->with('error', 'Token tidak valid atau email tidak cocok.');
	})->name('password.update');

	Route::post('/login', function () {
		$credentials = request()->only('email', 'password');
		$remember = (bool) request('remember', false);

		if (Auth::attempt($credentials, $remember)) {
			// Regenerate session untuk mencegah session fixation
			request()->session()->regenerate();
			request()->session()->put('last_activity', now()->timestamp);
			request()->session()->flash('success', 'Login berhasil. Selamat datang!');

			// Log login activity
			\App\Models\ActivityLog::log('login', 'User berhasil login ke sistem');

			$user = auth()->user();
			if ($user->isAdmin()) {
				return redirect()->route('admin.dashboard');
			} elseif ($user->isKasir()) {
				return redirect()->route('kasir.dashboard');
			} elseif ($user->isGudang()) {
				return redirect()->route('gudang.dashboard');
			}
		} else {
			// Check if user exists in database
			$user = \App\Models\User::where('email', $credentials['email'])->first();
			
			if ($user) {
				// User exists but wrong password
				\App\Models\ActivityLog::log('failed_login', 'Login gagal - password salah', [
					'email' => $credentials['email'],
					'user_id' => $user->id,
					'user_name' => $user->name,
					'user_role' => $user->role->display_name ?? 'Unknown'
				]);
			} else {
				// User not found in database
				\App\Models\ActivityLog::log('failed_login', 'Login gagal - user tidak dikenal', [
					'email' => $credentials['email'],
					'user_exists' => false
				]);
			}
		}
		
		return back()->withErrors(['email' => 'Email atau password salah']);
	})->name('login.post');
});

Route::post('/logout', function () {
	// Log logout activity before logout
	\App\Models\ActivityLog::log('logout', 'User logout dari sistem');
	
	// Logout aman dan invalidate session
	Auth::logout();
	request()->session()->invalidate();
	request()->session()->regenerateToken();
	return redirect('/login')->with('success', 'Anda telah logout. Sampai jumpa!');
})->name('logout');

// Global Group Chat (All roles)
Route::middleware(['auth', 'active.session'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/latest', [ChatController::class, 'latest'])->name('chat.latest');
    Route::post('/chat/clear', [ChatController::class, 'clear'])->name('chat.clear');
    Route::delete('/chat/messages/{message}', [ChatController::class, 'delete'])->name('chat.delete');
});

// Profile routes (All authenticated users)
Route::middleware(['auth', 'active.session'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Note: Authenticated users are already blocked from guest routes by 'guest' middleware
// and will be redirected to '/'. The root route below will forward them to their role dashboard.

// Admin routes
Route::middleware(['auth', 'active.session', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
	Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
	Route::get('/users', [AdminController::class, 'users'])->name('users');
	Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
	Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
	Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
	Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
	Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
	Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
	Route::get('/reports/export', [AdminController::class, 'exportReports'])->name('reports.export');
	
	// Activity Logs
	Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
	Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
	Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
	Route::get('/activity-logs/statistics', [ActivityLogController::class, 'statistics'])->name('activity-logs.statistics');
	
	// Store Settings
	Route::get('/store-settings', [StoreSettingController::class, 'index'])->name('store-settings');
	Route::put('/store-settings', [StoreSettingController::class, 'update'])->name('store-settings.update');
	Route::delete('/store-settings/logo', [StoreSettingController::class, 'removeLogo'])->name('store-settings.remove-logo');
	Route::get('/store-settings/api', [StoreSettingController::class, 'getSettings'])->name('store-settings.api');
	Route::get('/store-settings/timezones', [StoreSettingController::class, 'getTimezones'])->name('store-settings.timezones');
	
	// App Settings
	Route::get('/app-settings', [AdminController::class, 'appSettings'])->name('app-settings');
	Route::post('/app-settings', [AdminController::class, 'updateAppSettings'])->name('app-settings.update');
	
        // Market Analysis
        Route::get('/market-analysis', [MarketAnalysisController::class, 'index'])->name('market-analysis.index');
        Route::get('/market-analysis/create', [MarketAnalysisController::class, 'create'])->name('market-analysis.create');
        Route::post('/market-analysis/generate', [MarketAnalysisController::class, 'generateAnalysis'])->name('market-analysis.generate');
        Route::get('/market-analysis/{marketAnalysis}', [MarketAnalysisController::class, 'show'])->name('market-analysis.show');
        Route::delete('/market-analysis/{marketAnalysis}', [MarketAnalysisController::class, 'destroy'])->name('market-analysis.destroy');
        
        // Smart Inventory Management
        Route::get('/smart-inventory', [SmartInventoryController::class, 'index'])->name('smart-inventory.index');
        Route::post('/smart-inventory/generate', [SmartInventoryController::class, 'generateRecommendations'])->name('smart-inventory.generate');
        
        // Customer Intelligence
        Route::get('/customer-intelligence', [CustomerIntelligenceController::class, 'index'])->name('customer-intelligence.index');
        Route::post('/customer-intelligence/generate', [CustomerIntelligenceController::class, 'generateInsights'])->name('customer-intelligence.generate');
        
        // Predictive Analytics
        Route::get('/predictive-analytics', [PredictiveAnalyticsController::class, 'index'])->name('predictive-analytics.index');
        Route::post('/predictive-analytics/generate', [PredictiveAnalyticsController::class, 'generateForecast'])->name('predictive-analytics.generate');
        
    // Automated Reporting
    Route::get('/automated-reporting', [AutomatedReportingController::class, 'index'])->name('automated-reporting.index');
    Route::post('/automated-reporting/generate', [AutomatedReportingController::class, 'generateReport'])->name('automated-reporting.generate');
    Route::post('/automated-reporting/schedule', [AutomatedReportingController::class, 'scheduleReport'])->name('automated-reporting.schedule');

    // AI Chatbot
    Route::get('/ai-chatbot', [AIChatbotController::class, 'index'])->name('ai-chatbot.index');
    Route::post('/ai-chatbot/send', [AIChatbotController::class, 'sendMessage'])->name('ai-chatbot.send');
    Route::post('/ai-chatbot/session/create', [AIChatbotController::class, 'createSession'])->name('ai-chatbot.session.create');
    Route::post('/ai-chatbot/session/close', [AIChatbotController::class, 'closeSession'])->name('ai-chatbot.session.close');
    Route::get('/ai-chatbot/sessions', [AIChatbotController::class, 'getSessions'])->name('ai-chatbot.sessions');
});


// Kasir routes
Route::middleware(['auth', 'active.session', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
	Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
	Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
	Route::post('/transactions', [KasirController::class, 'storeTransaction'])->name('transactions.store');
	Route::get('/transactions', [KasirController::class, 'transactions'])->name('transactions');
	Route::get('/transactions/export', [KasirController::class, 'exportTransactions'])->name('transactions.export');
	Route::get('/transactions/{transaction}', [KasirController::class, 'showTransaction'])->name('transactions.show');
	Route::post('/transactions/{transaction}/cancel', [KasirController::class, 'cancelTransaction'])->name('transactions.cancel');
	Route::get('/transactions/{transaction}/print', [KasirController::class, 'printTransaction'])->name('transactions.print');
	Route::get('/search-products', [KasirController::class, 'searchProduct'])->name('search.products');
});

// Gudang routes
Route::middleware(['auth', 'active.session', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
	Route::get('/dashboard', [GudangController::class, 'dashboard'])->name('dashboard');
	Route::get('/products', [GudangController::class, 'products'])->name('products');
	Route::get('/products/create', [GudangController::class, 'createProduct'])->name('products.create');
	Route::post('/products', [GudangController::class, 'storeProduct'])->name('products.store');
	Route::get('/products/{product}/edit', [GudangController::class, 'editProduct'])->name('products.edit');
	Route::put('/products/{product}', [GudangController::class, 'updateProduct'])->name('products.update');
	Route::delete('/products/{product}', [GudangController::class, 'deleteProduct'])->name('products.delete');
	Route::post('/products/{product}/adjust-stock', [GudangController::class, 'adjustStock'])->name('products.adjust-stock');
	Route::get('/categories', [GudangController::class, 'categories'])->name('categories');
	Route::get('/categories/create', [GudangController::class, 'createCategory'])->name('categories.create');
	Route::post('/categories', [GudangController::class, 'storeCategory'])->name('categories.store');
	Route::get('/categories/{category}/edit', [GudangController::class, 'editCategory'])->name('categories.edit');
	Route::put('/categories/{category}', [GudangController::class, 'updateCategory'])->name('categories.update');
	Route::delete('/categories/{category}', [GudangController::class, 'deleteCategory'])->name('categories.delete');
	Route::get('/reports/stock', [GudangController::class, 'stockReport'])->name('reports.stock');
	Route::get('/reports/stock/export', [GudangController::class, 'exportStockReport'])->name('reports.stock.export');
});

// Redirect root to appropriate dashboard based on user role
Route::get('/', function () {
	if (auth()->check()) {
		$user = auth()->user();
		if ($user->isAdmin()) {
			return redirect()->route('admin.dashboard');
		} elseif ($user->isKasir()) {
			return redirect()->route('kasir.dashboard');
		} elseif ($user->isGudang()) {
			return redirect()->route('gudang.dashboard');
		}
	}

	return redirect('/login');
});
