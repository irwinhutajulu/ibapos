@extends('layouts.app', ['title' => 'New Purchase'])

@section('content')
<form method="post" action="{{ route('purchases.store') }}" x-data="purchaseForm()" x-init="init()" class="bg-white border rounded-md p-4">
  @csrf
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <label class="text-sm text-gray-600">Invoice No</label>
      <input name="invoice_no" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="text-sm text-gray-600">Date</label>
      <input type="datetime-local" name="date" class="w-full px-3 py-2 border rounded" required>
    </div>
    <div>
      <label class="text-sm text-gray-600">Lokasi</label>
      <select name="location_id" class="w-full px-3 py-2 border rounded" required>
        <option value="">-- Pilih Lokasi --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}">{{ $loc->name }}</option>
        @endforeach
      </select>
    </div>
    <div x-data="{q:'',results:[],selected:null,show:false}" @click.away="show=false">
      <label class="text-sm text-gray-600">Supplier</label>
      <input type="text" class="w-full px-3 py-2 border rounded mb-1" placeholder="Cari supplier..." x-model="q" @input.debounce.300ms="fetch('/api/suppliers?q='+encodeURIComponent(q)).then(r=>r.json()).then(data=>{results=data;show=true})">
      <template x-if="show && results.length">
        <ul class="bg-white border rounded shadow mt-1 max-h-40 overflow-auto">
          <template x-for="item in results" :key="item.id">
            <li @click="selected=item;show=false" class="px-3 py-2 cursor-pointer hover:bg-blue-50" x-text="item.name"></li>
          </template>
        </ul>
      </template>
      <template x-if="show && q && results.length === 0">
        <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
          Tidak ditemukan. <button type="button" class="px-2 py-1 bg-blue-600 text-white rounded text-xs" @click="showModal=true">Tambah Supplier Baru</button>
        </div>
      </template>
      <input type="hidden" name="supplier_id" :value="selected?.id">
      <template x-if="selected"><div class="text-xs text-gray-500">Terpilih: <span x-text="selected.name"></span></div></template>

      <!-- Modal input supplier baru -->
      <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
        <div class="bg-white rounded shadow-lg p-6 w-80">
          <div class="text-lg font-semibold mb-2">Tambah Supplier Baru</div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Nama" x-model="newSupplier.name"></div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Telepon" x-model="newSupplier.phone"></div>
          <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Alamat" x-model="newSupplier.address"></div>
          <div class="flex gap-2 justify-end mt-2">
            <button type="button" class="px-3 py-1 border rounded text-gray-600" @click="showModal=false">Batal</button>
            <button type="button" class="px-3 py-1 border rounded bg-blue-600 text-white" @click="
              fetch('/suppliers', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                },
                body: JSON.stringify(newSupplier)
              })
              .then(r => r.json())
              .then(data => {
                if(data.id){ selected = data; showModal=false; q=data.name; results=[]; window.notify('Supplier ditambahkan','success'); }
                else { window.notify('Gagal tambah supplier','error'); }
              })
              .catch(()=>window.notify('Gagal tambah supplier','error'))
            ">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <label class="text-sm text-gray-600">Freight Cost</label>
    <input type="number" step="0.01" name="freight_cost" class="w-60 px-3 py-2 border rounded" placeholder="0.00">
  </div>

  <div class="mt-6 overflow-x-auto border rounded">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
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
          <tr class="border-t">
            <td class="px-3 py-2">
              <select :name="`items[${idx}][product_id]`" class="w-64 px-2 py-1 border rounded" x-model.number="row.product_id" required>
                <option value="">-- Select product --</option>
                @foreach($products as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </td>
            <td class="px-3 py-2 text-right">
              <input type="number" step="0.001" min="0.001" class="w-28 px-2 py-1 border rounded text-right" :name="`items[${idx}][qty]`" x-model.number="row.qty" @input="recalc(idx)" required>
            </td>
            <td class="px-3 py-2 text-right">
              <input type="number" step="0.01" min="0" class="w-28 px-2 py-1 border rounded text-right" :name="`items[${idx}][price]`" x-model.number="row.price" @input="recalc(idx)" required>
            </td>
            <td class="px-3 py-2 text-right" x-text="row.subtotal.toFixed(2)"></td>
            <td class="px-3 py-2 text-right">
              <button type="button" class="text-red-700" @click="remove(idx)">Remove</button>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
  <div class="mt-2"><button type="button" class="px-3 py-2 border rounded" @click="add()">Add Item</button></div>

  <div class="mt-6 flex items-center gap-4">
    <div class="ml-auto text-right">
      <div class="text-sm text-gray-600">Total</div>
      <div class="text-lg font-semibold" x-text="total().toFixed(2)"></div>
    </div>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Draft</button>
    <a href="{{ route('purchases.index') }}" class="underline text-gray-600">Cancel</a>
  </div>
</form>

@push('scripts')
<script>
function purchaseForm(){
  return {
    rows: [{product_id: '', qty: 1, price: 0, subtotal: 0}],
    init(){ this.recalc(0); },
    add(){ this.rows.push({product_id: '', qty: 1, price: 0, subtotal: 0}); },
    remove(i){ this.rows.splice(i,1); if(this.rows.length===0) this.add(); },
    recalc(i){ const r=this.rows[i]; r.subtotal=(Number(r.qty||0)*Number(r.price||0)); },
    total(){ return this.rows.reduce((s,r)=>s+Number(r.subtotal||0),0); },
  }
}
</script>
@endpush
@endsection
