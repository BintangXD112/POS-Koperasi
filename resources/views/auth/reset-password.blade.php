@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-lg p-6">
	<h1 class="text-xl font-semibold text-gray-900 mb-2">Reset Password</h1>
	<p class="text-sm text-gray-600 mb-4">Masukkan password baru Anda.</p>

	<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}" />
		<input type="hidden" name="email" value="{{ $email }}" />
		<div>
			<label class="block text-sm font-medium text-gray-700">Password Baru</label>
			<input name="password" type="password" required class="mt-1 block w-full border-gray-300 rounded-md" />
		</div>
		<div>
			<label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
			<input name="password_confirmation" type="password" required class="mt-1 block w-full border-gray-300 rounded-md" />
		</div>
		<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Reset Password</button>
	</form>

	<div class="text-center mt-4">
		<a class="text-blue-600" href="{{ route('login') }}">Kembali ke Login</a>
	</div>
</div>
@endsection


