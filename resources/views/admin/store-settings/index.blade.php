@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('subtitle', 'Kelola informasi dan konfigurasi toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pengaturan Toko</h1>
                <p class="text-gray-600 mt-1">Kelola informasi dan konfigurasi toko Anda</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($settings->store_logo)
                    <img src="{{ $settings->logo_url }}" alt="Logo" class="w-16 h-16 object-contain rounded-lg border border-gray-200">
                @else
                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form method="POST" action="{{ route('admin.store-settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                <p class="text-sm text-gray-600">Informasi utama tentang toko Anda</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Store Name -->
                    <div class="md:col-span-2">
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="store_name" 
                               name="store_name" 
                               value="{{ old('store_name', $settings->store_name) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_name') border-red-500 @enderror"
                               placeholder="Masukkan nama toko"
                               required>
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Owner -->
                    <div>
                        <label for="store_owner" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Pemilik
                        </label>
                        <input type="text" 
                               id="store_owner" 
                               name="store_owner" 
                               value="{{ old('store_owner', $settings->store_owner) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_owner') border-red-500 @enderror"
                               placeholder="Masukkan nama pemilik">
                        @error('store_owner')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Phone -->
                    <div>
                        <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input type="text" 
                               id="store_phone" 
                               name="store_phone" 
                               value="{{ old('store_phone', $settings->store_phone) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_phone') border-red-500 @enderror"
                               placeholder="Masukkan nomor telepon">
                        @error('store_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Email -->
                    <div>
                        <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Toko
                        </label>
                        <input type="email" 
                               id="store_email" 
                               name="store_email" 
                               value="{{ old('store_email', $settings->store_email) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_email') border-red-500 @enderror"
                               placeholder="Masukkan email toko">
                        @error('store_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Website -->
                    <div>
                        <label for="store_website" class="block text-sm font-medium text-gray-700 mb-2">
                            Website
                        </label>
                        <input type="url" 
                               id="store_website" 
                               name="store_website" 
                               value="{{ old('store_website', $settings->store_website) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_website') border-red-500 @enderror"
                               placeholder="https://example.com">
                        @error('store_website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Store Address -->
                <div>
                    <label for="store_address" class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Toko
                    </label>
                    <textarea id="store_address" 
                              name="store_address" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_address') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap toko">{{ old('store_address', $settings->store_address) }}</textarea>
                    @error('store_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Description -->
                <div>
                    <label for="store_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Toko
                    </label>
                    <textarea id="store_description" 
                              name="store_description" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('store_description') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi singkat tentang toko">{{ old('store_description', $settings->store_description) }}</textarea>
                    @error('store_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Logo & Branding -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Logo & Branding</h3>
                <p class="text-sm text-gray-600">Unggah logo dan konfigurasi branding</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Current Logo -->
                @if($settings->store_logo)
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <img src="{{ $settings->logo_url }}" alt="Current Logo" class="w-20 h-20 object-contain rounded-lg border border-gray-200">
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Logo Saat Ini</p>
                            <p class="text-sm text-gray-500">Klik untuk melihat ukuran penuh</p>
                        </div>
                        <div class="ml-auto">
                            <a href="{{ route('admin.store-settings.remove-logo') }}" 
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors duration-200 js-confirm"
                               data-title="Hapus Logo?" 
                               data-text="Logo toko akan dihapus permanen." 
                               data-icon="warning" 
                               data-confirm="Ya, hapus">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Logo
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Logo Upload -->
                <div>
                    <label for="store_logo" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $settings->store_logo ? 'Ganti Logo' : 'Unggah Logo' }}
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="store_logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Unggah file</span>
                                    <input id="store_logo" name="store_logo" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF, SVG hingga 2MB</p>
                        </div>
                    </div>
                    @error('store_logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Business Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pengaturan Bisnis</h3>
                <p class="text-sm text-gray-600">Konfigurasi mata uang, zona waktu, dan informasi pajak</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Mata Uang <span class="text-red-500">*</span>
                        </label>
                        <select id="currency" 
                                name="currency"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('currency') border-red-500 @enderror"
                                required>
                            <option value="IDR" {{ old('currency', $settings->currency) == 'IDR' ? 'selected' : '' }}>IDR - Rupiah Indonesia</option>
                            <option value="USD" {{ old('currency', $settings->currency) == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency', $settings->currency) == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency', $settings->currency) == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Zona Waktu <span class="text-red-500">*</span>
                        </label>
                        <select id="timezone" 
                                name="timezone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('timezone') border-red-500 @enderror"
                                required>
                            <option value="Asia/Jakarta" {{ old('timezone', $settings->timezone) == 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Jakarta)</option>
                            <option value="Asia/Makassar" {{ old('timezone', $settings->timezone) == 'Asia/Makassar' ? 'selected' : '' }}>WITA (Makassar)</option>
                            <option value="Asia/Jayapura" {{ old('timezone', $settings->timezone) == 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Jayapura)</option>
                            <option value="UTC" {{ old('timezone', $settings->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ old('timezone', $settings->timezone) == 'America/New_York' ? 'selected' : '' }}>EST (New York)</option>
                            <option value="Europe/London" {{ old('timezone', $settings->timezone) == 'Europe/London' ? 'selected' : '' }}>GMT (London)</option>
                            <option value="Asia/Tokyo" {{ old('timezone', $settings->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>JST (Tokyo)</option>
                            <option value="Asia/Shanghai" {{ old('timezone', $settings->timezone) == 'Asia/Shanghai' ? 'selected' : '' }}>CST (Shanghai)</option>
                        </select>
                        @error('timezone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Number -->
                    <div>
                        <label for="tax_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Pajak
                        </label>
                        <input type="text" 
                               id="tax_number" 
                               name="tax_number" 
                               value="{{ old('tax_number', $settings->tax_number) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('tax_number') border-red-500 @enderror"
                               placeholder="Masukkan nomor pajak">
                        @error('tax_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Toko
                        </label>
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $settings->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="is_active" class="text-sm text-gray-700">
                                Toko aktif
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer Text -->
                <div>
                    <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Teks Footer
                    </label>
                    <textarea id="footer_text" 
                              name="footer_text" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('footer_text') border-red-500 @enderror"
                              placeholder="Masukkan teks yang akan muncul di footer (opsional)">{{ old('footer_text', $settings->footer_text) }}</textarea>
                    @error('footer_text')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-3">
            <button type="button" 
                    onclick="window.location.reload()"
                    class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                Reset
            </button>
            <button type="submit" 
                    class="px-6 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview logo upload
    const logoInput = document.getElementById('store_logo');
    const logoPreview = document.querySelector('.border-dashed');
    
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Create preview
                    const preview = document.createElement('div');
                    preview.className = 'flex items-center space-x-3';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-16 h-16 object-contain rounded-lg border border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${file.name}</p>
                            <p class="text-sm text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    `;
                    
                    // Replace content
                    logoPreview.innerHTML = '';
                    logoPreview.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
@endsection
