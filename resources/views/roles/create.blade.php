@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Create Role</h1>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Permissions</label>
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
                });
            </script>
        </div>
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
