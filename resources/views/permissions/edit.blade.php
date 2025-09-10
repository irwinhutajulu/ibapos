@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Permission</h1>
    <form action="{{ route('permissions.update', $permission) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $permission->name }}" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
