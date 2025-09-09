<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Sistem Koperasi')</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<style>
		#sidebar.collapsed .sidebar-label { display: none; }
		#sidebar.collapsed .section-title { display: none; }
		#sidebar.collapsed nav .space-y-1 > a { padding-left: 0.75rem; padding-right: 0.75rem; }
		#sidebar.collapsed nav a { justify-content: center; gap: 0; }
		#sidebar.collapsed #sidebarCollapseToggle svg { transform: rotate(180deg); }
	</style>
</head>
<body class="bg-gray-100">
	<div class="flex h-screen">
		<!-- Sidebar - Always visible on desktop -->
		<div id="sidebar" class="hidden lg:flex lg:flex-shrink-0">
			<div id="sidebarInner" class="flex flex-col w-64 bg-blue-800 transition-all duration-200 ease-in-out">
				<div class="flex items-center justify-between h-16 px-6 bg-blue-900">
					<h1 class="text-xl font-semibold text-white sidebar-label">Sistem Koperasi</h1>
					<button id="sidebarCollapseToggle" class="hidden lg:inline-flex items-center justify-center p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-700" aria-label="Collapse sidebar">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m6 6-6-6 6-6" />
						</svg>
					</button>
				</div>
				
				<nav class="mt-6 px-6 flex-1">
					@if(auth()->check() && auth()->user()->isAdmin())
						<div class="mb-4">
							<h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider section-title">Admin</h3>
							<div class="mt-2 space-y-1">
								<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M15 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
									</span>
									<span class="sidebar-label">Manajemen User</span>
								</a>
								<a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8m-6 8h6M9 5h12" /></svg>
									</span>
									<span class="sidebar-label">Laporan</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isKasir())
						<div class="mb-4">
							<h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider section-title">Kasir</h3>
							<div class="mt-2 space-y-1">
								<a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('kasir.pos') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 2 2m-2-2v6m7-6h2a2 2 0 012 2v4a2 2 0 01-2 2h-2M7 10H5a2 2 0 00-2 2v4a2 2 0 002 2h2" /></svg>
									</span>
									<span class="sidebar-label">Point of Sale</span>
								</a>
								<a href="{{ route('kasir.transactions') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 7h14" /></svg>
									</span>
									<span class="sidebar-label">Transaksi</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isGudang())
						<div class="mb-4">
							<h3 class="text-xs font-semibold text-blue-300 uppercase tracking-wider section-title">Gudang</h3>
							<div class="mt-2 space-y-1">
								<a href="{{ route('gudang.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('gudang.products') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v6" /></svg>
									</span>
									<span class="sidebar-label">Produk</span>
								</a>
								<a href="{{ route('gudang.categories') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16" /></svg>
									</span>
									<span class="sidebar-label">Kategori</span>
								</a>
								<a href="{{ route('gudang.reports.stock') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3a1 1 0 00-1 1v2H6a2 2 0 00-2 2v2h16V8a2 2 0 00-2-2h-4V4a1 1 0 00-1-1h-2zM4 15h16M10 19h4" /></svg>
									</span>
									<span class="sidebar-label">Laporan Stok</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check())
						<div class="pt-4 border-t border-blue-700 mt-auto">
							<form method="POST" action="{{ route('logout') }}" class="js-confirm"
									data-title="Logout?" data-text="Anda akan keluar dari sistem." data-icon="question" data-confirm="Ya, logout">
								@csrf
								<button type="submit" class="flex items-center gap-3 w-full text-left px-3 py-2 rounded-md text-sm font-medium text-blue-100 hover:text-white hover:bg-blue-700">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" /></svg>
									</span>
									<span class="sidebar-label">Logout</span>
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
					<div class="flex items-center space-x-3">
						<button id="sidebarToggle" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 lg:hidden" aria-label="Toggle sidebar">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
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
			// Sidebar toggle (mobile/tablet)
			const sidebar = document.getElementById('sidebar');
			const sidebarInner = document.getElementById('sidebarInner');
			const toggle = document.getElementById('sidebarToggle');
			const collapseToggle = document.getElementById('sidebarCollapseToggle');
			if (toggle && sidebar) {
				toggle.addEventListener('click', function(e) {
					e.preventDefault();
					if (sidebar.classList.contains('hidden')) {
						sidebar.classList.remove('hidden');
						sidebar.classList.add('fixed','inset-0','z-40','flex');
					} else {
						sidebar.classList.add('hidden');
						sidebar.classList.remove('fixed','inset-0','z-40','flex');
					}
				});
				// Click outside to close on small screens
				document.addEventListener('click', function(e) {
					if (window.innerWidth >= 1024) return; // match lg breakpoint
					if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
						sidebar.classList.add('hidden');
						sidebar.classList.remove('fixed','inset-0','z-40','flex');
					}
				});
			}

			// Desktop collapse control with persistence
			function applySidebarCollapsed(collapsed) {
				if (!sidebarInner) return;
				if (collapsed) {
					sidebar.classList.add('collapsed');
					sidebarInner.classList.remove('w-64');
					sidebarInner.classList.add('w-20');
				} else {
					sidebar.classList.remove('collapsed');
					sidebarInner.classList.remove('w-20');
					sidebarInner.classList.add('w-64');
				}
			}

			const persisted = localStorage.getItem('sidebarCollapsed');
			applySidebarCollapsed(persisted === '1');
			if (collapseToggle) {
				collapseToggle.addEventListener('click', function(e) {
					e.preventDefault();
					const currently = sidebar.classList.contains('collapsed');
					applySidebarCollapsed(!currently);
					localStorage.setItem('sidebarCollapsed', !currently ? '1' : '0');
				});
			}
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

			// Global confirm for all forms that perform DELETE (method spoofing)
			document.querySelectorAll('form').forEach(function(form) {
				const methodInput = form.querySelector('input[name="_method"][value="DELETE"]');
				if (!methodInput) return;
				if (form.classList.contains('js-confirm')) return; // already handled above
				form.addEventListener('submit', function(e) {
					e.preventDefault();
					Swal.fire({
						title: form.getAttribute('data-title') || 'Hapus data?',
						text: form.getAttribute('data-text') || 'Tindakan ini tidak dapat dibatalkan.',
						icon: form.getAttribute('data-icon') || 'warning',
						showCancelButton: true,
						confirmButtonText: form.getAttribute('data-confirm') || 'Ya, hapus',
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

	@stack('scripts')
</body>
</html>
