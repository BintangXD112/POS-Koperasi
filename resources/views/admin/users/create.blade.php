@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Tambah User Baru</h1>
        <p class="mt-1 text-sm text-gray-600">Buat user baru untuk sistem koperasi</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.users.store') }}" method="POST" class="px-4 py-5 sm:p-6 space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role_id" id="role_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                       class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button type="button" tabindex="-1" aria-label="toggle password" data-toggle-password="#password"
                        class="absolute right-2 top-8 px-2 py-1 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="mt-1 block w-full pr-10 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <button type="button" tabindex="-1" aria-label="toggle password" data-toggle-password="#password_confirmation"
                        class="absolute right-2 top-8 px-2 py-1 text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.users') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
	document.querySelectorAll('[data-toggle-password]').forEach(function(btn){
		btn.addEventListener('click', function(){
			const target = document.querySelector(btn.getAttribute('data-toggle-password'));
			if (!target) return;
			target.type = target.type === 'password' ? 'text' : 'password';
			btn.classList.toggle('text-gray-700');
		});
	});
</script>
@endpush
@endsection
