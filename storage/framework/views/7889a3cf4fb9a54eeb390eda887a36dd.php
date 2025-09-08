

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Mutations</h2>
        <p class="text-gray-600 dark:text-gray-400">Track stock movements between locations</p>
    </div>
</div>

<!-- Mutations Table -->
<?php
$tableHeaders = ['Date', 'Product', 'From', 'To', 'Quantity', 'Status'];
$tableRows = $mutations->map(function($m) {
    return [
        'cells' => [
            $m->date,
            $m->product->name ?? ('#' . $m->product_id),
            'Location #' . $m->from_location_id,
            'Location #' . $m->to_location_id,
            [
                'type' => 'text',
                'text' => number_format($m->qty, 0),
                'align' => 'right'
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($m->status),
                'style' => $m->status === 'confirmed' ? 'success' : ($m->status === 'rejected' ? 'danger' : 'warning')
            ]
        ],
        'actions' => $m->status === 'pending' ? collect([
            [
                'type' => 'button',
                'label' => 'Confirm',
                'style' => 'success',
                'onclick' => "document.getElementById('confirm-form-{$m->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            ],
            [
                'type' => 'button',
                'label' => 'Reject',
                'style' => 'danger',
                'onclick' => "document.getElementById('reject-form-{$m->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            ]
        ])->toArray() : []
    ];
})->toArray();
?>

<?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => $tableHeaders,'rows' => $tableRows,'pagination' => $mutations,'emptyMessage' => 'No mutations found','emptyDescription' => 'Stock mutations will appear here when products are moved between locations.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableHeaders),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableRows),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mutations),'empty-message' => 'No mutations found','empty-description' => 'Stock mutations will appear here when products are moved between locations.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $attributes = $__attributesOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__attributesOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal163c8ba6efb795223894d5ffef5034f5)): ?>
<?php $component = $__componentOriginal163c8ba6efb795223894d5ffef5034f5; ?>
<?php unset($__componentOriginal163c8ba6efb795223894d5ffef5034f5); ?>
<?php endif; ?>

<!-- Hidden Forms for Actions -->
<?php $__currentLoopData = $mutations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($m->status === 'pending'): ?>
    <form id="confirm-form-<?php echo e($m->id); ?>" action="<?php echo e(route('stock-mutations.confirm', $m)); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    
    <form id="reject-form-<?php echo e($m->id); ?>" action="<?php echo e(route('stock-mutations.reject', $m)); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Stock Mutations'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/mutations/index.blade.php ENDPATH**/ ?>