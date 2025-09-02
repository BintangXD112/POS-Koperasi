<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Str;

class GudangController extends Controller
{

    public function dashboard()
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        $totalCategories = Category::count();
        
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $lowStockList = Product::lowStock()->with('category')->get();
        
        return view('gudang.dashboard', compact(
            'totalProducts', 
            'lowStockProducts', 
            'outOfStockProducts', 
            'totalCategories',
            'recentProducts',
            'lowStockList'
        ));
    }

    public function products()
    {
        $products = Product::with('category')->paginate(15);
        $categories = Category::all();
        
        return view('gudang.products.index', compact('products', 'categories'));
    }

    public function createProduct()
    {
        $categories = Category::all();
        return view('gudang.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $sku = 'SKU-' . strtoupper(Str::random(8));
        
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'sku' => $sku,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('gudang.products')->with('success', 'Produk berhasil dibuat');
    }

    public function editProduct(Product $product)
    {
        $categories = Category::all();
        return view('gudang.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id
        ]);

        return redirect()->route('gudang.products')->with('success', 'Produk berhasil diupdate');
    }

    public function deleteProduct(Product $product)
    {
        if ($product->transactionDetails()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah ada transaksi');
        }

        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $quantity = $request->quantity;
        
        if ($request->adjustment_type === 'subtract') {
            if ($product->stock < $quantity) {
                return back()->with('error', 'Stok tidak mencukupi untuk pengurangan');
            }
            $product->decrement('stock', $quantity);
        } else {
            $product->increment('stock', $quantity);
        }

        return back()->with('success', 'Stok berhasil disesuaikan');
    }

    public function categories()
    {
        $categories = Category::withCount('products')->paginate(15);
        return view('gudang.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('gudang.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('gudang.categories')->with('success', 'Kategori berhasil dibuat');
    }

    public function editCategory(Category $category)
    {
        return view('gudang.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('gudang.categories')->with('success', 'Kategori berhasil diupdate');
    }

    public function deleteCategory(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih ada produk');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function stockReport()
    {
        $products = Product::with('category')
            ->orderBy('stock', 'asc')
            ->get();
            
        $lowStockProducts = Product::lowStock()->with('category')->get();
        $outOfStockProducts = Product::where('stock', 0)->with('category')->get();
        
        return view('gudang.reports.stock', compact('products', 'lowStockProducts', 'outOfStockProducts'));
    }
}
