@extends('layouts.app')

@section('content')
<div x-data="pos()" class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-3">
    <div class="p-3 bg-white border rounded-md flex items-center gap-2">
      <input x-model="keyword" @keydown.enter.prevent="search()" type="text" placeholder="Cari produk..." class="flex-1 px-3 py-2 border rounded-md">
      <button @click="search()" class="px-3 py-2 bg-gray-900 text-white rounded-md text-sm">Cari</button>
    </div>
    <div class="p-3 bg-white border rounded-md">
      <template x-if="results.length === 0"><div class="text-sm text-gray-500">Belum ada hasil.</div></template>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3" x-show="results.length">
        <template x-for="p in results" :key="p.id">
          <button @click="addToCart(p)" class="p-3 border rounded-md text-left hover:bg-gray-50">
            <div class="font-medium" x-text="p.name"></div>
            <div class="text-xs text-gray-500" x-text="'SKU: ' + (p.sku || '-')"></div>
          </button>
        </template>
      </div>
    </div>
  </div>
  <div class="space-y-3">
    <div class="p-3 bg-white border rounded-md">
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
                  <input type="number" step="0.01" class="w-24 border rounded px-2 py-1" x-model.number="it.price">
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
            <div class="text-right text-sm w-24" x-text="format(itemSubtotal(it))"></div>
            <button class="text-xs text-red-600" @click="cart.splice(idx,1)">Hapus</button>
          </div>
        </template>
      </div>
    </div>
    <div class="p-3 bg-white border rounded-md space-y-2">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-500">Total</div>
        <div class="font-semibold" x-text="format(total())"></div>
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
          <div class="font-medium" x-text="format(payTotal())"></div>
        </div>
        <div class="flex items-center justify-between text-sm">
          <div class="text-gray-500">Kembali</div>
          <div class="font-medium" x-text="format(Math.max(0, payTotal() - total()))"></div>
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
      this.results = data.data || [];
    },
    addToCart(p) {
      this.cart.push({ id: p.id, name: p.name, qty: 1, price: 0, discount: 0, source_location_id: null });
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
