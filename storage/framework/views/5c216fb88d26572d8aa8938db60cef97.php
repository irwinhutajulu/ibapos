
<div class="max-w-3xl mx-auto">
    <div class="relative transform overflow-hidden rounded-xl bg-gray-800 dark:bg-gray-800 shadow-2xl border border-gray-700 dark:border-gray-600 w-full">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-600 dark:border-gray-600 bg-gray-700 dark:bg-gray-700">
            <h3 class="text-lg font-semibold text-white" data-modal-title><?php echo e(isset($adjustment) ? 'Edit Adjustment' : 'New Adjustment'); ?></h3>
            <p class="text-sm text-gray-300">Use this form to create or update a stock adjustment.</p>
        </div>

        <!-- Body -->
        <div class="p-4 bg-gray-800 dark:bg-gray-800 text-white" data-modal-body>
            <form method="POST" action="<?php echo e(isset($adjustment) ? route('stock-adjustments.update', $adjustment->id) : route('stock-adjustments.store')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php if(isset($adjustment)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Code (optional)</label>
                <input type="text" name="code" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" value="<?php echo e($adjustment->code ?? old('code')); ?>">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Date</label>
                    <input type="datetime-local" name="date" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" value="<?php echo e(isset($adjustment) && $adjustment->date ? $adjustment->date->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i')); ?>">
                </div>

                <div>
                        <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Location</label>
                        <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'location_id','options' => collect(
                            $locations ?? []
                        )->map(fn($l) => ['value' => data_get($l,'id'), 'label' => data_get($l,'name')])->toArray(),'value' => isset($adjustment) ? $adjustment->location_id : null,'placeholder' => '-- Select Location --']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'location_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect(
                            $locations ?? []
                        )->map(fn($l) => ['value' => data_get($l,'id'), 'label' => data_get($l,'name')])->toArray()),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($adjustment) ? $adjustment->location_id : null),'placeholder' => '-- Select Location --']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Reason</label>
                <?php $reasons = ['cycle_count'=>'Cycle count','damage'=>'Damage','spoilage'=>'Spoilage','theft'=>'Theft','other'=>'Other']; ?>
                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'reason','options' => collect($reasons)->map(fn($label,$key) => ['value' => $key, 'label' => $label])->toArray(),'value' => isset($adjustment) ? $adjustment->reason : null,'placeholder' => '-- Select Reason --']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'reason','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect($reasons)->map(fn($label,$key) => ['value' => $key, 'label' => $label])->toArray()),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($adjustment) ? $adjustment->reason : null),'placeholder' => '-- Select Reason --']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Catatan</label>
                <textarea name="note" rows="2" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white"><?php echo e($adjustment->note ?? old('note')); ?></textarea>
            </div>

            <fieldset class="border border-gray-200 rounded p-4">
                <legend class="text-sm font-medium">Items</legend>

                <div id="items-wrapper" class="space-y-3 mt-3">
                    <?php
                        $oldItems = old('items', null);
                        $initial = [];
                        if(isset($adjustment) && isset($adjustment->items)) {
                            $initial = $adjustment->items->toArray();
                        } elseif(is_array($oldItems)) {
                            $initial = $oldItems;
                        } else {
                            $initial = [ ['product_id'=>old('product_id'),'qty_change'=>old('qty'),'unit_cost'=>old('unit_cost'),'note'=>old('item_note')] ];
                        }
                    ?>

                    <?php $__currentLoopData = $initial; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="grid grid-cols-6 gap-3 items-center item-row">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Product</label>
                                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'items['.e($idx).'][product_id]','options' => collect(
                                    $products ?? []
                                )->map(fn($p) => ['value' => data_get($p,'id'), 'label' => data_get($p,'name')])->toArray(),'value' => isset($it['product_id']) ? $it['product_id'] : null,'placeholder' => '-- Select Product --']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'items['.e($idx).'][product_id]','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(collect(
                                    $products ?? []
                                )->map(fn($p) => ['value' => data_get($p,'id'), 'label' => data_get($p,'name')])->toArray()),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(isset($it['product_id']) ? $it['product_id'] : null),'placeholder' => '-- Select Product --']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Qty Change</label>
                                <input type="number" name="items[<?php echo e($idx); ?>][qty_change]" step="0.001" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" value="<?php echo e($it['qty_change'] ?? 0); ?>">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Unit Cost</label>
                                <input type="number" name="items[<?php echo e($idx); ?>][unit_cost]" step="0.0001" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" value="<?php echo e($it['unit_cost'] ?? ''); ?>">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Item Note</label>
                                <input type="text" name="items[<?php echo e($idx); ?>][note]" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white" value="<?php echo e($it['note'] ?? ''); ?>">
                            </div>

                            <div class="flex items-end">
                                <button type="button" class="remove-item text-red-600">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-3">
                    <button type="button" id="add-item" class="px-3 py-1 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600">Add item</button>
                </div>
            </fieldset>

            <input type="hidden" name="status" value="draft">

            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="<?php echo e(route('stock-adjustments.index')); ?>" class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600">Cancel</a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg"><?php echo e(isset($adjustment) ? 'Save Changes' : 'Create Adjustment'); ?></button>
            </div>
            </form>
        </div>

    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
                            <select name="items[${index}][product_id]" required class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white mt-1">
                                <option value="">-- Select Product --</option>
                                <?php $__currentLoopData = collect($products ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e(data_get($p,'id')); ?>"><?php echo e(data_get($p,'name')); ?><?php if(data_get($p,'code')): ?> (<?php echo e(data_get($p,'code')); ?>)<?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Qty Change</label>
                    <input type="number" name="items[${index}][qty_change]" step="0.001" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white mt-1" value="0">
                </div>
                <div>
                    <label class="block text-sm text-gray-600">Unit Cost</label>
                    <input type="number" name="items[${index}][unit_cost]" step="0.0001" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white mt-1" value="">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm text-gray-600">Item Note</label>
                    <input type="text" name="items[${index}][note]" class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white mt-1" value="">
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-item text-red-400">Remove</button>
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
<?php $__env->stopPush(); ?>

<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/adjustments/partials/_form.blade.php ENDPATH**/ ?>