
@extends('layouts.app', ['title' => 'Edit Purchase '.$purchase->invoice_no])

@section('content')
<form method="post" action="{{ route('purchases.update', $purchase) }}" x-data='purchaseForm(@json($purchase->items->map(fn($i)=>["product_id"=>$i->product_id,"qty"=>(float)$i->qty,"price"=>(float)$i->price])))' x-init="init()" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
  @csrf
  @method('PUT')
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Invoice No</label>
      <input name="invoice_no" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" value="{{ $purchase->invoice_no }}" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Date</label>
      <input type="datetime-local" name="date" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" value="{{ $purchase->date?->format('Y-m-d\TH:i') }}" required>
    </div>
    <div class="md:col-span-2">
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Supplier</label>
      <select name="supplier_id" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white" required>
        @foreach($suppliers as $s)
          <option value="{{ $s->id }}" @selected($purchase->supplier_id===$s->id)>{{ $s->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 py-4">
    <div>
      <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Freight Cost</label>
      <input type="number" name="freight_cost" x-model.number="freight_cost" class="w-60 px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm bg-gray-700 dark:bg-gray-700 text-white text-right" value="{{ $purchase->freight_cost ?? 0 }}">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-300 mb-1">Loading Cost</label>
      <input type="number" name="loading_cost" x-model.number="loading_cost" class="w-60 px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white text-right" value="{{ $purchase->loading_cost ?? 0 }}">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-300 mb-1">Unloading Cost</label>
      <input type="number" name="unloading_cost" x-model.number="unloading_cost" class="w-60 px-3 py-2 border border-gray-600 rounded-lg bg-gray-700 text-white text-right" value="{{ $purchase->unloading_cost ?? 0 }}">
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
        <template x-for="(row, idx) in rows" :key="row.uid">
          <tr class="border-t border-gray-700">
            <td class="px-3 py-2">
              <select :name="`items[${idx}][product_id]`" class="w-64 px-2 py-1 border border-gray-600 rounded-lg bg-gray-700 text-white" x-model.number="row.product_id" required>
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
      Save Changes
    </button>
    <a href="{{ route('purchases.show', $purchase) }}" class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Cancel</a>
  </div>
</form>

@push('scripts')
<script>
function purchaseForm(initialRows){
  return {
    // ensure each row has a stable unique id to use as key (prevents DOM reuse issues)
    rows: (initialRows || [{product_id: '', qty: 1, price: 0, subtotal: 0}]).map(r => ({...r, uid: r.uid || (Date.now().toString(36) + Math.random().toString(36).slice(2))})),
    freight_cost: {{ $purchase->freight_cost ?? 0 }},
    loading_cost: {{ $purchase->loading_cost ?? 0 }},
    unloading_cost: {{ $purchase->unloading_cost ?? 0 }},
    init(){ this.rows.forEach((_,i)=>this.recalc(i)); },
    add(){ this.rows.push({product_id: '', qty: 1, price: 0, subtotal: 0, uid: (Date.now().toString(36) + Math.random().toString(36).slice(2))}); },
    remove(i){ this.rows.splice(i,1); if(this.rows.length===0) this.add(); },
    recalc(i){ const r=this.rows[i]; r.subtotal=(Number(r.qty||0)*Number(r.price||0)); },
    total(){ return this.rows.reduce((s,r)=>s+Number(r.subtotal||0),0); },
    totalExtra(){ return (Number(this.freight_cost||0) + Number(this.loading_cost||0) + Number(this.unloading_cost||0)); }
  }
}
</script>
@endpush
@endsection
