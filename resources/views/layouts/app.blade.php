<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', $storeSettings['name'] ?? 'Sistem Koperasi')</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
	<!-- Chart.js -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		body { font-family: 'Inter', sans-serif; }
		#sidebar.collapsed .sidebar-label { display: none; }
		#sidebar.collapsed .section-title { display: none; }
		#sidebar.collapsed nav .space-y-1 > a { padding-left: 0.75rem; padding-right: 0.75rem; }
		#sidebar.collapsed nav a { justify-content: center; gap: 0; }
		#sidebar.collapsed #sidebarCollapseToggle svg { transform: rotate(180deg); }
		
		/* Enhanced scrollbar */
		::-webkit-scrollbar { width: 8px; }
		::-webkit-scrollbar-track { background: #f8fafc; border-radius: 4px; }
		::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #cbd5e1, #94a3b8); border-radius: 4px; }
		::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #94a3b8, #64748b); }
		
		/* Smooth transitions with better performance */
		* { 
			transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
		}
		
		/* Enhanced glass effect for mobile sidebar */
		.mobile-sidebar {
			backdrop-filter: blur(20px);
			background: rgba(15, 23, 42, 0.95);
			border-right: 1px solid rgba(255, 255, 255, 0.1);
		}
		
		/* Enhanced card hover effects */
		.card-hover {
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		}
		.card-hover:hover {
			transform: translateY(-4px);
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
		}
		
		/* Modern gradient backgrounds */
		.gradient-primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		}
		.gradient-secondary {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
		}
		.gradient-success {
			background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
		}
		.gradient-warning {
			background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
		}
		
		/* Enhanced animations */
		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
		
		@keyframes slideInRight {
			from {
				opacity: 0;
				transform: translateX(30px);
			}
			to {
				opacity: 1;
				transform: translateX(0);
			}
		}
		
		.animate-fade-in-up {
			animation: fadeInUp 0.6s ease-out;
		}
		
		.animate-slide-in-right {
			animation: slideInRight 0.5s ease-out;
		}
		
		/* Modern focus styles */
		.focus-ring {
			transition: all 0.2s ease;
		}
		.focus-ring:focus {
			outline: none;
			ring: 2px;
			ring-color: #3b82f6;
			ring-offset: 2px;
		}
		
		/* Enhanced button styles */
		.btn-modern {
			position: relative;
			overflow: hidden;
			transition: all 0.3s ease;
		}
		.btn-modern::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
			transition: left 0.5s;
		}
		.btn-modern:hover::before {
			left: 100%;
		}
		
		/* User dropdown z-index fix */
		.user-dropdown {
			z-index: 99999 !important;
			position: relative;
		}
		
		.user-dropdown .dropdown-card {
			z-index: 99999 !important;
			position: absolute;
		}
		
		/* Ensure dropdown is above everything */
		[x-data] .dropdown-card {
			z-index: 99999 !important;
		}
		
		/* Force dropdown to be on top */
		.user-dropdown .dropdown-card {
			z-index: 99999 !important;
			position: absolute !important;
			top: 100% !important;
			right: 0 !important;
			margin-top: 0.5rem !important;
		}
		
		/* Override any conflicting styles */
		.user-dropdown .dropdown-card * {
			position: relative !important;
		}
		
		/* Ensure dropdown is always visible */
		.user-dropdown .dropdown-card {
			z-index: 999999 !important;
			position: absolute !important;
			right: 0 !important;
			top: 100% !important;
			margin-top: 0.5rem !important;
			background: white !important;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
		}
		
		/* Force all content to be above dropdown */
		.user-dropdown .dropdown-card::before {
			content: '';
			position: absolute;
			top: -10px;
			right: 0;
			width: 100%;
			height: 10px;
			background: transparent;
			z-index: 999999;
		}
		
		/* Ultimate z-index fix */
		.user-dropdown {
			z-index: 999999 !important;
			position: relative !important;
		}
		
		.user-dropdown .dropdown-card {
			z-index: 999999 !important;
			position: absolute !important;
			right: 0 !important;
			top: 100% !important;
			margin-top: 0.5rem !important;
		}
		
		/* Force dropdown to be above everything */
		.user-dropdown .dropdown-card {
			z-index: 999999 !important;
			position: absolute !important;
			right: 0 !important;
			top: 100% !important;
			margin-top: 0.5rem !important;
			background: white !important;
			border: 1px solid #e5e7eb !important;
			border-radius: 0.75rem !important;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
		}
		
		/* Ultimate fix for dropdown positioning */
		.user-dropdown {
			z-index: 999999 !important;
			position: relative !important;
		}
		
		.user-dropdown .dropdown-card {
			z-index: 999999 !important;
			position: absolute !important;
			right: 0 !important;
			top: 100% !important;
			margin-top: 0.5rem !important;
			background: white !important;
			border: 1px solid #e5e7eb !important;
			border-radius: 0.75rem !important;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
			transform: none !important;
		}
		
		/* Force all other elements to have lower z-index */
		.flex-1 {
			z-index: 1 !important;
		}
		
		.bg-white {
			z-index: 1 !important;
		}
		
		/* Loading Overlay Styles */
		.loading-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.3);
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 9999999;
			backdrop-filter: blur(2px);
		}
		
		.loading-spinner {
			width: 40px;
			height: 40px;
			border: 3px solid rgba(255, 255, 255, 0.3);
			border-top: 3px solid #3b82f6;
			border-radius: 50%;
			animation: spin 0.8s linear infinite;
		}
		
		.loading-text {
			color: white;
			font-size: 14px;
			font-weight: 500;
			margin-top: 15px;
			text-align: center;
		}
		
		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
		
		/* Button loading state */
		.btn-loading {
			position: relative;
			pointer-events: none;
			opacity: 0.7;
		}
		
		.btn-loading::after {
			content: '';
			position: absolute;
			top: 50%;
			left: 50%;
			width: 20px;
			height: 20px;
			margin: -10px 0 0 -10px;
			border: 2px solid transparent;
			border-top: 2px solid currentColor;
			border-radius: 50%;
			animation: spin 1s linear infinite;
		}
		
		/* Page transition loading */
		.page-loading {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 4px;
			background: linear-gradient(90deg, #3b82f6, #1d4ed8, #3b82f6);
			background-size: 200% 100%;
			animation: loading-bar 2s ease-in-out infinite;
			z-index: 9999998;
		}
		
		@keyframes loading-bar {
			0% { background-position: 200% 0; }
			100% { background-position: -200% 0; }
		}
		
		/* Ensure dropdown is always on top */
		.user-dropdown .dropdown-card {
			z-index: 999999 !important;
			position: absolute !important;
			right: 0 !important;
			top: 100% !important;
			margin-top: 0.5rem !important;
			background: white !important;
			border: 1px solid #e5e7eb !important;
			border-radius: 0.75rem !important;
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
			transform: none !important;
		}
		
		/* Dark Mode Styles */
		.dark {
			color-scheme: dark;
		}

		.dark .bg-white {
			background-color: #1f2937 !important;
		}

		.dark .text-gray-900 {
			color: #f9fafb !important;
		}

		.dark .text-gray-700 {
			color: #d1d5db !important;
		}

		.dark .text-gray-500 {
			color: #9ca3af !important;
		}

		.dark .border-gray-300 {
			border-color: #4b5563 !important;
		}

		.dark .bg-gray-50 {
			background-color: #374151 !important;
		}

		.dark .bg-gray-100 {
			background-color: #4b5563 !important;
		}

		.dark .shadow {
			box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2) !important;
		}

		.dark .shadow-lg {
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
		}

		.dark .shadow-xl {
			box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
		}

		.dark .shadow-2xl {
			box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
		}
	</style>
</head>
<body class="bg-gray-50 min-h-screen">
	<div class="flex h-screen overflow-hidden">
		<!-- Mobile Sidebar Overlay -->
		<div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden lg:hidden" onclick="toggleSidebar()"></div>
		
		<!-- Sidebar - Always visible on desktop, overlay on mobile -->
		<div id="sidebar" class="hidden lg:flex lg:flex-shrink-0 z-50">
			<div id="sidebarInner" class="flex flex-col w-64 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 transition-all duration-300 ease-in-out shadow-2xl border-r border-slate-700/50">
				<div class="flex items-center justify-between h-16 px-6 bg-gradient-to-r from-slate-900 to-slate-800 border-b border-slate-700/50">
					<div class="flex items-center space-x-3">
						<div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
							<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
							</svg>
						</div>
						<h1 class="text-xl font-bold text-white sidebar-label tracking-tight">{{ $storeSettings['name'] ?? 'Sistem Koperasi' }}</h1>
					</div>
				</div>
				
				<nav class="mt-6 px-4 flex-1 overflow-y-auto">
					@if(auth()->check() && auth()->user()->isAdmin())
						<div class="mb-6">
							<h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider section-title mb-4 px-2 flex items-center">
								<div class="w-2 h-2 bg-blue-400 rounded-full mr-2"></div>
								Admin
							</h3>
							<div class="space-y-1">
								<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M15 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
									</span>
									<span class="sidebar-label">Manajemen User</span>
								</a>
								<a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h8m-6 8h6M9 5h12" /></svg>
									</span>
									<span class="sidebar-label">Laporan</span>
								</a>
								<a href="{{ route('admin.market-analysis.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
									</span>
									<span class="sidebar-label">Analisis Pasar</span>
								</a>
								<a href="{{ route('admin.smart-inventory.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group border border-transparent hover:border-green-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
									</span>
									<span class="sidebar-label">Smart Inventory</span>
								</a>
								<a href="{{ route('admin.customer-intelligence.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-500/20 hover:to-pink-500/20 transition-all duration-300 group border border-transparent hover:border-purple-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" /></svg>
									</span>
									<span class="sidebar-label">Customer Intelligence</span>
								</a>
								<a href="{{ route('admin.predictive-analytics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-indigo-500/20 hover:to-purple-500/20 transition-all duration-300 group border border-transparent hover:border-indigo-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
									</span>
									<span class="sidebar-label">Predictive Analytics</span>
								</a>
                <a href="{{ route('admin.automated-reporting.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-red-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
                    <span class="inline-flex w-5 h-5 items-center justify-center">
                        <svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </span>
                    <span class="sidebar-label">Automated Reporting</span>
                </a>
                <a href="{{ route('admin.ai-chatbot.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-cyan-500/20 hover:to-blue-500/20 transition-all duration-300 group border border-transparent hover:border-cyan-500/30">
                    <span class="inline-flex w-5 h-5 items-center justify-center">
                        <svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </span>
                    <span class="sidebar-label">AI Chatbot</span>
                </a>
								<a href="{{ route('admin.activity-logs') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
									</span>
									<span class="sidebar-label">Log Aktivitas</span>
								</a>
								<a href="{{ route('admin.store-settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
									</span>
									<span class="sidebar-label">Pengaturan Toko</span>
								</a>
								<a href="{{ route('admin.app-settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" /></svg>
									</span>
									<span class="sidebar-label">Pengaturan Aplikasi</span>
								</a>
								<a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-blue-500/20 hover:to-indigo-500/20 transition-all duration-300 group border border-transparent hover:border-blue-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H7l-4 4V10a2 2 0 012-2h2"/></svg>
									</span>
									<span class="sidebar-label">Group Chat</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isKasir())
						<div class="mb-6">
							<h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider section-title mb-4 px-2 flex items-center">
								<div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
								Kasir
							</h3>
							<div class="space-y-1">
								<a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group border border-transparent hover:border-green-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('kasir.pos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group border border-transparent hover:border-green-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 2 2m-2-2v6m7-6h2a2 2 0 012 2v4a2 2 0 01-2 2h-2M7 10H5a2 2 0 00-2 2v4a2 2 0 002 2h2" /></svg>
									</span>
									<span class="sidebar-label">Point of Sale</span>
								</a>
								<a href="{{ route('kasir.transactions') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group border border-transparent hover:border-green-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h10M5 7h14" /></svg>
									</span>
									<span class="sidebar-label">Transaksi</span>
								</a>
								<a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-green-500/20 hover:to-emerald-500/20 transition-all duration-300 group border border-transparent hover:border-green-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H7l-4 4V10a2 2 0 012-2h2"/></svg>
									</span>
									<span class="sidebar-label">Group Chat</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check() && auth()->user()->isGudang())
						<div class="mb-6">
							<h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider section-title mb-4 px-2 flex items-center">
								<div class="w-2 h-2 bg-orange-400 rounded-full mr-2"></div>
								Gudang
							</h3>
							<div class="space-y-1">
								<a href="{{ route('gudang.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-amber-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7" /></svg>
									</span>
									<span class="sidebar-label">Dashboard</span>
								</a>
								<a href="{{ route('gudang.products') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-amber-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v6" /></svg>
									</span>
									<span class="sidebar-label">Produk</span>
								</a>
								<a href="{{ route('gudang.categories') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-amber-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16" /></svg>
									</span>
									<span class="sidebar-label">Kategori</span>
								</a>
								<a href="{{ route('gudang.reports.stock') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-amber-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3a1 1 0 00-1 1v2H6a2 2 0 00-2 2v2h16V8a2 2 0 00-2-2h-4V4a1 1 0 00-1-1h-2zM4 15h16M10 19h4" /></svg>
									</span>
									<span class="sidebar-label">Laporan Stok</span>
								</a>
								<a href="{{ route('chat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-amber-500/20 transition-all duration-300 group border border-transparent hover:border-orange-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H7l-4 4V10a2 2 0 012-2h2"/></svg>
									</span>
									<span class="sidebar-label">Group Chat</span>
								</a>
							</div>
						</div>
					@endif

					@if(auth()->check())
						<div class="pt-6 border-t border-slate-700/50 mt-auto">
							<!-- Profile Settings -->
							<a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-purple-500/20 hover:to-pink-500/20 transition-all duration-300 group mb-3 border border-transparent hover:border-purple-500/30">
								<span class="inline-flex w-5 h-5 items-center justify-center">
									<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
								</span>
								<span class="sidebar-label">Edit Profil</span>
							</a>
							
							<!-- Logout -->
							<form method="POST" action="{{ route('logout') }}" class="js-confirm"
									data-title="Logout?" data-text="Anda akan keluar dari sistem." data-icon="question" data-confirm="Ya, logout">
								@csrf
								<button type="submit" class="flex items-center gap-3 w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-slate-300 hover:text-white hover:bg-gradient-to-r hover:from-red-500/20 hover:to-rose-500/20 transition-all duration-300 group border border-transparent hover:border-red-500/30">
									<span class="inline-flex w-5 h-5 items-center justify-center">
										<svg class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" /></svg>
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
		<div class="flex-1 flex flex-col overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
			<!-- Enhanced Top bar -->
			<div class="bg-white/80 backdrop-blur-lg shadow-lg border-b border-gray-200/50 relative" style="z-index: 999999 !important;">
				<div class="flex items-center justify-between h-16 px-4 sm:px-6">
					<div class="flex items-center space-x-4">
						<!-- Enhanced Toggle Button -->
						<button id="sidebarToggle" class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-600 hover:text-gray-900 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 shadow-sm hover:shadow-md" aria-label="Toggle sidebar">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
						
						<div class="flex items-center space-x-3">
							<div class="w-1 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
							<div>
								<h2 class="text-lg font-bold text-gray-900 tracking-tight">@yield('title', $storeSettings['name'] ?? 'Sistem Koperasi')</h2>
								<p class="text-sm text-gray-600 hidden sm:block">@yield('subtitle', '')</p>
							</div>
						</div>
					</div>
					
					<div class="flex items-center space-x-3 relative" style="z-index: 999999 !important;">
						@if(auth()->check())
							<!-- Desktop User Card -->
							<div class="hidden sm:block relative user-dropdown" x-data="{ open: false }" style="z-index: 999999 !important;">
								<button @click="open = !open" class="flex items-center space-x-3 group hover:bg-gray-50 rounded-lg p-2 -m-2 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
									<div class="text-right">
										<p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors duration-200">{{ auth()->user()->name }}</p>
										<p class="text-xs text-gray-500 group-hover:text-blue-500 transition-colors duration-200">{{ auth()->user()->role->display_name }}</p>
									</div>
									<div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center group-hover:shadow-lg transition-shadow duration-200">
										<span class="text-xs font-semibold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
									</div>
									<svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
									</svg>
								</button>

								<!-- Dropdown Card -->
								<div x-show="open" 
									 @click.away="open = false"
									 x-transition:enter="transition ease-out duration-200"
									 x-transition:enter-start="opacity-0 scale-95"
									 x-transition:enter-end="opacity-100 scale-100"
									 x-transition:leave="transition ease-in duration-150"
									 x-transition:leave-start="opacity-100 scale-100"
									 x-transition:leave-end="opacity-0 scale-95"
									 class="dropdown-card absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden"
									 style="z-index: 999999 !important; position: absolute !important; right: 0 !important; top: 100% !important; margin-top: 0.5rem !important; background: white !important; border: 1px solid #e5e7eb !important; border-radius: 0.75rem !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; transform: none !important;">
									
									<!-- Card Header -->
									<div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
										<div class="flex items-center space-x-3">
											<div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
												<span class="text-lg font-semibold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
											</div>
											<div>
												<h3 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
												<p class="text-sm text-gray-600">{{ auth()->user()->role->display_name }}</p>
												<p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
											</div>
										</div>
									</div>

									<!-- Card Body -->
									<div class="px-6 py-4 space-y-3">
										<div class="flex items-center justify-between py-2">
											<div class="flex items-center space-x-3">
												<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
												</svg>
												<span class="text-sm text-gray-700">Edit Profil</span>
											</div>
											<a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
												Kelola →
											</a>
										</div>
										
										<div class="border-t border-gray-200 pt-3">
											<div class="flex items-center justify-between">
												<span class="text-xs text-gray-500">Terakhir login</span>
												<span class="text-xs text-gray-700">{{ now()->format('d M Y, H:i') }}</span>
											</div>
										</div>
									</div>

									<!-- Card Footer -->
									<div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
										<form method="POST" action="{{ route('logout') }}" class="js-confirm"
												data-title="Logout?" data-text="Anda akan keluar dari sistem." data-icon="question" data-confirm="Ya, logout">
											@csrf
											<button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
												</svg>
												<span>Logout</span>
											</button>
										</form>
									</div>
								</div>
							</div>

							<!-- Mobile User Info -->
							<div class="sm:hidden">
								<div class="flex items-center space-x-2">
									<span class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</span>
									<a href="{{ route('profile.edit') }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200">
										{{ auth()->user()->role->display_name }}
									</a>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>

			<!-- Page content -->
			<main class="flex-1 overflow-y-auto p-4 sm:p-6">
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
			// Sidebar elements
			const sidebar = document.getElementById('sidebar');
			const sidebarInner = document.getElementById('sidebarInner');
			const sidebarOverlay = document.getElementById('sidebarOverlay');
			const toggle = document.getElementById('sidebarToggle');
			
			// Universal sidebar toggle function
			window.toggleSidebar = function() {
				if (!sidebar) return;
				
				const isMobile = window.innerWidth < 1024;
				
				if (isMobile) {
					// Mobile behavior - overlay sidebar
					if (sidebar.classList.contains('hidden')) {
						sidebar.classList.remove('hidden');
						sidebar.classList.add('fixed', 'inset-0', 'z-50', 'flex', 'mobile-sidebar');
						sidebarOverlay.classList.remove('hidden');
						document.body.classList.add('overflow-hidden');
					} else {
						sidebar.classList.add('hidden');
						sidebar.classList.remove('fixed', 'inset-0', 'z-50', 'flex', 'mobile-sidebar');
						sidebarOverlay.classList.add('hidden');
						document.body.classList.remove('overflow-hidden');
					}
				} else {
					// Desktop behavior - collapse/expand sidebar
					const isCollapsed = sidebar.classList.contains('collapsed');
					applySidebarCollapsed(!isCollapsed);
					localStorage.setItem('sidebarCollapsed', !isCollapsed ? '1' : '0');
				}
			};
			
			// Toggle button event listener
			if (toggle && sidebar) {
				toggle.addEventListener('click', function(e) {
					e.preventDefault();
					window.toggleSidebar();
				});
			}
			
			// Close sidebar on overlay click (mobile only)
			if (sidebarOverlay) {
				sidebarOverlay.addEventListener('click', function() {
					if (window.innerWidth < 1024) {
						window.toggleSidebar();
					}
				});
			}
			
			// Close sidebar on escape key (mobile only)
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && !sidebar.classList.contains('hidden') && window.innerWidth < 1024) {
					window.toggleSidebar();
				}
			});

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

			// Initialize sidebar state
			const persisted = localStorage.getItem('sidebarCollapsed');
			applySidebarCollapsed(persisted === '1');
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

		// Global Chat Notifications (simple polling)
		@if(auth()->check())
		(function setupChatNotifications(){
			let lastNotifiedId = parseInt(localStorage.getItem('chatLastId') || '0', 10);
			let initialized = false;
			function playBeep(){
				try {
					const ctx = new (window.AudioContext || window.webkitAudioContext)();
					const o = ctx.createOscillator();
					const g = ctx.createGain();
					o.type = 'sine';
					o.frequency.value = 880; // A5
					o.connect(g); g.connect(ctx.destination);
					g.gain.setValueAtTime(0.001, ctx.currentTime);
					g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.01);
					g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
					o.start(); o.stop(ctx.currentTime + 0.26);
				} catch (e) { /* ignore */ }
			}
			async function pollNotifications(){
				try {
					const url = `{{ route('chat.latest') }}` + (lastNotifiedId ? `?since=${lastNotifiedId}` : '');
					const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
					if (!res.ok) return;
					const data = await res.json();
					const list = Array.isArray(data.messages) ? data.messages : [];
					if (!initialized) {
						// On first run, just set the pointer without notifying for historical messages
						if (list.length) lastNotifiedId = Math.max(...list.map(m => m.id));
						initialized = true;
						localStorage.setItem('chatLastId', String(lastNotifiedId));
						return;
					}
					if (list.length === 0) return;
					// Suppress toast when already on chat page
					const onChatPage = window.location.pathname.startsWith('/chat');
					list.forEach(m => {
						lastNotifiedId = Math.max(lastNotifiedId, m.id);
						if (onChatPage) return;
						const text = (m.content && m.content.trim()) ? m.content.trim().slice(0, 80) : (m.attachment_type ? 'Mengirim lampiran' : 'Pesan baru');
						Toast.fire({
							icon: 'info',
							title: `${m.user}`,
							text,
							position: 'top-end',
							showConfirmButton: false,
							timer: 2500,
							willOpen: playBeep,
						}).then(() => {});
					});
					localStorage.setItem('chatLastId', String(lastNotifiedId));
				} catch (e) { /* ignore */ }
			}
			setInterval(pollNotifications, 5000);
		})();
		@endif
	</script>

@php($viteManifest = public_path('build/manifest.json'))
@if(file_exists($viteManifest))
    @vite(['resources/js/bootstrap.js'])
@endif

	@stack('scripts')

	<!-- Global Loading Overlay -->
	<div id="globalLoading" class="loading-overlay" style="display: none;">
		<div class="text-center">
			<div class="loading-spinner"></div>
			<div class="loading-text" id="loadingText">Memuat...</div>
		</div>
	</div>

	<!-- Page Loading Bar -->
	<div id="pageLoading" class="page-loading" style="display: none;"></div>

	<script>
		// Global Loading Management
		window.LoadingManager = {
			enabled: {{ \App\Models\AppSetting::getValue('lazy_loading', true) ? 'true' : 'false' }},
			
			show: function(message = 'Loading...') {
				if (!this.enabled) return;
				document.getElementById('loadingText').textContent = message;
				document.getElementById('globalLoading').style.display = 'flex';
			},
			hide: function() {
				if (!this.enabled) return;
				document.getElementById('globalLoading').style.display = 'none';
			},
			showPageLoading: function() {
				if (!this.enabled) return;
				document.getElementById('pageLoading').style.display = 'block';
			},
			hidePageLoading: function() {
				if (!this.enabled) return;
				document.getElementById('pageLoading').style.display = 'none';
			},
			updateSetting: function(enabled) {
				this.enabled = enabled;
			}
		};

		// Intercept form submissions
		document.addEventListener('submit', function(e) {
			if (e.target.tagName === 'FORM') {
				LoadingManager.show('Saving...');
			}
		});

		// Intercept link clicks
		document.addEventListener('click', function(e) {
			const link = e.target.closest('a');
			if (link && link.href && !link.href.includes('#') && !link.target) {
				LoadingManager.show('Loading...');
			}
		});

		// Handle page load completion
		window.addEventListener('load', function() {
			LoadingManager.hide();
			LoadingManager.hidePageLoading();
		});

		// Handle AJAX requests
		const originalFetch = window.fetch;
		window.fetch = function(...args) {
			LoadingManager.show('Loading...');
			return originalFetch.apply(this, args)
				.finally(() => LoadingManager.hide());
		};

		// Handle browser back/forward
		window.addEventListener('popstate', function() {
			LoadingManager.show('Loading...');
		});

		// Initialize Dark Mode
		document.addEventListener('DOMContentLoaded', function() {
			// Check for saved dark mode preference
			const savedDarkMode = localStorage.getItem('darkMode');
			const darkModeSetting = {{ \App\Models\AppSetting::getValue('dark_mode', false) ? 'true' : 'false' }};
			
			if (savedDarkMode === 'true' || darkModeSetting) {
				document.documentElement.classList.add('dark');
			}
		});
	</script>
</body>
</html>
