@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pengaturan Aplikasi</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola pengaturan sistem aplikasi koperasi</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.app-settings.update') }}" class="space-y-6">
        @csrf
        
        <!-- Performance Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Performa</h3>
                
                <!-- Lazy Loading Toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Lazy Loading</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Performance
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Menampilkan loading indicator saat proses loading untuk meningkatkan pengalaman pengguna
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="lazy_loading" value="1" class="sr-only peer" {{ $lazyLoading ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Theme Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Tema</h3>
                
                <!-- Dark Mode Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Dark Mode</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                Theme
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Mengaktifkan mode gelap untuk pengalaman visual yang lebih nyaman
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="dark_mode" value="1" class="sr-only peer" {{ $darkMode ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Notifikasi</h3>
                
                <!-- Email Notifications -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Email
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Mengirim notifikasi email untuk transaksi dan update sistem
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1" class="sr-only peer" {{ $emailNotifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Browser Notifications -->
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Browser Notifications</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Browser
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Menampilkan notifikasi push di browser untuk update real-time
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="browser_notifications" value="1" class="sr-only peer" {{ $browserNotifications ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Export Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Auto Export</h3>
                
                <!-- Auto Export Toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Auto Export</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                Export
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Mengaktifkan export otomatis laporan sesuai jadwal yang ditentukan
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_export_enabled" value="1" class="sr-only peer" {{ $autoExportEnabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Export Schedule -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Export</label>
                        <select name="auto_export_schedule" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="daily" {{ $autoExportSchedule === 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ $autoExportSchedule === 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ $autoExportSchedule === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                        <select name="auto_export_format" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="excel" {{ $autoExportFormat === 'excel' ? 'selected' : '' }}>Excel</option>
                            <option value="pdf" {{ $autoExportFormat === 'pdf' ? 'selected' : '' }}>PDF</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Cleanup Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Data Cleanup</h3>
                
                <!-- Data Cleanup Toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <h4 class="text-sm font-medium text-gray-900">Auto Cleanup</h4>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Cleanup
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Mengaktifkan pembersihan otomatis data lama untuk menghemat ruang penyimpanan
                        </p>
                    </div>
                    <div class="ml-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="data_cleanup_enabled" value="1" class="sr-only peer" {{ $dataCleanupEnabled ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Data Retention Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retensi Data (Hari)</label>
                    <input type="number" name="data_retention_days" value="{{ $dataRetentionDays }}" 
                           min="30" max="3650" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500">
                    <p class="mt-1 text-sm text-gray-500">Data akan dihapus setelah periode ini (30-3650 hari)</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Semua Pengaturan
            </button>
        </div>
    </form>

    <!-- System Information -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informasi Sistem</h3>
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Laravel Version</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Database</dt>
                    <dd class="mt-1 text-sm text-gray-900">SQLite</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Environment</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ app()->environment() }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Current Settings Display -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Pengaturan Saat Ini</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Lazy Loading -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Lazy Loading</p>
                            <p class="text-sm text-gray-500">Performance</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($lazyLoading)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Dark Mode -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Dark Mode</p>
                            <p class="text-sm text-gray-500">Theme</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($darkMode)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Email Notifications -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Email Notifications</p>
                            <p class="text-sm text-gray-500">Email</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($emailNotifications)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Browser Notifications -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6zM4 5h6V1H4v4zM15 7h5l-5-5v5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Browser Notifications</p>
                            <p class="text-sm text-gray-500">Browser</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($browserNotifications)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Auto Export -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Auto Export</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($autoExportSchedule) }} - {{ strtoupper($autoExportFormat) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($autoExportEnabled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>

                <!-- Data Cleanup -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Data Cleanup</p>
                            <p class="text-sm text-gray-500">{{ $dataRetentionDays }} hari retensi</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        @if($dataCleanupEnabled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all toggle switches
        const toggles = document.querySelectorAll('input[type="checkbox"]');
        
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                // Auto submit form when any toggle changes
                this.form.submit();
            });
        });

        // Handle lazy loading toggle specifically for LoadingManager
        const lazyLoadingToggle = document.getElementById('lazy_loading');
        if (lazyLoadingToggle && window.LoadingManager) {
            lazyLoadingToggle.addEventListener('change', function() {
                window.LoadingManager.updateSetting(this.checked);
            });
        }

        // Handle dark mode toggle
        const darkModeToggle = document.getElementById('dark_mode');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', function() {
                // Apply dark mode immediately
                if (this.checked) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('darkMode', 'true');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('darkMode', 'false');
                }
            });
        }

        // Check for saved dark mode preference
        const savedDarkMode = localStorage.getItem('darkMode');
        if (savedDarkMode === 'true') {
            document.documentElement.classList.add('dark');
        }
    });
</script>
@endsection
