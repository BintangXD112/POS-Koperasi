<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    
    if (auth()->attempt($credentials)) {
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

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

// Kasir routes
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
    Route::post('/transactions', [KasirController::class, 'storeTransaction'])->name('transactions.store');
    Route::get('/transactions', [KasirController::class, 'transactions'])->name('transactions');
    Route::get('/transactions/{transaction}', [KasirController::class, 'showTransaction'])->name('transactions.show');
    Route::post('/transactions/{transaction}/cancel', [KasirController::class, 'cancelTransaction'])->name('transactions.cancel');
    Route::get('/search-products', [KasirController::class, 'searchProduct'])->name('search.products');
});

// Gudang routes
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
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
