<?php $__env->startSection('content'); ?>
<div x-data="salesRealtime()" x-init="init()">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage your sales transactions</p>
        </div>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sales.create')): ?>
        <a href="<?php echo e(route('pos.index')); ?>" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Sale (POS)
        </a>
        <?php endif; ?>
    </div>

    <!-- Filters Card -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="form-label">Search</label>
                    <input name="q" 
                           value="<?php echo e($q ?? ''); ?>" 
                           placeholder="Invoice or customer" 
                           class="form-input"/>
                </div>
                
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <?php $__currentLoopData = ['draft','posted','void']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st); ?>" <?php if(($status ?? '')===$st): echo 'selected'; endif; ?>><?php echo e(ucfirst($st)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div>
                    <label class="form-label">From</label>
                    <input type="date" 
                           name="from" 
                           value="<?php echo e($dateFrom ?? ''); ?>" 
                           class="form-input"/>
                </div>
                
                <div>
                    <label class="form-label">To</label>
                    <input type="date" 
                           name="to" 
                           value="<?php echo e($dateTo ?? ''); ?>" 
                           class="form-input"/>
                </div>
                
                <div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filter
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

    <!-- Sales Table -->
    <?php
    $tableHeaders = ['Date', 'Invoice', 'Customer', 'Total', 'Status'];
    $tableRows = $sales->map(function($s) {
        return [
            'cells' => [
                [
                    'type' => 'date',
                    'value' => $s->date,
                    'formatted' => $s->date?->format('Y-m-d')
                ],
                [
                    'type' => 'link',
                    'url' => route('sales.show', $s),
                    'text' => $s->invoice_no
                ],
                $s->customer->name ?? '-',
                [
                    'type' => 'currency',
                    'value' => $s->total,
                    'formatted' => 'Rp ' . number_format($s->total, 0, ',', '.')
                ],
                [
                    'type' => 'badge',
                    'text' => ucfirst($s->status),
                    'style' => $s->status === 'posted' ? 'success' : ($s->status === 'void' ? 'danger' : 'secondary')
                ]
            ],
            'actions' => collect([
                $s->status === 'draft' ? [
                    'type' => 'button',
                    'label' => 'Post',
                    'style' => 'success',
                    'onclick' => "event.preventDefault(); if(confirm('Post this sale?')) { document.getElementById('post-form-{$s->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                ] : null,
                $s->status === 'draft' || $s->status === 'posted' ? [
                    'type' => 'button',
                    'label' => 'Void',
                    'style' => 'danger',
                    'onclick' => "event.preventDefault(); if(confirm('Void this sale?')) { document.getElementById('void-form-{$s->id}').submit(); }",
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
                ] : null
            ])->filter()->values()->toArray()
        ];
    })->toArray();
    ?>

    <?php if (isset($component)) { $__componentOriginal163c8ba6efb795223894d5ffef5034f5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal163c8ba6efb795223894d5ffef5034f5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table','data' => ['headers' => $tableHeaders,'rows' => $tableRows,'pagination' => $sales,'emptyMessage' => 'No sales found','emptyDescription' => 'Start selling by creating your first sale transaction.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableHeaders),'rows' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tableRows),'pagination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sales),'empty-message' => 'No sales found','empty-description' => 'Start selling by creating your first sale transaction.']); ?>
         <?php $__env->slot('empty-action', null, []); ?> 
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('sales.create')): ?>
            <a href="<?php echo e(route('pos.index')); ?>" class="btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Your First Sale
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
    <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($s->status === 'draft'): ?>
        <form id="post-form-<?php echo e($s->id); ?>" action="<?php echo e(route('sales.post', $s)); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
        <?php endif; ?>
        
        <?php if($s->status === 'draft' || $s->status === 'posted'): ?>
        <form id="void-form-<?php echo e($s->id); ?>" action="<?php echo e(route('sales.void', $s)); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function salesRealtime(){
  return {
    init(){
      if(!window.Echo || !window.appActiveLocationId) return;
      const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
      ch.listen('.sale.posted', (e)=>{
        window.notify(`Sale posted #${e.id} total ${e.total}`, 'success');
      }).listen('.sale.voided', (e)=>{
        window.notify(`Sale voided #${e.id}`, 'warning');
      }).listen('.stock.updated', (e)=>{
        window.notify(`Stock updated product ${e.product_id} qty ${e.qty}`, 'info');
      });
    }
  }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Sales'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/sales/index.blade.php ENDPATH**/ ?>