@extends('layouts.app')

@section('content')
<div x-data="pos()" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-3">
    <div class="p-3 bg-white border rounded-md flex flex-col gap-2 relative" x-data="{show:false}" @click.away="show=false">
      <div class="flex items-center gap-2">
        <input x-model="keyword" @input.debounce.300ms="search();show=true" @focus="show=true" type="text" placeholder="Cari produk..." class="flex-1 px-3 py-2 border rounded-md">
        <button @click="search();show=true" class="px-3 py-2 bg-gray-900 text-white rounded-md text-sm">Cari</button>
      </div>
      <template x-if="show && results.length">
  <ul class="absolute z-10 bg-white border rounded shadow mt-1 max-h-60 overflow-auto" style="width:100%">
          <template x-for="p in results" :key="p.id">
            <li @click="addToCart(p);show=false" class="px-3 py-2 cursor-pointer hover:bg-blue-50">
              <div class="font-medium" x-text="p.name"></div>
              <div class="text-xs text-gray-500" x-text="'SKU: ' + (p.sku || '-')"></div>
            </li>
          </template>
        </ul>
      </template>
      <template x-if="show && keyword && results.length === 0"><div class="text-sm text-gray-500 mt-1">Tidak ada hasil.</div></template>
    </div>
  </div>
  <div class="space-y-3">
    <div class="p-3 bg-white border rounded-md">
      <div class="mb-2">
        <label class="text-sm text-gray-600">Customer</label>
        <div x-data="{q:'',results:[],selected:null,show:false}" @click.away="show=false">
          <input type="text" class="w-full px-3 py-2 border rounded mb-1" placeholder="Cari customer..." x-model="q" @input.debounce.300ms="fetch('/api/customers?q='+encodeURIComponent(q)).then(r=>r.json()).then(data=>{results=data;show=true})">
          <template x-if="show && results.length">
            <ul class="bg-white border rounded shadow mt-1 max-h-40 overflow-auto" style="width:100%">
              <template x-for="item in results" :key="item.id">
                <li @click="selected=item;show=false" class="px-3 py-2 cursor-pointer hover:bg-blue-50" x-text="item.name"></li>
              </template>
            </ul>
          </template>
          <template x-if="show && q && results.length === 0">
            <div class="text-sm text-gray-500 mt-1 flex items-center gap-2">
              Tidak ditemukan. <button type="button" class="px-2 py-1 bg-blue-600 text-white rounded text-xs" @click="showModal=true">Tambah Customer Baru</button>
            </div>
          </template>
          <input type="hidden" name="customer_id" :value="selected?.id">
          <template x-if="selected"><div class="text-xs text-gray-500">Terpilih: <span x-text="selected.name"></span></div></template>

          <!-- Modal input customer baru -->
          <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow-lg p-6 w-80">
              <div class="text-lg font-semibold mb-2">Tambah Customer Baru</div>
              <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Nama" x-model="newCustomer.name"></div>
              <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Telepon" x-model="newCustomer.phone"></div>
              <div class="mb-2"><input type="text" class="w-full px-3 py-2 border rounded" placeholder="Alamat" x-model="newCustomer.address"></div>
              <div class="flex gap-2 justify-end mt-2">
                <button type="button" class="px-3 py-1 border rounded text-gray-600" @click="showModal=false">Batal</button>
                <button type="button" class="px-3 py-1 border rounded bg-blue-600 text-white" @click="
                  fetch('/customers', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                    },
                    body: JSON.stringify(newCustomer)
                  })
                  .then(r => r.json())
                  .then(data => {
                    if(data.id){ selected = data; showModal=false; q=data.name; results=[]; window.notify('Customer ditambahkan','success'); }
                    else { window.notify('Gagal tambah customer','error'); }
                  })
                  .catch(()=>window.notify('Gagal tambah customer','error'))
                ">Simpan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="font-semibold mb-2">Keranjang</div>
      <template x-if="cart.length === 0"><div class="text-sm text-gray-500">Kosong</div></template>
      <div class="space-y-2">
        <template x-for="(it,idx) in cart" :key="idx">
      <div class="flex items-center gap-2">
            <div class="flex-1">
              <div class="text-sm" x-text="it.name"></div>
        <div class="text-xs text-gray-600" x-show="it.source_location_id">Sumber: <span class="inline-block px-2 py-0.5 bg-gray-100 border rounded" x-text="(locations.find(l=>l.id==it.source_location_id)||{}).name||'Lokasi aktif'"></span></div>
              <div class="flex items-center gap-2 text-xs text-gray-600">
                <label class="flex items-center gap-1">Qty
                  <input type="number" step="0.001" class="w-20 border rounded px-2 py-1" x-model.number="it.qty">
                </label>
                <label class="flex items-center gap-1">Harga
                  <span class="w-24 px-2 py-1 border rounded bg-gray-100 text-gray-700 inline-block" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(it.price)"></span>
                </label>
                <label class="flex items-center gap-1">Diskon
                  <input type="number" step="0.01" class="w-20 border rounded px-2 py-1" x-model.number="it.discount">
                </label>
              </div>
              <div class="text-xs text-gray-500">Sumber
                <select class="border rounded px-2 py-1" x-model.number="it.source_location_id">
                  <option :value="null">Lokasi aktif</option>
                  <template x-for="loc in locations" :key="loc.id">
                    <option :value="loc.id" x-text="loc.name"></option>
                  </template>
                </select>
              </div>
            </div>
            <div class="text-right text-sm w-24">@component('components.currency', ['value' => 0])@endcomponent</div>
            <button class="text-xs text-red-600" @click="cart.splice(idx,1)">Hapus</button>
          </div>
        </template>
      </div>
    </div>
    <div class="p-3 bg-white border rounded-md space-y-2">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">Total</div>
  <div class="font-semibold">@component('components.currency', ['value' => 0])@endcomponent</div>
      </div>
      <div class="border-t pt-2 mt-2">
        <div class="text-sm font-medium mb-1">Pembayaran</div>
        <template x-for="(p, i) in payments" :key="i">
          <div class="flex items-center gap-2 mb-2">
            <select class="border rounded px-2 py-1 text-sm" x-model="p.type">
              <option value="cash">Cash</option>
              <option value="transfer">Transfer</option>
              <option value="card">Card</option>
              <option value="qris">QRIS</option>
            </select>
            <input type="number" step="0.01" class="w-32 border rounded px-2 py-1" x-model.number="p.amount" placeholder="Amount">
            <input type="text" class="flex-1 border rounded px-2 py-1" x-model="p.reference" placeholder="Ref">
            <button class="text-xs text-red-600" @click="payments.splice(i,1)">Hapus</button>
          </div>
        </template>
        <button class="text-xs text-blue-700" @click="payments.push({type:'cash',amount:0,reference:''})">+ Tambah Pembayaran</button>
        <div class="flex items-center justify-between text-sm mt-2">
          <div class="text-gray-500">Dibayar</div>
          <div class="font-medium">@component('components.currency', ['value' => 0])@endcomponent</div>
        </div>
        <div class="flex items-center justify-between text-sm">
          <div class="text-gray-500">Kembali</div>
          <div class="font-medium">@component('components.currency', ['value' => 0])@endcomponent</div>
        </div>
      </div>
      <button @click="checkout()" class="w-full py-2 bg-emerald-600 text-white rounded-md">Simpan Draft</button>
    </div>
  </div>
</div>

<script>
function pos() {
  return {
    keyword: '',
    results: [],
    cart: [],
    locations: [],
  payments: [],
    async search() {
      const url = new URL('/api/products', window.location.origin);
      if (this.keyword) url.searchParams.set('q', this.keyword);
      const r = await fetch(url);
      const data = await r.json();
  this.results = data || [];
    },
    addToCart(p) {
      this.cart.push({ id: p.id, name: p.name, qty: 1, price: Number(p.price) || 0, discount: 0, source_location_id: null });
    },
  itemSubtotal(it){ return (Number(it.price) - Number(it.discount || 0)) * Number(it.qty || 0); },
  total() { return this.cart.reduce((a, c) => a + this.itemSubtotal(c), 0); },
  payTotal(){ return this.payments.reduce((a,p)=>a+Number(p.amount||0),0); },
    format(v){ return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(v || 0); },
    async checkout() {
      // Validate stock availability
      const batch = this.cart.map(it => ({ product_id: it.id, location_id: it.source_location_id || null }));
      const check = await fetch('/api/stock/available-batch', { method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ items: batch }) });
      if (check.ok) {
        const resCheck = await check.json();
        for (let i=0; i<this.cart.length; i++) {
          const it = this.cart[i];
          const av = resCheck.data[i]?.available ?? 0;
          if (Number(it.qty) > av) { alert(`Stok tidak cukup untuk ${it.name}. Tersedia: ${av}`); return; }
        }
      }

      const payload = {
  invoice_no: 'DR-' + Date.now(),
        date: new Date().toISOString(),
  items: this.cart.map(it => ({ product_id: it.id, qty: it.qty, price: it.price, discount: it.discount, subtotal: this.itemSubtotal(it), source_location_id: it.source_location_id || null })),
        payments: this.payments,
      };
      const res = await fetch('/sales', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(payload) });
      if (res.ok) { this.cart = []; alert('Draft tersimpan'); } else { const t = await res.text(); alert('Gagal: ' + t); }
    },
    async init() {
      try { const r = await fetch('/api/locations'); this.locations = r.ok ? await r.json() : []; } catch {}
    }
  }
}
</script>
@endsection
