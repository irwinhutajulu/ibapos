@extends('layouts.app', ['title' => 'New Purchase'])

@section('content')
<form method="post" action="{{ route('purchases.store') }}" x-data="purchaseForm()" x-init="init()" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
  @csrf
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Invoice No</label>
      <input name="invoice_no" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Date</label>
      <input type="datetime-local" name="date" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Lokasi</label>
      <select name="location_id" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" required>
        <option value="">-- Pilih Lokasi --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}">{{ $loc->name }}</option>
        @endforeach
      </select>
    </div>
    <div x-data="{q:'',results:[],selected:null,show:false, showModal:false, newSupplier:{name:'',phone:'',address:''}}" @click.away="show=false">
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Supplier</label>
      <input type="text" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white mb-1" placeholder="Cari supplier..." x-model="q" @input.debounce.300ms="fetch('{{ url('api/suppliers') }}?q='+encodeURIComponent(q)).then(r=>r.json()).then(data=>{results=data;show=true})">
      <template x-if="show && results.length">
        <ul class="bg-gray-800 border border-gray-600 rounded-lg shadow mt-1 max-h-40 overflow-auto">
          <template x-for="item in results" :key="item.id">
            <li @click="selected=item;show=false" class="px-3 py-2 cursor-pointer hover:bg-blue-600 text-white" x-text="item.name"></li>
          </template>
        </ul>
      </template>
      <template x-if="show && q && results.length === 0">
        <div class="text-sm text-gray-400 mt-1 flex items-center gap-2">
          Tidak ditemukan. <button type="button" class="px-2 py-1 bg-blue-600 text-white rounded-lg text-xs" @click="showModal=true">Tambah Supplier Baru</button>
        </div>
      </template>
      <input type="hidden" name="supplier_id" :value="selected?.id">
      <template x-if="selected"><div class="text-xs text-gray-400">Terpilih: <span x-text="selected.name"></span></div></template>

      <!-- Modal input supplier baru -->
      <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 w-80">
          <div class="text-lg font-semibold mb-2 text-white">Tambah Supplier Baru</div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white" placeholder="Nama" x-model="newSupplier.name"></div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white" placeholder="Telepon" x-model="newSupplier.phone"></div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white" placeholder="Alamat" x-model="newSupplier.address"></div>
          <div class="flex gap-2 justify-end mt-2">
            <button type="button" class="px-3 py-1 border border-gray-600 rounded-lg text-gray-300" @click="showModal=false">Batal</button>
            <button type="button" class="px-3 py-1 border border-blue-600 rounded-lg bg-blue-600 text-white" @click="
              fetch('{{ url('suppliers') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                },
                body: JSON.stringify(newSupplier)
              })
              .then(r => r.json())
              .then(data => { if(data.id){ selected = data; showModal=false; q=data.name; results=[]; window.notify('Supplier ditambahkan','success'); } else { window.notify('Gagal tambah supplier','error'); } })
              .catch(()=>window.notify('Gagal tambah supplier','error'))
            ">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 py-4">
      <div>
        <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Freight Cost</label>
        <input type="number" step="0.01" name="freight_cost" x-model.number="freight_cost" class="w-60 px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" placeholder="0.00">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Loading Cost</label>
        <input type="number" step="0.01" name="loading_cost" x-model.number="loading_cost" class="w-60 px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white" placeholder="0.00">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-300 mb-1">Unloading Cost</label>
        <input type="number" step="0.01" name="unloading_cost" x-model.number="unloading_cost" class="w-60 px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white" placeholder="0.00">
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
          <th class="px-3 py-2 text-right">Action</th>
        </tr>
      </thead>
      <tbody>
        <template x-for="(row, idx) in rows" :key="idx">
          <tr class="border-t border-gray-700">
            <td class="px-3 py-2">
              <select :name="`items[${idx}][product_id]`" class="w-64 px-2 py-1 border border-gray-600 rounded-lg bg-gray-700 text-white" x-model.number="row.product_id" required>
                <option value="">-- Select product --</option>
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </td>
            <td class="px-3 py-2 text-right">
              <input type="number" step="0.001" min="0.001" class="w-28 px-2 py-1 border border-gray-600 rounded-lg text-right bg-gray-700 text-white" :name="`items[${idx}][qty]`" x-model.number="row.qty" @input="recalc(idx)" required>
            </td>
            <td class="px-3 py-2 text-right">
              <input type="number" step="0.01" min="0" class="w-28 px-2 py-1 border border-gray-600 rounded-lg text-right bg-gray-700 text-white" :name="`items[${idx}][price]`" x-model.number="row.price" @input="recalc(idx)" required>
            </td>
            <td class="px-3 py-2 text-right" x-text="row.subtotal.toFixed(2)"></td>
            <td class="px-3 py-2 text-right">
              <button type="button" class="text-red-400 hover:text-red-600" @click="remove(idx)">
                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Remove
              </button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
  <div class="mt-2"><button type="button" class="px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-gray-300 hover:bg-gray-600" @click="add()">
    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
    Add Item
  </button></div>

  <div class="mt-6 flex items-center gap-4">
    <div class="ml-auto text-right">
      <div class="text-sm text-gray-300">Total</div>
      <div class="text-lg font-semibold text-white" x-text="total().toFixed(2)"></div>
    </div>
    <button class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center gap-2">
      <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
      </svg>
      Save Draft
    </button>
    <a href="{{ route('purchases.index') }}" class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Cancel</a>
  </div>
</form>

@push('scripts')
<script>
function purchaseForm(){
  return {
    rows: [{product_id: '', qty: 1, price: 0, subtotal: 0}],
    freight_cost: 0,
    loading_cost: 0,
    unloading_cost: 0,
    init(){ this.recalc(0); },
    add(){ this.rows.push({product_id: '', qty: 1, price: 0, subtotal: 0}); },
    remove(i){ this.rows.splice(i,1); if(this.rows.length===0) this.add(); },
    recalc(i){ const r=this.rows[i]; r.subtotal=(Number(r.qty||0)*Number(r.price||0)); },
    total(){ return this.rows.reduce((s,r)=>s+Number(r.subtotal||0),0); },
    totalExtra(){ return (Number(this.freight_cost||0) + Number(this.loading_cost||0) + Number(this.unloading_cost||0)); }
  }
}
</script>
@endpush
@endsection
