@extends('')
{{-- Partial: adjustments form --}}
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900">{{ isset($adjustment) ? 'Edit Adjustment' : 'New Adjustment' }}</h2>
        <p class="text-gray-600">Use this form to create or update a stock adjustment.</p>
    </div>

    <form method="POST" action="{{ isset($adjustment) ? route('stock-adjustments.update', $adjustment->id) : route('stock-adjustments.store') }}" class="space-y-6">
        @csrf
        @if(isset($adjustment)) @method('PUT') @endif
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm text-gray-600">Code (optional)</label>
                <input type="text" name="code" class="w-full mt-1 form-input" value="{{ $adjustment->code ?? old('code') }}">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600">Date</label>
                    <input type="datetime-local" name="date" class="w-full mt-1 form-input" value="{{ isset($adjustment) && $adjustment->date ? $adjustment->date->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i') }}">
                </div>

                <div>
                    <label class="block text-sm text-gray-600">Location</label>
                    <x-select-dropdown name="location_id" :items="$locations" :selected="isset($adjustment)?$adjustment->location_id:null" display="name" value="id" placeholder="-- Select Location --" />
                </div>
            </div>

            <div>
                <label class="block text-sm text-gray-600">Reason</label>
                @php $reasons = [''=>'-- Select Reason --','cycle_count'=>'Cycle count','damage'=>'Damage','spoilage'=>'Spoilage','theft'=>'Theft','other'=>'Other']; @endphp
                <x-select-dropdown name="reason" :items="$reasons" :selected="isset($adjustment)?$adjustment->reason:null" />
            </div>

            <div>
                <label class="block text-sm text-gray-600">Header Note</label>
                <textarea name="note" rows="2" class="w-full mt-1 form-textarea">{{ $adjustment->note ?? old('note') }}</textarea>
            </div>

            <fieldset class="border border-gray-200 rounded p-4">
                <legend class="text-sm font-medium">Items</legend>

                <div id="items-wrapper" class="space-y-3 mt-3">
                    @php
                        $oldItems = old('items', null);
                        $initial = [];
                        if(isset($adjustment) && isset($adjustment->items)) {
                            $initial = $adjustment->items->toArray();
                        } elseif(is_array($oldItems)) {
                            $initial = $oldItems;
                        } else {
                            $initial = [ ['product_id'=>old('product_id'),'qty_change'=>old('qty'),'unit_cost'=>old('unit_cost'),'note'=>old('item_note')] ];
                        }
                    @endphp

                    @foreach($initial as $idx => $it)
                        <div class="grid grid-cols-6 gap-3 items-center item-row">
                            <div class="col-span-2">
                                <label class="block text-sm text-gray-600">Product</label>
                                <x-select-dropdown name="items[{{ $idx }}][product_id]" :items="$products" :selected="isset($it['product_id'])?$it['product_id']:null" display="name" value="id" placeholder="-- Select Product --" />
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600">Qty Change</label>
                                <input type="number" name="items[{{ $idx }}][qty_change]" step="0.001" class="w-full mt-1 form-input" value="{{ $it['qty_change'] ?? 0 }}">
                            </div>

                            <div>
                                <label class="block text-sm text-gray-600">Unit Cost</label>
                                <input type="number" name="items[{{ $idx }}][unit_cost]" step="0.0001" class="w-full mt-1 form-input" value="{{ $it['unit_cost'] ?? '' }}">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm text-gray-600">Item Note</label>
                                <input type="text" name="items[{{ $idx }}][note]" class="w-full mt-1 form-input" value="{{ $it['note'] ?? '' }}">
                            </div>

                            <div class="flex items-end">
                                <button type="button" class="remove-item text-red-600">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <button type="button" id="add-item" class="px-3 py-1 bg-gray-100 border rounded-md">Add item</button>
                </div>
            </fieldset>

            <input type="hidden" name="status" value="draft">

            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ route('stock-adjustments.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">{{ isset($adjustment) ? 'Save Changes' : 'Create Adjustment' }}</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    (function(){
        const wrapper = document.getElementById('items-wrapper');
        const addBtn = document.getElementById('add-item');
        function makeRow(index){
            const div = document.createElement('div');
            div.className = 'grid grid-cols-6 gap-3 items-center item-row';
            div.innerHTML = `
                <div class="col-span-2">
                    <label class="block text-sm text-gray-600">Product</label>
                            <select name="items[${index}][product_id]" required class="w-full px-3 py-2 border rounded-md mt-1">
                                <option value="">-- Select Product --</option>
                                @foreach($products as $p)
                                    <option value="{{ data_get($p,'id') }}">{{ data_get($p,'name') }}@if(data_get($p,'code')) ({{ data_get($p,'code') }})@endif</option>
                                @endforeach
                            </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Qty Change</label>
                    <input type="number" name="items[${index}][qty_change]" step="0.001" class="w-full mt-1 form-input" value="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Unit Cost</label>
                    <input type="number" name="items[${index}][unit_cost]" step="0.0001" class="w-full mt-1 form-input" value="">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm text-gray-600">Item Note</label>
                    <input type="text" name="items[${index}][note]" class="w-full mt-1 form-input" value="">
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-item text-red-600">Remove</button>
                </div>
            `;
            return div;
        }

        addBtn.addEventListener('click', function(){
            const idx = wrapper.querySelectorAll('.item-row').length;
            wrapper.appendChild(makeRow(idx));
        });

        wrapper.addEventListener('click', function(e){
            if(e.target && e.target.classList.contains('remove-item')){
                const row = e.target.closest('.item-row');
                if(row) row.remove();
            }
        });
    })();
</script>
@endpush
@php
// Partial: adjustments form
// ...existing code...
@endphp
@includeWhen(true, 'adjustments.form')
