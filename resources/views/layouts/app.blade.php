<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Koperasi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar - Always visible on desktop -->
        <div class="hidden lg:flex lg:flex-shrink-0">
            <div class="flex flex-col w-64 bg-blue-800">
                <div class="flex items-center justify-between h-16 px-6 bg-blue-900">
                    <h1 class="text-xl font-semibold text-white">Sistem Koperasi</h1>
                </div>
                
                <nav class="mt-6 px-6 flex-1">
                    @if(auth()->user()->isAdmin())
                        <div class="mb-4">
                            <h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Admin</h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Dashboard</a>
                                <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Manajemen User</a>
                                <a href="{{ route('admin.reports') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Laporan</a>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->isKasir())
                        <div class="mb-4">
                            <h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Kasir</h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('kasir.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Dashboard</a>
                                <a href="{{ route('kasir.pos') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Point of Sale</a>
                                <a href="{{ route('kasir.transactions') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Transaksi</a>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->isGudang())
                        <div class="mb-4">
                            <h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Gudang</h3>
                            <div class="mt-2 space-y-1">
                                <a href="{{ route('gudang.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Dashboard</a>
                                <a href="{{ route('gudang.products') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Produk</a>
                                <a href="{{ route('gudang.categories') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Kategori</a>
                                <a href="{{ route('gudang.reports.stock') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Laporan Stok</a>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-blue-700 mt-auto">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
                                Logout
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <div class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between h-16 px-6">
                    <div class="flex items-center">
                        <h2 class="text-lg font-medium text-gray-900">@yield('title', 'Sistem Koperasi')</h2>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ auth()->user()->role->display_name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
