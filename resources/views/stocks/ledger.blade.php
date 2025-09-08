@extends('layouts.app', ['title' => 'Stock Ledger'])

@section('content')
@php
    // If request is AJAX (modal load), render only the partial content so it can be injected into modal
    if(request()->ajax()) {
        echo view('stocks._ledger_content', compact('product','entries','locationId'))->render();
        return; // stop further rendering
    }

    // Otherwise render full page and include the partial
@endphp

@include('stocks._ledger_content')

@endsection
