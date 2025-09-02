@extends('layouts.app')

@section('title', 'Point of Sale')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Point of Sale</h1>
        <p class="mt-1 text-sm text-gray-600">Buat transaksi baru untuk pelanggan</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Product Selection -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pilih Produk</h3>
                
                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" id="searchProduct" placeholder="Cari produk atau SKU..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Product List -->
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @forelse($products as $product)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md hover:bg-gray-50">
                            <div>
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->category->name }} | Stok: {{ $product->stock }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <button onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})" 
                                        class="mt-1 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Tambah
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada produk tersedia</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Cart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Keranjang Belanja</h3>
                
                <form action="{{ route('kasir.transactions.store') }}" method="POST">
                    @csrf
                    
                    <!-- Cart Items -->
                    <div id="cartItems" class="space-y-3 mb-4 min-h-32">
                        <p class="text-gray-500 text-center py-8">Keranjang kosong</p>
                    </div>

                    <!-- Total -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between text-lg font-medium">
                            <span>Total:</span>
                            <span id="totalAmount">Rp 0</span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="notes" id="notes" rows="2" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Hidden inputs for cart items -->
                    <div id="cartInputs"></div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitBtn" disabled
                            class="mt-4 w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Proses Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let cartTotal = 0;

function addToCart(productId, productName, price, stock) {
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        if (existingItem.quantity < stock) {
            existingItem.quantity++;
            existingItem.subtotal = existingItem.quantity * price;
        } else {
            alert('Stok tidak mencukupi!');
            return;
        }
    } else {
        cart.push({
            product_id: productId,
            name: productName,
            price: price,
            quantity: 1,
            subtotal: price
        });
    }
    
    updateCartDisplay();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function updateQuantity(index, newQuantity) {
    if (newQuantity > 0) {
        cart[index].quantity = newQuantity;
        cart[index].subtotal = cart[index].quantity * cart[index].price;
        updateCartDisplay();
    }
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const cartInputs = document.getElementById('cartInputs');
    const totalAmount = document.getElementById('totalAmount');
    const submitBtn = document.getElementById('submitBtn');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-gray-500 text-center py-8">Keranjang kosong</p>';
        totalAmount.textContent = 'Rp 0';
        submitBtn.disabled = true;
        cartInputs.innerHTML = '';
        return;
    }
    
    // Update cart items display
    cartItems.innerHTML = cart.map((item, index) => `
        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
            <div class="flex-1">
                <p class="font-medium text-gray-900">${item.name}</p>
                <p class="text-sm text-gray-500">Rp ${item.price.toLocaleString('id-ID')} x ${item.quantity}</p>
            </div>
            <div class="flex items-center space-x-2">
                <input type="number" min="1" value="${item.quantity}" 
                       onchange="updateQuantity(${index}, parseInt(this.value))"
                       class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                <span class="font-medium text-gray-900">Rp ${item.subtotal.toLocaleString('id-ID')}</span>
                <button type="button" onclick="removeFromCart(${index})" 
                        class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
    
    // Update hidden inputs
    cartInputs.innerHTML = cart.map(item => `
        <input type="hidden" name="items[${item.product_id}][product_id]" value="${item.product_id}">
        <input type="hidden" name="items[${item.product_id}][quantity]" value="${item.quantity}">
    `).join('');
    
    // Update total
    cartTotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    totalAmount.textContent = `Rp ${cartTotal.toLocaleString('id-ID')}`;
    submitBtn.disabled = false;
}

// Search functionality
document.getElementById('searchProduct').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const productItems = document.querySelectorAll('#cartItems').previousElementSibling.querySelectorAll('.border');
    
    productItems.forEach(item => {
        const productName = item.querySelector('.font-medium').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection

