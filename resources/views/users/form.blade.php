@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ isset($user) && $user->exists ? 'Edit' : 'Create' }} User</h1>
        <p class="text-gray-600 dark:text-gray-400">Add a new user, assign roles and optional direct permissions.</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ isset($user) && $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if(isset($user) && $user->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" required>
                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" required>
                    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                    <input type="password" name="password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" {{ isset($user) && $user->exists ? '' : 'required' }}>
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password when editing.</p>
                    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                    <select name="roles[]" multiple class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ (isset($user) && $user->roles->contains('id', $role->id)) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('roles') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="my-6 border-gray-200 dark:border-gray-700">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Direct Permissions</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($permissions as $perm)
                        <label class="flex items-center gap-2 p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" {{ (isset($user) && $user->hasPermissionTo($perm->name)) ? 'checked' : '' }} class="form-checkbox text-blue-600">
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-200">Cancel</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm">Save</button>
            </div>
        </form>
    </div>
</div>

@endsection
