@extends('layouts.app', ['title' => 'Notifications'])

@section('content')
<div class="flex items-center justify-between mb-4">
  <div class="font-medium">My Notifications</div>
</div>

@if(session('ok'))
  <div class="mb-3 p-2 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('ok') }}</div>
@endif

<div class="overflow-x-auto bg-white border rounded-md">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="text-left px-3 py-2">Type</th>
        <th class="text-left px-3 py-2">Data</th>
        <th class="text-left px-3 py-2">Created</th>
        <th class="text-left px-3 py-2">Status</th>
        <th class="px-3 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($notifications as $n)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $n->type }}</td>
        <td class="px-3 py-2"><pre class="whitespace-pre-wrap">{{ json_encode(json_decode($n->data, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></td>
        <td class="px-3 py-2">{{ $n->created_at }}</td>
        <td class="px-3 py-2">{{ $n->read_at ? 'Read' : 'Unread' }}</td>
        <td class="px-3 py-2 text-right">
          @if(!$n->read_at)
          <form action="{{ route('admin.notifications.read', $n->id) }}" method="post" class="inline">
            @csrf
            <button class="text-blue-700 hover:underline">Mark as read</button>
          </form>
          @else
            -
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
