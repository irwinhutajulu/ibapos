@include('mutations._flash_notify')
@extends('layouts.app', ['title' => 'Stock Mutations'])

@section('content')
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Mutations</h2>
        <p class="text-gray-600 dark:text-gray-400">Track stock movements between locations</p>
    </div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('stock-mutations.create') }}" class="btn btn-primary">New Mutation</a>
    </div>
</div>

<!-- Mutations Table -->
@php
$tableHeaders = ['Date', 'Product', 'From', 'To', 'Quantity', 'Status'];
$tableRows = $mutations->map(function($m) {
    $productName = $m->product->name ?? ('#' . $m->product_id);
    return [
        'cells' => [
            $m->date,
            [
                'type' => 'link',
                'url' => route('stock-mutations.show', $m),
                'text' => $productName
            ],
            $m->fromLocation->name ?? ('Location #' . $m->from_location_id),
            $m->toLocation->name ?? ('Location #' . $m->to_location_id),
                [
                'type' => 'text',
                'text' => number_format($m->qty, 2, ',', '.'),
                'align' => 'right'
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($m->status),
                'style' => $m->status === 'confirmed' ? 'success' : ($m->status === 'rejected' ? 'danger' : 'warning')
            ]
        ],
        'actions' => ($m->status === 'pending') ? (function() use ($m) {
            $actions = [];
            $canEdit = auth()->check() && method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('super-admin') && auth()->id() === $m->requested_by;
            if ($canEdit) {
                $actions[] = [
                    'type' => 'button',
                    'label' => 'Edit',
                    'style' => 'secondary',
                    'onclick' => "window.location.href='" . route('stock-mutations.edit', $m) . "'",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"></path></svg>'
                ];
            }
            $actions[] = [
                'type' => 'button',
                'label' => 'Confirm',
                'style' => 'success',
                'onclick' => "ajaxConfirm({$m->id}, '" . route('stock-mutations.confirm', $m) . "')",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            ];
            $actions[] = [
                'type' => 'button',
                'label' => 'Reject',
                'style' => 'danger',
                'onclick' => "ajaxReject({$m->id}, '" . route('stock-mutations.reject', $m) . "')",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            ];
            return $actions;
        })() : []
    ];
})->toArray();
@endphp

<x-table 
    :headers="$tableHeaders"
    :rows="$tableRows"
    :pagination="$mutations"
    empty-message="No mutations found"
    empty-description="Stock mutations will appear here when products are moved between locations."
/>

<!-- Hidden Forms for Actions -->
@foreach($mutations as $m)
    @if($m->status === 'pending')
    <form id="confirm-form-{{ $m->id }}" action="{{ route('stock-mutations.confirm', $m) }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <form id="reject-form-{{ $m->id }}" action="{{ route('stock-mutations.reject', $m) }}" method="POST" style="display: none;">
        @csrf
    </form>
    @endif
@endforeach

@push('scripts')
<script>
// Attach data-mutation-id to table rows by matching hidden forms we rendered per mutation.
document.addEventListener('DOMContentLoaded', function() {
    try {
        document.querySelectorAll('form[id^="confirm-form-"]').forEach(f => {
            const id = f.id.replace('confirm-form-','');
            // Find nearest table row containing the confirm form or nearby by scanning rows
            // We assume the table rows are in document order and match the mutations sequence.
            const rowById = document.getElementById('mutation-row-' + id);
            if (rowById) {
                rowById.setAttribute('data-mutation-id', id);
                return;
            }
            // fallback: find a link to show for this mutation
            const anchor = document.querySelector(`a[href*="/stock-mutations/${id}"]`);
            if (anchor) {
                const row = anchor.closest('tr');
                if (row) row.setAttribute('data-mutation-id', id);
            }
        });
    } catch(e) { console.warn('row tagging failed', e); }
});

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

function findRowByMutationId(id) {
    // x-table generates rows with data-row-id when possible; fall back to id-based lookup
    return document.querySelector(`[data-mutation-id="${id}"]`) || document.getElementById(`mutation-row-${id}`);
}

function updateRowToFinalState(id, status) {
    const row = findRowByMutationId(id);
    if (!row) return;
    // Update badge cell (assumes last cell contains badge)
    const badge = row.querySelector('.badge');
    if (badge) {
        badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        badge.classList.remove('badge-warning','badge-success','badge-danger');
        if (status === 'confirmed') badge.classList.add('badge-success');
        if (status === 'rejected') badge.classList.add('badge-danger');
    }
    // Remove action buttons
    const actions = row.querySelectorAll('button[data-action="mutation-action"]');
    actions.forEach(b => b.remove());
}

async function ajaxConfirm(id, url) {
    if (!confirm('Confirm this mutation?')) return;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        const j = await res.json().catch(()=>null);
        if (res.ok) {
            updateRowToFinalState(id, 'confirmed');
            try { window.notify(j?.message || 'Mutation confirmed', 'success'); } catch(e) { console.log('notify missing'); }
        } else {
            try { window.notify(j?.message || 'Confirm failed', 'error'); } catch(e) { alert(j?.message || 'Confirm failed'); }
        }
    } catch (e) { try { window.notify('Network error', 'error'); } catch(_) { alert('Network error'); } }
}

async function ajaxReject(id, url) {
    if (!confirm('Reject this mutation?')) return;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        });
        const j = await res.json().catch(()=>null);
        if (res.ok) {
            updateRowToFinalState(id, 'rejected');
            try { window.notify(j?.message || 'Mutation rejected', 'success'); } catch(e) { console.log('notify missing'); }
        } else {
            try { window.notify(j?.message || 'Reject failed', 'error'); } catch(e) { alert(j?.message || 'Reject failed'); }
        }
    } catch (e) { try { window.notify('Network error', 'error'); } catch(_) { alert('Network error'); } }
}
</script>
@endpush
@endsection
