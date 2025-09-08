

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Stock Overview</h2>
        <p class="text-gray-600 dark:text-gray-400">Monitor your inventory levels and valuations</p>
    </div>
</div>

<!-- Live Search + Styled Table -->
<?php
    $searchUrl = route('stocks.index');
    $initialRows = $stocks->map(function($s) {
        $qty = number_format($s->qty, 0);
        $avg = 'Rp ' . number_format($s->avg_cost, 2, ',', '.');
        $valuation = 'Rp ' . number_format((float)$s->qty * (float)$s->avg_cost, 2, ',', '.');

        return [
            'id' => $s->id,
            'cells' => [
                [
                    'type' => 'avatar',
                    'image' => optional($s->product)->image_path ? url('storage/'.optional($s->product)->image_path) : null,
                    'name' => optional($s->product)->name ?? ('#'.$s->product_id),
                    'subtitle' => optional($s->product)->barcode ?? 'No Barcode'
                ],
                $qty,
                $avg,
                $valuation
            ],
                'actions' => [
                [
                    'type' => 'button',
                    // We will open the ledger inside a remote modal
                    'onclick' => "openRemoteModal('ledgerModal', '" . route('stocks.ledger', $s->product_id) . "', 'Ledger: ' + '" . addslashes(optional($s->product)->name ?? ('#'.$s->product_id)) . "')",
                    // Provide a direct URL as well so mobile cards can navigate to the full ledger page
                    'url' => route('stocks.ledger', $s->product_id),
                    'label' => 'View Ledger',
                    'style' => 'primary',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
                ]
            ]
        ];
    })->values();
?>

<?php $__env->startComponent('components.live-search-table', [
    'searchUrl' => $searchUrl,
    'searchParams' => [],
    'debounceMs' => 500,
    'minLength' => 0,
    'placeholder' => 'Search by product name or barcode...',
    'headers' => ['Product','Quantity','Avg Cost','Valuation'],
    'initialRows' => $initialRows,
    'initialPagination' => $stocks->toArray(),
    'emptyMessage' => 'No stock data found',
    'emptyDescription' => 'Stock information will appear here once you have products with inventory.'
]); ?>
<?php echo $__env->renderComponent(); ?>


<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'ledgerModal','title' => 'Ledger','size' => 'full','maxHeight' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'ledgerModal','title' => 'Ledger','size' => 'full','maxHeight' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
    
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Stock Overview'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/stocks/index.blade.php ENDPATH**/ ?>