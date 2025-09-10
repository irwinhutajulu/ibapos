
<!-- Modal Confirmation -->
<div id="modal-confirm" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
  <div class="bg-gray-800 rounded-lg shadow-lg p-6 w-80">
    <div class="text-lg font-semibold mb-2 text-white" id="modal-confirm-title">Konfirmasi Aksi</div>
    <div class="mb-4 text-gray-300" id="modal-confirm-desc">Apakah Anda yakin ingin melanjutkan aksi ini?</div>
    <div class="flex gap-2 justify-end">
      <button id="modal-cancel" class="px-3 py-1 border border-gray-600 rounded-lg text-gray-300">Batal</button>
      <button id="modal-ok" class="px-3 py-1 border border-amber-600 rounded-lg bg-amber-600 text-white">Lanjutkan</button>
    </div>
  </div>
</div>
@extends('layouts.app', ['title' => 'Purchase '.$purchase->invoice_no])

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
  @if(session('ok'))
    <script>window.notify(@json(session('ok')), 'success')</script>
  @endif
  <div class="flex items-start justify-between">
    <div>
      <div class="text-sm text-gray-300">Invoice</div>
      <div class="text-lg font-semibold text-white">{{ $purchase->invoice_no }}</div>
      <div class="text-sm text-gray-300 mt-2">Date</div>
      <div class="text-white">{{ $purchase->date?->format('Y-m-d H:i') }}</div>
    </div>
    <div class="text-right">
      <div class="text-sm text-gray-300">Status</div>
      <div class="font-medium text-white">{{ ucfirst($purchase->status) }}</div>
      <div class="mt-2 text-sm text-gray-300">User</div>
      <div class="text-white">{{ $purchase->user->name ?? '-' }}</div>
    </div>
  </div>

  <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
      <div class="text-sm text-gray-300">Supplier</div>
      <div class="text-white">{{ $purchase->supplier->name ?? '-' }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-300">Freight</div>
      <div class="text-white">{{ number_format($purchase->freight_cost ?? 0,2) }}</div>
    </div>
    <div>
      <div class="text-sm text-gray-300">Totals</div>
      <div class="text-white">Total: {{ number_format($purchase->total ?? 0,2) }} | Weight: {{ number_format($purchase->total_weight ?? 0,3) }}</div>
    </div>
  </div>

  <div class="mt-6 overflow-x-auto border border-gray-600 dark:border-gray-600 rounded-lg">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-900 text-gray-300">
        <tr>
          <th class="text-left px-3 py-2">Product</th>
          <th class="text-right px-3 py-2">Qty</th>
          <th class="text-right px-3 py-2">Price</th>
          <th class="text-right px-3 py-2">Subtotal</th>
          @if($purchase->status === 'posted')
            <th class="text-right px-3 py-2">Landed Cost</th>
            <th class="text-right px-3 py-2">Note</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($purchase->items as $it)
        <tr class="border-t border-gray-700">
          <td class="px-3 py-2 text-white">{{ $it->product->name ?? ('#'.$it->product_id) }}</td>
          <td class="px-3 py-2 text-right text-white">{{ number_format($it->qty,3) }}</td>
          <td class="px-3 py-2 text-right text-white">{{ number_format($it->price,2) }}</td>
          <td class="px-3 py-2 text-right text-white">{{ number_format($it->subtotal,2) }}</td>
          @if($purchase->status === 'posted')
            @php
              // Estimasi landed cost: freight dialokasikan proporsional subtotal
              $freight = $purchase->freight_cost ?? 0;
              $total = $purchase->total > 0 ? $purchase->total : 1;
              $allocation = $freight * ($it->subtotal / $total);
              $landed = $it->price + ($allocation / max($it->qty,1));
            @endphp
            <td class="px-3 py-2 text-right text-white">{{ number_format($landed,2) }}</td>
            <td class="px-3 py-2 text-gray-300">Freight: {{ number_format($allocation,2) }}</td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
    @if($purchase->status === 'posted')
      <div class="mt-2 text-xs text-gray-300">* Landed cost = price + proporsi freight per item. Alokasi otomatis oleh service saat posting.</div>
    @endif
  </div>

  <div class="mt-4 flex items-center gap-2">
    @if($purchase->status === 'draft')
      @can('purchases.receive')
        <form class="js-ajax-form" data-action="{{ route('purchases.receive', $purchase) }}" method="post">
          @csrf
          <button type="submit" class="px-3 py-2 border border-amber-600 rounded-lg text-amber-400 hover:bg-amber-900 flex items-center gap-2">
            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Receive
          </button>
        </form>
      @endcan
      @can('purchases.post')
        <form class="js-ajax-form" data-action="{{ route('purchases.post', $purchase) }}" method="post">
          @csrf
          <button type="submit" class="px-3 py-2 border border-green-600 rounded-lg text-green-400 hover:bg-green-900 flex items-center gap-2">
            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Post
          </button>
        </form>
      @endcan
      @can('purchases.void')
        <form class="js-ajax-form" data-action="{{ route('purchases.void', $purchase) }}" method="post">
          @csrf
          <button type="submit" class="px-3 py-2 border border-red-600 rounded-lg text-red-400 hover:bg-red-900 flex items-center gap-2">
            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            Void
          </button>
        </form>
      @endcan
    @elseif($purchase->status === 'received')
      @can('purchases.post')
      <form action="{{ route('purchases.post', $purchase) }}" method="post" onsubmit="return confirm('Post this purchase?')">
        @csrf
        <button class="px-3 py-2 border border-green-600 rounded-lg text-green-400 hover:bg-green-900 flex items-center gap-2">
          <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          Post
        </button>
      </form>
      @endcan
      @can('purchases.void')
      <form action="{{ route('purchases.void', $purchase) }}" method="post" onsubmit="return confirm('Void this purchase?')">
        @csrf
        <button class="px-3 py-2 border border-red-600 rounded-lg text-red-400 hover:bg-red-900 flex items-center gap-2">
          <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          Void
        </button>
      </form>
      @endcan
    @elseif($purchase->status === 'posted')
      @can('purchases.void')
      <form action="{{ route('purchases.void', $purchase) }}" method="post" onsubmit="return confirm('Void this purchase?')">
        @csrf
        <button class="px-3 py-2 border border-red-600 rounded-lg text-red-400 hover:bg-red-900 flex items-center gap-2">
          <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          Void
        </button>
      </form>
      @endcan
    @endif
    <a href="{{ route('purchases.index') }}" class="ml-auto px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Back</a>
  </div>
</div>
<script>
let pendingForm = null;
document.querySelectorAll('.js-ajax-form').forEach(form => {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    pendingForm = form;
    let action = form.querySelector('button[type="submit"]').textContent.trim();
    document.getElementById('modal-confirm-title').textContent = 'Konfirmasi ' + action;
    document.getElementById('modal-confirm-desc').textContent = 'Apakah Anda yakin ingin melakukan aksi "' + action + '" pada pembelian ini?';
    document.getElementById('modal-confirm').classList.remove('hidden');
  });
});

document.getElementById('modal-cancel').onclick = function() {
  document.getElementById('modal-confirm').classList.add('hidden');
  pendingForm = null;
};
document.getElementById('modal-ok').onclick = function() {
  if (!pendingForm) return;
  document.getElementById('modal-confirm').classList.add('hidden');
  const form = pendingForm;
  const url = form.getAttribute('data-action');
  const token = form.querySelector('input[name="_token"]').value;
  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': token,
      'Accept': 'application/json',
    },
    body: new URLSearchParams({}),
  })
  .then(res => res.json())
  .then(data => {
    window.notify(data.message || 'Berhasil!', 'success');
    setTimeout(() => window.location.reload(), 1200);
  })
  .catch(err => {
    window.notify('Gagal: ' + (err.message || 'Error'), 'error');
  });
  pendingForm = null;
};
</script>
@endsection
