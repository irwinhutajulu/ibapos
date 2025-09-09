
<?php $__env->startSection('content'); ?>
<?php if(session('error') || $errors->any()): ?>
<div class="border rounded-md">
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
  </div>
</div>
<?php endif; ?>

<?php echo $__env->make('adjustments.partials._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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