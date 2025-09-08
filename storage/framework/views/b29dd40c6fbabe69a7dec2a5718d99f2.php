

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Adjustments</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage inventory adjustments and corrections</p>
    </div>
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stocks.adjust')): ?>
    <a href="<?php echo e(route('stock-adjustments.create')); ?>" class="btn-primary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        New Adjustment
    </a>
    <?php endif; ?>
</div>

<!-- Filters Card -->
<div class="card mb-6">
    <div class="card-body">
        <form method="get" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input name="q"
                       value="<?php echo e($q ?? ''); ?>"
                       placeholder="Search by code or note..."
                       class="form-input w-full"/>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Message -->
<?php if(session('ok')): ?>
<div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-xl">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <?php echo e(session('ok')); ?>

    </div>
</div>
<?php endif; ?>

<!-- Permission Notice -->
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('stocks.adjust')): ?>
<div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-xl">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        You don't have permission to create stock adjustments. Ask an admin to grant "stocks.adjust".
    </div>
</div>
<?php endif; ?>

<!-- Adjustments Table -->
<?php
$tableHeaders = ['Date', 'Code', 'Status'];
$tableRows = $adjustments->map(function($a) {
    return [
        'cells' => [
            [
                'type' => 'link',
                'url' => route('stock-adjustments.show', $a),
                'text' => $a->date
            ],
            [
                'type' => 'link',
                'url' => route('stock-adjustments.show', $a),
                'text' => $a->code
            ],
            [
                'type' => 'badge',
                'text' => ucfirst($a->status),
                'style' => $a->status === 'posted' ? 'success' : ($a->status === 'void' ? 'danger' : 'secondary')
            ]
        ],
        'actions' => collect([
            $a->status === 'draft' ? [
                'type' => 'link',
                'url' => route('stock-adjustments.edit', $a),
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            $a->status === 'draft' ? [
                'type' => 'button',
                'label' => 'Post',
                'style' => 'success',
                'onclick' => "document.getElementById('post-form-{$a->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ] : null,
            $a->status === 'posted' ? [
                'type' => 'button',
                'label' => 'Void',
                'style' => 'danger',
                'onclick' => "document.getElementById('void-form-{$a->id}').submit();",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
            ] : null
        ])->filter()->values()->toArray()
    ];
})->toArray();
?>

<?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => $tableHeaders,'rows' => $tableRows,'pagination' => $adjustments,'emptyMessage' => 'No adjustments found','emptyDescription' => 'Stock adjustments will appear here when you create inventory corrections.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableHeaders),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableRows),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($adjustments),'empty-message' => 'No adjustments found','empty-description' => 'Stock adjustments will appear here when you create inventory corrections.']); ?>
     <?php $__env->slot('empty-action', null, []); ?> 
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('stocks.adjust')): ?>
        <a href="<?php echo e(route('stock-adjustments.create')); ?>" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Your First Adjustment
        </a>
        <?php endif; ?>
     <?php $__env->endSlot(); ?>
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
<?php $__currentLoopData = $adjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($a->status === 'draft'): ?>
    <form id="post-form-<?php echo e($a->id); ?>" action="<?php echo e(route('stock-adjustments.post', $a)); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    <?php endif; ?>
    
    <?php if($a->status === 'posted'): ?>
    <form id="void-form-<?php echo e($a->id); ?>" action="<?php echo e(route('stock-adjustments.void', $a)); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
    </form>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Stock Adjustments'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/adjustments/index.blade.php ENDPATH**/ ?>