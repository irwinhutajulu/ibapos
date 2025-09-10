@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage all available permissions</p>
        </div>
        <button type="button" onclick="openModal('permissionCreateModal')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Permission
        </button>
</div>
<x-modal id="permissionCreateModal" title="Create Permission" size="md" maxHeight="true">
    @include('permissions._create-form')
</x-modal>
    </div>
    @if(session('success'))<div class="alert alert-success mb-4">{{ session('success') }}</div>@endif
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        @php
            $groups = [];
            foreach($permissions as $perm) {
                $prefix = explode('.', $perm->name)[0];
                $groups[$prefix][] = $perm;
            }
        @endphp
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($groups as $group => $perms)
            <div class="border-b last:border-b-0">
                <button type="button" class="w-full flex justify-between items-center px-6 py-4 bg-gray-50 dark:bg-gray-700 focus:outline-none group" onclick="document.getElementById('perm-{{ $group }}').classList.toggle('hidden')">
                    <span class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ ucfirst($group) }}</span>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="perm-{{ $group }}" class="hidden px-6 pb-4">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($perms as $permission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $permission->name }}</div>
                                </td>
                                <td class="py-2 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('permissions.edit', $permission) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors" title="Edit Permission">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            <span>Edit</span>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" onclick="return confirm('Delete this permission?')" title="Delete Permission">
                                                <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
