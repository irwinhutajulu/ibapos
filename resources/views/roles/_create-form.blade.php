<form id="createRoleForm" action="{{ route('roles.store') }}" method="POST" class="grid grid-cols-1 gap-6">
    @csrf
    <div>
        <label for="role-name" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Role Name <span class="text-red-400">*</span></label>
        <input type="text" name="name" id="role-name" required
               class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white"
               placeholder="Enter role name">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Permissions</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @php
            $groups = [];
            foreach($permissions as $perm) {
                $prefix = explode('.', $perm->name)[0];
                $groups[$prefix][] = $perm;
            }
        @endphp
        @foreach($groups as $group => $perms)
            <div class="border rounded-lg p-3 bg-gray-800 dark:bg-gray-900">
                <label class="flex items-center mb-2">
                    <input type="checkbox" class="master-checkbox mr-2" data-group="{{ $group }}">
                    <strong class="text-blue-400">{{ ucfirst($group) }}</strong>
                </label>
                <div class="pl-3 flex flex-col gap-1">
                    @foreach($perms as $perm)
                        <label class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="perm-checkbox-{{ $group }} mr-2">
                            <span class="text-gray-300">{{ $perm->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
    </div>
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" onclick="closeModal('roleCreateModal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Role
        </button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.master-checkbox').forEach(function(master) {
        master.addEventListener('change', function() {
            var group = master.getAttribute('data-group');
            document.querySelectorAll('.perm-checkbox-' + group).forEach(function(cb) {
                cb.checked = master.checked;
            });
        });
    });
    document.getElementById('createRoleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            body: data
        }).then(async res => {
            if (res.ok) {
                closeModal('roleCreateModal');
                window.location.reload();
            } else {
                let msg = 'Failed to create role.';
                try { msg = (await res.json()).message || msg; } catch {}
                alert(msg);
            }
        }).catch(() => alert('Failed to create role.'));
    });
});
</script>
