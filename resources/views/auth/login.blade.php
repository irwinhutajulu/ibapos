<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - IBAPOS</title>
  @vite(['resources/css/app.css'])
  <style>body{background:#f9fafb}</style>
  </head>
<body class="min-h-screen grid place-items-center">
  <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm bg-white border rounded-md p-6 space-y-3">
    @csrf
    <h1 class="text-lg font-semibold">Login</h1>
    @if($errors->any())
      <div class="text-sm text-red-600">{{ $errors->first() }}</div>
    @endif
    <div>
      <label class="block text-sm text-gray-600">Email</label>
      <input name="email" type="email" class="w-full px-3 py-2 border rounded-md" value="{{ old('email') }}" required>
    </div>
    <div>
      <label class="block text-sm text-gray-600">Password</label>
      <input name="password" type="password" class="w-full px-3 py-2 border rounded-md" required>
    </div>
    <div class="flex items-center justify-between">
      <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="border"> Remember</label>
      <button class="px-3 py-2 bg-gray-900 text-white rounded-md">Login</button>
    </div>
  </form>
</body>
</html>
