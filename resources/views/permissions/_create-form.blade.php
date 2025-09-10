<form id="createPermissionForm" action="{{ route('permissions.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input type="text" name="name" class="form-input w-full" required>
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <button type="button" class="px-4 py-2 rounded-lg bg-gray-500 text-white" onclick="closeModal('permissionCreateModal')">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Create</button>
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
