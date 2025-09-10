<form id="createRoleForm" action="{{ route('roles.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input type="text" name="name" class="form-input w-full" required>
    </div>
    <div class="mb-3">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
        @php
            $groups = [];
            foreach($permissions as $perm) {
                $prefix = explode('.', $perm->name)[0];
                $groups[$prefix][] = $perm;
            }
        @endphp
        @foreach($groups as $group => $perms)
            <div class="mb-2 border rounded p-2">
                <label><input type="checkbox" class="master-checkbox" data-group="{{ $group }}"> <strong>{{ ucfirst($group) }}</strong></label>
                <div class="pl-3">
                    @foreach($perms as $perm)
                        <div>
                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="perm-checkbox-{{ $group }}"> {{ $perm->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-end gap-2 mt-4">
        <button type="button" class="px-4 py-2 rounded-lg bg-gray-500 text-white" onclick="closeModal('roleCreateModal')">Cancel</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Create</button>
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
