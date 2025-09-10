<form id="createPermissionForm" action="{{ route('permissions.store') }}" method="POST" class="grid grid-cols-1 gap-6">
    @csrf
    <div>
        <label for="permission-name" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Permission Name <span class="text-red-400">*</span></label>
        <input type="text" name="name" id="permission-name" required
               class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white"
               placeholder="Enter permission name">
    </div>
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" onclick="closeModal('permissionCreateModal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Permission
        </button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('createPermissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            body: data
        }).then(async res => {
            if (res.ok) {
                closeModal('permissionCreateModal');
                window.location.reload();
            } else {
                let msg = 'Failed to create permission.';
                try { msg = (await res.json()).message || msg; } catch {}
                alert(msg);
            }
        }).catch(() => alert('Failed to create permission.'));
    });
});
</script>
