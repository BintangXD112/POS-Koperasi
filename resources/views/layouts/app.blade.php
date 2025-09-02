<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Sistem Koperasi')</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
					@if(auth()->check() && auth()->user()->isAdmin())
						<div class="mb-4">
							<h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Admin</h3>
							<div class="mt-2 space-y-1">
								<a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Dashboard</a>
								<a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Manajemen User</a>
								<a href="{{ route('admin.reports') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Laporan</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isKasir())
						<div class="mb-4">
							<h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider">Kasir</h3>
							<div class="mt-2 space-y-1">
								<a href="{{ route('kasir.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Dashboard</a>
								<a href="{{ route('kasir.pos') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Point of Sale</a>
								<a href="{{ route('kasir.transactions') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">Transaksi</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isGudang())
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

					@if(auth()->check())
						<div class="pt-4 border-t border-blue-700 mt-auto">
							<form method="POST" action="{{ route('logout') }}" class="js-confirm"
									data-title="Logout?" data-text="Anda akan keluar dari sistem." data-icon="question" data-confirm="Ya, logout">
								@csrf
								<button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									Logout
								</button>
							</form>
						</div>
					@endif
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
						@if(auth()->check())
							<span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
								{{ auth()->user()->role->display_name }}
							</span>
						@endif
					</div>
				</div>
			</div>

			<!-- Page content -->
			<main class="flex-1 overflow-y-auto p-6">
				@yield('content')
			</main>
		</div>
	</div>

	<script>
		// SweetAlert toast preset
		const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 2000,
			timerProgressBar: true,
		});

		// Flash messages → SweetAlert2
		@if (session('success'))
			Toast.fire({ icon: 'success', title: @json(session('success')) });
		@endif
		@if (session('info'))
			Toast.fire({ icon: 'info', title: @json(session('info')) });
		@endif
		@if (session('error'))
			Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')) });
		@endif
		@if ($errors->any())
			Swal.fire({
				icon: 'error',
				title: 'Validasi Gagal',
				html: `{!! implode('<br/>', $errors->all()) !!}`,
			});
		@endif

		// Global confirm handler for forms with .js-confirm
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('form.js-confirm').forEach(function(form) {
				form.addEventListener('submit', function(e) {
					e.preventDefault();
					const title = form.getAttribute('data-title') || 'Apakah Anda yakin?';
					const text = form.getAttribute('data-text') || 'Tindakan ini tidak dapat dibatalkan.';
					const icon = form.getAttribute('data-icon') || 'warning';
					const confirmText = form.getAttribute('data-confirm') || 'Ya, lanjut';
					Swal.fire({
						title, text, icon,
						showCancelButton: true,
						confirmButtonText: confirmText,
						cancelButtonText: 'Batal'
					}).then((result) => {
						if (result.isConfirmed) {
							form.submit();
						}
					});
				});
			});

			// Helper: AJAX action with confirmation (for links/buttons)
			const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
			document.querySelectorAll('.js-ajax-confirm').forEach(function(el) {
				el.addEventListener('click', async function(e) {
					e.preventDefault();
					const url = el.getAttribute('data-url') || el.getAttribute('href');
					const method = (el.getAttribute('data-method') || 'POST').toUpperCase();
					const title = el.getAttribute('data-title') || 'Lanjutkan aksi?';
					const text = el.getAttribute('data-text') || '';
					const icon = el.getAttribute('data-icon') || 'warning';
					const success = el.getAttribute('data-success') || 'Berhasil';
					const fail = el.getAttribute('data-fail') || 'Gagal';
					const redirect = el.getAttribute('data-redirect') || '';

					const ok = await Swal.fire({ title, text, icon, showCancelButton: true, confirmButtonText: 'Ya, lanjut', cancelButtonText: 'Batal' });
					if (!ok.isConfirmed) return;

					try {
						const res = await fetch(url, {
							method,
							headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
							body: method === 'GET' ? undefined : (el.getAttribute('data-body') || undefined),
						});
						if (res.ok) {
							Toast.fire({ icon: 'success', title: success });
							if (redirect) { setTimeout(() => { window.location.href = redirect; }, 600); }
						} else {
							const data = await res.json().catch(() => ({}));
							Swal.fire({ icon: 'error', title: fail, text: data.message || 'Terjadi kesalahan' });
						}
					} catch (err) {
						Swal.fire({ icon: 'error', title: fail, text: err?.message || 'Terjadi kesalahan jaringan' });
					}
				});
			});
		});
	</script>
</body>
</html>
