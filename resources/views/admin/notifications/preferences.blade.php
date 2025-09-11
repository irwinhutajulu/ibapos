@extends('layouts.app', ['title' => 'Notification Preferences'])

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Notification Preferences</h2>
    <form method="POST" action="{{ route('admin.notifications.preferences.update') }}">
        @csrf
        <table class="table-auto w-full mb-6">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th>Type</th>
                    <th>Enabled</th>
                </tr>
            </thead>
            <tbody>
                @foreach($channels as $channel)
                    @foreach($types as $type)
                        @php
                            $setting = $settings->where('channel', $channel)->where('type', $type)->first();
                        @endphp
                        <tr>
                            <td>{{ ucfirst($channel) }}</td>
                            <td>{{ ucfirst(str_replace('_',' ',$type)) }}</td>
                            <td>
                                <input type="checkbox" name="enabled[{{ $channel }}][{{ $type }}]" value="1" {{ ($setting && $setting->enabled) ? 'checked' : '' }}>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <input type="hidden" name="channel[]" value="{{ implode(',', $channels) }}">
        <input type="hidden" name="type[]" value="{{ implode(',', $types) }}">
        <button type="submit" class="btn btn-primary">Save Preferences</button>
    </form>
    @if(session('ok'))
        <div class="mt-4 p-3 bg-green-100 text-green-800 rounded">{{ session('ok') }}</div>
    @endif
</div>
@endsection
