@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($user) && $user->exists ? 'Edit' : 'Create' }} User</h1>
        <p class="text-gray-600 dark:text-gray-400">Add a new user and assign roles.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ isset($user) && $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user) && $user->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" required>
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" required>
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" {{ isset($user) && $user->exists ? '' : 'required' }}>
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password when editing.</p>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Password Confirmation</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" {{ isset($user) && $user->exists ? '' : 'required' }}>
                    @error('password_confirmation') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Roles</label>
                    <select name="roles[]" multiple class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ (isset($user) && $user->roles->contains('id', $role->id)) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('roles') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3 pt-6 border-t border-gray-600 dark:border-gray-600">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ isset($user) && $user->exists ? 'Update User' : 'Create User' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
