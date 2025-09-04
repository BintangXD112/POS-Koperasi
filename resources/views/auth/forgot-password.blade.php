@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="max-w-md mx-auto bg-white shadow rounded-lg p-6">
	<h1 class="text-xl font-semibold text-gray-900 mb-2">Lupa Password</h1>
	<p class="text-sm text-gray-600 mb-4">Masukkan email Anda, kami akan mengirim link untuk mereset password.</p>

	<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
		@csrf
		<div>
			<label class="block text-sm font-medium text-gray-700">Email</label>
			<input name="email" type="email" required class="mt-1 block w-full border-gray-300 rounded-md" value="{{ old('email') }}" />
		</div>
		<button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Kirim Link Reset</button>
	</form>

	<div class="text-center mt-4">
		<a class="text-blue-600" href="{{ route('login') }}">Kembali ke Login</a>
	</div>
</div>
@endsection


