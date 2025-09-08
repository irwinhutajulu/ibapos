

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl bg-white border rounded-md p-4">
  <div class="flex items-start justify-between gap-4">
    <div class="flex-1">
      <div class="flex items-center justify-between">
        <div class="font-semibold text-lg">Adjustment #<?php echo e($adjustment->id); ?> <span class="ml-2 text-xs px-2 py-0.5 rounded bg-gray-100"><?php echo e($adjustment->status); ?></span></div>
        <a class="ml-4 text-gray-600 underline" href="<?php echo e(route('stock-adjustments.index')); ?>">Back</a>
      </div>

      <div class="mt-4 grid md:grid-cols-3 gap-3 text-sm">
        <div>
          <div class="text-gray-500">Date</div>
          <div><?php echo e($adjustment->date); ?></div>
        </div>
        <div>
          <div class="text-gray-500">Reason</div>
          <div><?php echo e($adjustment->reason ?? '-'); ?></div>
        </div>
        <div>
          <div class="text-gray-500">Note</div>
          <div><?php echo e($adjustment->note ?? '-'); ?></div>
        </div>
      </div>

      <div class="mt-4 overflow-auto">
        <table class="w-full">
          <thead>
            <tr class="text-left border-b">
              <th>Product</th>
              <th class="text-right">Qty +/-</th>
              <th class="text-right">Unit Cost</th>
              <th>Note</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $adjustment->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-b">
              <td><?php echo e($it->product->name ?? ('#'.$it->product_id)); ?></td>
              <td class="text-right"><?php echo e($it->qty_change); ?></td>
              <td class="text-right"><?php echo e($it->unit_cost ?? '-'); ?></td>
              <td><?php echo e($it->note); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>

      <div class="mt-4 flex items-center gap-2">
        <?php if($adjustment->status==='draft'): ?>
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stocks.adjust')): ?>
            <a class="px-3 py-2 border rounded-md" href="<?php echo e(route('stock-adjustments.edit', $adjustment)); ?>">Edit</a>
            <form method="POST" action="<?php echo e(route('stock-adjustments.post', $adjustment)); ?>" class="inline"><?php echo csrf_field(); ?><button class="px-3 py-2 bg-green-600 text-white rounded-md">Post</button></form>
          <?php endif; ?>
        <?php endif; ?>
        <?php if($adjustment->status==='posted'): ?>
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stocks.adjust')): ?>
            <form method="POST" action="<?php echo e(route('stock-adjustments.void', $adjustment)); ?>" class="inline"><?php echo csrf_field(); ?><button class="px-3 py-2 bg-red-600 text-white rounded-md">Void</button></form>
          <?php endif; ?>
        <?php endif; ?>
        <a href="<?php echo e(route('stock-adjustments.index')); ?>" class="ml-auto text-gray-600 underline">Back to list</a>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Stock Adjustment'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/adjustments/show.blade.php ENDPATH**/ ?>