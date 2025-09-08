<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product' => null,
    'categories' => [],
    'action' => '',
    'method' => 'POST',
    'mode' => 'create' // create, edit, show
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'product' => null,
    'categories' => [],
    'action' => '',
    'method' => 'POST',
    'mode' => 'create' // create, edit, show
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$isEdit = $mode === 'edit';
$isShow = $mode === 'show';
$isCreate = $mode === 'create';
$readonly = $isShow ? 'readonly' : '';
$disabled = $isShow ? 'disabled' : '';
?>

<form action="<?php echo e($action); ?>" method="POST" enctype="multipart/form-data" 
      <?php if(!$isShow): ?> onsubmit="return validateProductForm()" <?php endif; ?>>
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Product Image -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Product Image
            </label>
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <img id="image-preview" 
                         src="<?php echo e($product ? ($product->image_url ?? asset('images/default-product.svg')) : asset('images/default-product.svg')); ?>" 
                         alt="Product preview" 
                         class="w-20 h-20 rounded-lg object-cover border border-gray-600 dark:border-gray-600">
                </div>
                <?php if(!$isShow): ?>
                <div class="flex-1">
                    <input type="file" 
                           name="image" 
                           id="image-input"
                           accept="image/*"
                           class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                           onchange="previewImage(this)">
                    <p class="text-xs text-gray-400 dark:text-gray-400 mt-1">PNG, JPG, GIF up to 2MB</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Name -->
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Product Name <span class="text-red-400">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name"
                   value="<?php echo e(old('name', $product->name ?? '')); ?>" 
                   <?php echo e($readonly); ?>

                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white <?php if($isShow): ?> bg-gray-600 dark:bg-gray-600 <?php endif; ?>"
                   placeholder="Enter product name"
                   <?php if(!$isShow): ?> required <?php endif; ?>>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Barcode -->
        <div>
            <label for="barcode" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Barcode
            </label>
            <input type="text" 
                   name="barcode" 
                   id="barcode"
                   value="<?php echo e(old('barcode', $product->barcode ?? '')); ?>" 
                   <?php echo e($readonly); ?>

                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white <?php if($isShow): ?> bg-gray-600 dark:bg-gray-600 <?php endif; ?>"
                   placeholder="Enter barcode">
            <?php $__errorArgs = ['barcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Unit -->
        <div>
            <label for="unit" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Unit
            </label>
            <input type="text" 
                   name="unit" 
                   id="unit"
                   value="<?php echo e(old('unit', $product->unit ?? '')); ?>" 
                   <?php echo e($readonly); ?>

                   class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white <?php if($isShow): ?> bg-gray-600 dark:bg-gray-600 <?php endif; ?>"
                   placeholder="pcs, kg, liter, etc.">
            <?php $__errorArgs = ['unit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Category -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Category <span class="text-red-400">*</span>
            </label>
            <select name="category_id" 
                    id="category_id"
                    <?php echo e($disabled); ?>

                    class="w-full px-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white <?php if($isShow): ?> bg-gray-600 dark:bg-gray-600 <?php endif; ?>"
                    <?php if(!$isShow): ?> required <?php endif; ?>>
                <option value="">Select Category</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($id); ?>" <?php echo e(old('category_id', $product->category_id ?? '') == $id ? 'selected' : ''); ?>>
                        <?php echo e($name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Weight -->
        <div>
            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Weight (kg)
            </label>
            <input type="number" 
                   name="weight" 
                   id="weight"
                   value="<?php echo e(old('weight', $product->weight ?? '')); ?>" 
                   <?php echo e($readonly); ?>

                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white <?php if($isShow): ?> bg-gray-50 dark:bg-gray-600 <?php endif; ?>"
                   placeholder="0.000"
                   step="0.001"
                   min="0">
            <?php $__errorArgs = ['weight'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        
        <!-- Price -->
        <div>
            <label for="price" class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">
                Price <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-400 dark:text-gray-400">Rp</span>
                <input type="number" 
                       name="price" 
                       id="price"
                       value="<?php echo e(old('price', $product->price ?? '')); ?>" 
                       <?php echo e($readonly); ?>

                       class="w-full pl-10 pr-3 py-2 border border-gray-600 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-700 dark:bg-gray-700 text-white <?php if($isShow): ?> bg-gray-600 dark:bg-gray-600 <?php endif; ?>"
                       placeholder="0"
                       min="0"
                       step="1"
                       <?php if(!$isShow): ?> required <?php endif; ?>>
            </div>
            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <?php if($isShow && $product): ?>
        <!-- Additional Info for Show Mode -->
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Created At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400"><?php echo e($product->created_at->format('d M Y, H:i')); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-300 dark:text-gray-300 mb-2">Updated At</label>
            <p class="text-sm text-gray-400 dark:text-gray-400"><?php echo e($product->updated_at->format('d M Y, H:i')); ?></p>
        </div>
        <?php endif; ?>
    </div>

    <?php if(!$isShow): ?>
    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <button type="button" 
                onclick="closeModal('product-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </button>
        <button type="submit" 
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php if($isCreate): ?>
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Product
            <?php else: ?>
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Update Product
            <?php endif; ?>
        </button>
    </div>
    <?php else: ?>
    <!-- Show Mode Actions -->
    <div class="flex items-center justify-end space-x-3 mt-6 pt-6 border-t border-gray-600 dark:border-gray-600">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('products.update')): ?>
        <button type="button" 
                onclick="editProduct(<?php echo e($product->id); ?>)"
                class="px-4 py-2 text-sm font-medium text-blue-400 dark:text-blue-400 bg-blue-900/20 dark:bg-blue-900/20 border border-blue-800 dark:border-blue-800 rounded-lg hover:bg-blue-900/40 dark:hover:bg-blue-900/40 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Edit Product
        </button>
        <?php endif; ?>
        <button type="button" 
                onclick="closeModal('product-modal')"
                class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Close
        </button>
    </div>
    <?php endif; ?>
</form>

<!-- Product form helper functions moved to resources/js/app-helpers.js and bundled in app.js -->
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/components/product-form.blade.php ENDPATH**/ ?>