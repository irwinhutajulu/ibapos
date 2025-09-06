@extends('layouts.app', ['title' => 'Realtime Debug'])

@section('content')
<div x-data="rt()" x-init="init()" class="space-y-3">
  <div class="text-sm text-gray-600">Active Location ID: <span x-text="loc"></span></div>
  <template x-if="!echoEnabled">
    <div class="text-sm text-red-600">Echo not initialized. Pastikan VITE_BROADCAST_ENABLED=true dan rebuild assets.</div>
  </template>
  <div class="flex items-center gap-2">
    <button @click="fire()" class="px-3 py-2 rounded bg-blue-600 text-white text-sm">Trigger Test Event</button>
  <button @click="fireSale('posted')" class="px-3 py-2 rounded bg-green-600 text-white text-sm">Trigger Sale Posted</button>
  <button @click="fireSale('voided')" class="px-3 py-2 rounded bg-red-600 text-white text-sm">Trigger Sale Voided</button>
    <span class="text-xs text-gray-500">Listen on private channel: <code x-text="`location.${loc}`"></code></span>
  </div>
  <div class="border rounded p-3 bg-white">
    <div class="text-xs text-gray-500 mb-2">Incoming events</div>
    <template x-for="m in messages" :key="m.id">
      <div class="text-sm"><span class="font-medium" x-text="m.type"></span>: <span x-text="m.payload"></span></div>
    </template>
    <template x-if="messages.length===0">
      <div class="text-xs text-gray-400">(Belum ada event)</div>
    </template>
  </div>
  <div class="text-xs" :class="statusColor" x-text="status"></div>
</div>
@endsection

@push('scripts')
<script>
function rt(){
  return {
    loc: window.appActiveLocationId ?? '-'
    ,messages: []
    ,status: ''
    ,statusColor: 'text-gray-400'
    ,echoEnabled: false
    ,init(){
      this.echoEnabled = !!window.Echo;
      if(!this.echoEnabled || !this.loc) return;
      const ch = window.Echo.private(`location.${this.loc}`);
      ch.listen('.sale.posted', e=>{ this.messages.unshift({id:Date.now()+Math.random(), type:'sale.posted', payload:JSON.stringify(e)}); window.notify(`Sale posted #${e.id}`, 'success'); })
        .listen('.sale.voided', e=>{ this.messages.unshift({id:Date.now()+Math.random(), type:'sale.voided', payload:JSON.stringify(e)}); window.notify(`Sale voided #${e.id}`, 'warning'); })
        .listen('.stock.updated', e=>{ this.messages.unshift({id:Date.now()+Math.random(), type:'stock.updated', payload:JSON.stringify(e)}); window.notify(`Stock updated product ${e.product_id}`, 'info'); });
    }
    ,async fire(){
      this.status = 'Triggering...'; this.statusColor='text-gray-400';
      try{
        const res = await fetch('{{ route('debug.fire') }}', {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
        if(!res.ok){
          const txt = await res.text();
          this.status = `Failed (${res.status}): ${txt.substring(0,120)}`; this.statusColor='text-red-600';
          return;
        }
  this.status = 'Triggered OK'; this.statusColor='text-green-600'; window.notify('StockUpdated sent', 'success');
      }catch(err){
        this.status = 'Error: '+(err?.message || err); this.statusColor='text-red-600';
      }
    }
    ,async fireSale(kind){
      this.status = `Triggering sale ${kind}...`; this.statusColor='text-gray-400';
      const url = kind==='posted' ? '{{ route('debug.sale-posted') }}' : '{{ route('debug.sale-voided') }}';
      try{
        const res = await fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}})
        if(!res.ok){
          const txt = await res.text();
          this.status = `Failed (${res.status}): ${txt.substring(0,120)}`; this.statusColor='text-red-600';
          return;
        }
  this.status = `Sale ${kind} triggered`; this.statusColor='text-green-600'; window.notify(`Sale ${kind} event sent`, 'success');
      }catch(err){
        this.status = 'Error: '+(err?.message || err); this.statusColor='text-red-600';
      }
    }
  }
}
</script>
@endpush