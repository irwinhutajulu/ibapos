
<?php $__env->startSection('content'); ?>
<div class="bg-white border rounded-md">
  <div class="p-3 border-b flex items-center justify-between">
    <div class="font-semibold">Edit Adjustment #<?php echo e($adjustment->id); ?></div>
    <a href="<?php echo e(route('stock-adjustments.show', $adjustment)); ?>" class="text-sm underline">Back</a>
  </div>
  <div class="p-3">
    <?php if(session('error')): ?>
      <div class="mb-2 text-red-600 text-sm"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="mb-3 text-red-600 text-sm">
        <ul class="list-disc pl-5">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($e); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('stock-adjustments.update', $adjustment)); ?>" x-data="editAdjForm(<?php echo e($adjustment->toJson()); ?>)">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <div class="grid md:grid-cols-2 gap-3">
        <div>
          <label class="block text-sm">Reason</label>
          <input type="text" name="reason" x-model="reason" class="w-full border rounded p-2" />
        </div>
        <div>
          <label class="block text-sm">Note</label>
          <input type="text" name="note" x-model="note" class="w-full border rounded p-2" />
        </div>
      </div>

      <div class="mt-4">
        <div class="flex items-center justify-between mb-2">
          <div class="font-medium">Items</div>
          <button type="button" class="px-3 py-1 border rounded" @click="addRow()">+ Add Row</button>
        </div>
        <div class="overflow-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left border-b">
                <th style="width: 40%">Product</th>
                <th>Qty +/-</th>
                <th>Unit Cost</th>
                <th>Note</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <template x-for="(row, idx) in rows" :key="idx">
                <tr class="border-b" :class="rowError(idx) ? 'bg-red-50' : ''">
                  <td class="align-top">
                    <div class="space-y-1">
                      <input type="hidden" :name="`items[${idx}][product_id]`" :value="row.product_id">
                      <input type="text" class="w-full border rounded p-1" placeholder="Search name/barcode" @input.debounce.300ms="searchProduct(idx, $event.target.value)" :value="row.product_name ?? ''">
                      <div class="border rounded max-h-40 overflow-auto" x-show="suggest[idx] && suggest[idx].length">
                        <template x-for="p in suggest[idx]">
                          <div class="px-2 py-1 hover:bg-gray-100 cursor-pointer" @click="selectProduct(idx, p)">
                            <span x-text="p.name"></span>
                            <span class="text-gray-500" x-text="p.barcode ? ' • '+p.barcode : ''"></span>
                          </div>
                        </template>
                      </div>
                      <div class="text-xs text-red-600" x-show="!row.product_id">Required</div>
                    </div>
                  </td>
                  <td class="align-top">
                    <input type="number" step="0.001" :name="`items[${idx}][qty_change]`" x-model="row.qty_change" class="w-full border rounded p-1" :class="!row.qty_change || row.qty_change == 0 ? 'border-red-400' : ''" />
                    <div class="text-xs text-red-600" x-show="!row.qty_change || row.qty_change == 0">Non-zero</div>
                  </td>
                  <td class="align-top">
                    <input type="number" step="0.0001" :name="`items[${idx}][unit_cost]`" x-model="row.unit_cost" class="w-full border rounded p-1" />
                  </td>
                  <td class="align-top">
                    <input type="text" :name="`items[${idx}][note]`" x-model="row.note" class="w-full border rounded p-1" />
                  </td>
                  <td class="align-top">
                    <button type="button" class="px-2 py-1 text-red-600" @click="removeRow(idx)">Remove</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>

      <div class="mt-4 flex items-center justify-end gap-2">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
function editAdjForm(adj) {
  return {
    reason: adj.reason || '',
    note: adj.note || '',
    rows: (adj.items || []).map(it => ({
      product_id: it.product_id,
      product_name: it.product?.name || ('#'+it.product_id),
      qty_change: it.qty_change,
      unit_cost: it.unit_cost,
      note: it.note || ''
    })),
    suggest: {},
    addRow() { this.rows.push({ product_id: '', product_name: '', qty_change: '', unit_cost: '', note: '' }); },
    removeRow(i) { this.rows.splice(i,1); if (this.rows.length===0) this.addRow(); },
    rowError(i){ const r=this.rows[i]; return !r.product_id || !r.qty_change || r.qty_change==0; },
    async searchProduct(idx, q) {
      if (!q || q.length < 2) { this.suggest[idx] = []; return; }
      try {
        const url = new URL('<?php echo e(route('api.products')); ?>', window.location.origin);
        url.searchParams.set('q', q);
        const res = await fetch(url);
        const data = await res.json();
        this.suggest[idx] = data.data || [];
      } catch(e) { this.suggest[idx] = []; }
    },
    selectProduct(idx, p) {
      this.rows[idx].product_id = p.id;
      this.rows[idx].product_name = p.name;
      this.suggest[idx] = [];
    }
  }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/adjustments/edit.blade.php ENDPATH**/ ?>