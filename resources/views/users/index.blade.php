@extends('layouts.app')

@section('content')
@include('users._flash_notify')
<div class="p-6">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage application users and their roles/permissions</p>
        </div>
        <div class="flex items-center gap-4 mr-4">
            <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" id="showTrashedToggle" class="form-checkbox h-4 w-4 text-indigo-600" {{ request()->boolean('show_trashed') ? 'checked' : '' }}>
                <span class="ml-2">Show trashed</span>
            </label>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New User
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Roles</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" data-user-id="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-300">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors" title="Edit User">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit</span>
                                    </a>
                                    @if(method_exists($user, 'trashed') && $user->trashed())
                                        <button type="button" data-restore-id="{{ $user->id }}" class="inline-flex items-center px-3 py-1.5 border border-green-200 dark:border-green-800 text-xs font-medium rounded-lg text-green-600 dark:text-green-400 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 transition-colors" title="Restore User">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            <span>Restore</span>
                                        </button>
                                    @else
                                        <button type="button" data-delete-modal="userDeleteModal" data-delete-id="{{ $user->id }}" data-delete-name="{{ e($user->name) }}" data-delete-action="{{ url('admin/users') }}" class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" title="Delete User">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <span>Delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-gray-500">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</div>

<x-confirmation-modal modalId="userDeleteModal" title="Delete user" :message="'Are you sure you want to delete <span class=\'confirm-target-name font-semibold text-gray-900 dark:text-white\'></span>?'"></x-confirmation-modal>

<script>
window.openDeleteModal = function(id, name) {
    const action = '{{ url('admin/users') }}' + '/' + id;
    window.openConfirmationModal('userDeleteModal', action, name, { rowId: id });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('showTrashedToggle');
    if (toggle) {
        toggle.addEventListener('change', function () {
            const params = new URLSearchParams(window.location.search);
            if (this.checked) {
                params.set('show_trashed', '1');
            } else {
                params.delete('show_trashed');
            }
            window.location.search = params.toString();
        });
    }

    // restore handler (delegated)
    document.body.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-restore-id]');
        if (!btn) return;
        const id = btn.getAttribute('data-restore-id');
        if (!id) return;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ url('admin/users') }}' + '/' + id + '/restore', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        }).then(res => res.json())
        .then(json => {
            // on success, remove archived label and replace restore button with delete
            const row = document.querySelector('[data-user-id="' + id + '"]');
            if (row) {
                // remove (Archived) text if present
                const archived = row.querySelector('span.ml-2');
                if (archived && archived.textContent.includes('Archived')) archived.remove();
                // replace restore button with delete button by reloading the page fragment for simplicity
                window.location.reload();
            }
            // optionally show toast
            console.info('Restore response', json);
        }).catch(err => {
            console.error('Restore failed', err);
            alert('Restore failed');
        });
    });
});
</script>

@endsection
