<!-- Partial: ledger content suitable for modal or full-page include -->
<!-- Expects $product, $entries, $locationId -->

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Ledger</h2>
        <p class="text-gray-600 dark:text-gray-400">
            Transaction history for <?php echo e($product->name); ?>

            <?php if($locationId): ?>
                <span class="text-sm opacity-75">(Location #<?php echo e($locationId); ?>)</span>
            <?php endif; ?>
        </p>
    </div>
    
</div>

<!-- Product Info Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xl font-bold">
            <?php echo e(strtoupper(substr($product->name, 0, 2))); ?>

        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($product->name); ?></h3>
            <p class="text-gray-600 dark:text-gray-400"><?php echo e($product->code ?? ''); ?></p>
            <?php if($locationId): ?>
                <span class="inline-block mt-2 px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                    Location #<?php echo e($locationId); ?>

                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Stock Ledger Table -->
<?php
$tableHeaders = ['Date', 'Reference', 'Qty Change', 'Balance', 'Cost/Unit', 'Total Cost', 'Note'];
$tableRows = $entries->map(function($e) {
    return [
        'cells' => [
            [
                'type' => 'date',
                'value' => $e->created_at,
                'time' => $e->created_at ? $e->created_at->format('H:i') : null
            ],
            [
                'type' => 'text',
                'text' => $e->ref_type . ' #' . $e->ref_id,
                'classes' => 'font-mono text-sm'
            ],
            [
                'type' => 'text',
                'text' => ($e->qty_change > 0 ? '+' : '') . number_format($e->qty_change),
                'classes' => 'text-right font-semibold ' . ($e->qty_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400')
            ],
            [
                'type' => 'text',
                'text' => number_format($e->balance_after),
                'classes' => 'text-right font-semibold text-gray-900 dark:text-white'
            ],
            [
                'type' => 'currency',
                'value' => $e->cost_per_unit_at_time,
                'formatted' => $e->cost_per_unit_at_time ? 'Rp ' . number_format($e->cost_per_unit_at_time, 0, ',', '.') : '-',
                'classes' => 'text-right'
            ],
            [
                'type' => 'currency',
                'value' => $e->total_cost_effect,
                'formatted' => $e->total_cost_effect ? 'Rp ' . number_format($e->total_cost_effect, 0, ',', '.') : '-',
                'classes' => 'text-right'
            ],
            [
                'type' => 'text',
                'text' => $e->note ?: '-',
                'classes' => 'text-sm text-gray-600 dark:text-gray-400'
            ]
        ]
    ];
})->toArray();
?>

<!-- Mobile Card View (visible on small screens) -->
<div class="lg:hidden space-y-4 p-4">
    <?php $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($e->created_at ? $e->created_at->format('M d, Y') : '-'); ?> <?php if($e->created_at): ?> <span class="text-xs text-gray-400"><?php echo e($e->created_at->format('H:i')); ?></span> <?php endif; ?></p>
                <h4 class="text-sm font-medium text-gray-900 dark:text-white mt-1"><?php echo e($e->ref_type ?? '-'); ?> #<?php echo e($e->ref_id ?? ''); ?></h4>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e($e->note ?: '-'); ?></p>
            </div>
            <div class="text-right ml-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Qty</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e(($e->qty_change > 0 ? '+' : '') . number_format($e->qty_change)); ?></p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Balance</p>
                <p class="text-md font-medium text-gray-900 dark:text-white"><?php echo e(number_format($e->balance_after)); ?></p>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Cost/Unit</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($e->cost_per_unit_at_time ? 'Rp ' . number_format($e->cost_per_unit_at_time, 0, ',', '.') : '-'); ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Total Cost</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($e->total_cost_effect ? 'Rp ' . number_format($e->total_cost_effect, 0, ',', '.') : '-'); ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- Desktop Table (hidden on mobile) -->
<div class="hidden lg:block">
    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => $tableHeaders,'rows' => $tableRows,'pagination' => $entries,'actions' => false,'emptyMessage' => 'No stock transactions found','emptyDescription' => 'Stock movement history for this product will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableHeaders),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableRows),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entries),'actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'empty-message' => 'No stock transactions found','empty-description' => 'Stock movement history for this product will appear here.']); ?>
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
</div>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/stocks/_ledger_content.blade.php ENDPATH**/ ?>