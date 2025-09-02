<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

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

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalProducts', 
            'totalTransactions', 
            'totalRevenue',
            'recentTransactions',
            'lowStockProducts'
        ));
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

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id
        ]);

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

    public function reports()
    {
        // Use SQLite compatible date functions
        $monthlyRevenue = Transaction::completed()
            ->selectRaw('strftime("%m", created_at) as month, SUM(total_amount) as revenue')
            ->whereRaw('strftime("%Y", created_at) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topProducts = Product::withCount('transactionDetails')
            ->orderBy('transaction_details_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.reports', compact('monthlyRevenue', 'topProducts'));
    }
}
