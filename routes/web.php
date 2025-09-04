<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;

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

		if (auth()->attempt($credentials, $remember)) {
			// Regenerate session untuk mencegah session fixation
			request()->session()->regenerate();
			request()->session()->put('last_activity', now()->timestamp);
			request()->session()->flash('success', 'Login berhasil. Selamat datang!');

			$user = auth()->user();
			if ($user->isAdmin()) {
				return redirect()->route('admin.dashboard');
			} elseif ($user->isKasir()) {
				return redirect()->route('kasir.dashboard');
			} elseif ($user->isGudang()) {
				return redirect()->route('gudang.dashboard');
			}
		}
		
		return back()->withErrors(['email' => 'Email atau password salah']);
	})->name('login.post');
});

Route::post('/logout', function () {
	// Logout aman dan invalidate session
	auth()->logout();
	request()->session()->invalidate();
	request()->session()->regenerateToken();
	return redirect('/login')->with('success', 'Anda telah logout. Sampai jumpa!');
})->name('logout');

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
});

// Kasir routes
Route::middleware(['auth', 'active.session', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
	Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
	Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
	Route::post('/transactions', [KasirController::class, 'storeTransaction'])->name('transactions.store');
	Route::get('/transactions', [KasirController::class, 'transactions'])->name('transactions');
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
