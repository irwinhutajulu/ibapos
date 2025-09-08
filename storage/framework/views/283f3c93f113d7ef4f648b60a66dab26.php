<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-gray-800',
    'trigger' => null,
    'triggerClass' => '',
    'dropdownClass' => '',
    'items' => [],
    'header' => null
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
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-gray-800',
    'trigger' => null,
    'triggerClass' => '',
    'dropdownClass' => '',
    'items' => [],
    'header' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$alignmentClasses = match($align) {
    'left' => 'origin-top-left left-0',
    'top' => 'origin-top',
    default => 'origin-top-right right-0',
};

$widthClass = match($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    '72' => 'w-72',
    '80' => 'w-80',
    '96' => 'w-96',
    default => $width,
};
?>

<div class="relative inline-block text-left <?php echo e($dropdownClass); ?>" x-data="{ open: false }">
    <!-- Trigger -->
    <div @click="open = !open">
        <?php if($trigger): ?>
            <?php echo $trigger; ?>

        <?php else: ?>
            <button type="button" 
                    class="inline-flex items-center justify-center w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors <?php echo e($triggerClass); ?>">
                <slot name="trigger">
                    Options
                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </slot>
            </button>
        <?php endif; ?>
    </div>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         @click.away="open = false"
         @keydown.escape.window="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute <?php echo e($alignmentClasses); ?> mt-2 <?php echo e($widthClass); ?> rounded-xl shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 border border-gray-200 dark:border-gray-600 z-50 <?php echo e($contentClasses); ?>"
         style="display: none;">
         
        <?php if($header): ?>
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
            <?php echo $header; ?>

        </div>
        <?php endif; ?>

        <?php if($slot->isNotEmpty()): ?>
        <!-- Custom Content Slot -->
        <div class="py-1">
            <?php echo e($slot); ?>

        </div>
        <?php endif; ?>

        <?php if(!empty($items)): ?>
        <!-- Programmatic Items -->
        <div class="py-1">
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item['type'] === 'header'): ?>
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <?php echo e($item['label']); ?>

                    </div>
                <?php elseif($item['type'] === 'divider'): ?>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                <?php elseif($item['type'] === 'link'): ?>
                    <a href="<?php echo e($item['url'] ?? '#'); ?>" 
                       <?php if(isset($item['target'])): ?> target="<?php echo e($item['target']); ?>" <?php endif; ?>
                       class="group flex items-center px-4 py-2 text-sm transition-colors
                              <?php if(isset($item['style']) && $item['style'] === 'danger'): ?>
                                  text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                              <?php elseif(isset($item['style']) && $item['style'] === 'warning'): ?>
                                  text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20
                              <?php elseif(isset($item['style']) && $item['style'] === 'success'): ?>
                                  text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20
                              <?php else: ?>
                                  text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700
                              <?php endif; ?>
                              <?php echo e($item['class'] ?? ''); ?>">
                        <?php if(isset($item['icon'])): ?>
                        <span class="mr-3 flex-shrink-0 w-4 h-4">
                            <?php echo $item['icon']; ?>

                        </span>
                        <?php endif; ?>
                        <span class="flex-1"><?php echo e($item['label']); ?></span>
                        <?php if(isset($item['badge'])): ?>
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <?php echo e($item['badge']); ?>

                        </span>
                        <?php endif; ?>
                        <?php if(isset($item['shortcut'])): ?>
                        <span class="ml-2 text-xs text-gray-400 dark:text-gray-500"><?php echo e($item['shortcut']); ?></span>
                        <?php endif; ?>
                    </a>
                <?php elseif($item['type'] === 'button'): ?>
                    <button type="<?php echo e($item['buttonType'] ?? 'button'); ?>"
                            <?php if(isset($item['onclick'])): ?> onclick="<?php echo e($item['onclick']); ?>" <?php endif; ?>
                            <?php if(isset($item['form'])): ?> form="<?php echo e($item['form']); ?>" <?php endif; ?>
                            <?php if(isset($item['confirm'])): ?> onclick="return confirm('<?php echo e($item['confirm']); ?>')" <?php endif; ?>
                            class="group flex items-center w-full px-4 py-2 text-sm text-left transition-colors
                                   <?php if(isset($item['style']) && $item['style'] === 'danger'): ?>
                                       text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                                   <?php elseif(isset($item['style']) && $item['style'] === 'warning'): ?>
                                       text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20
                                   <?php elseif(isset($item['style']) && $item['style'] === 'success'): ?>
                                       text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20
                                   <?php else: ?>
                                       text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700
                                   <?php endif; ?>
                                   <?php echo e($item['class'] ?? ''); ?>">
                        <?php if(isset($item['icon'])): ?>
                        <span class="mr-3 flex-shrink-0 w-4 h-4">
                            <?php echo $item['icon']; ?>

                        </span>
                        <?php endif; ?>
                        <span class="flex-1"><?php echo e($item['label']); ?></span>
                        <?php if(isset($item['badge'])): ?>
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            <?php echo e($item['badge']); ?>

                        </span>
                        <?php endif; ?>
                        <?php if(isset($item['shortcut'])): ?>
                        <span class="ml-2 text-xs text-gray-400 dark:text-gray-500"><?php echo e($item['shortcut']); ?></span>
                        <?php endif; ?>
                    </button>
                <?php elseif($item['type'] === 'custom'): ?>
                    <div class="px-4 py-2">
                        <?php echo $item['content']; ?>

                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/components/dropdown.blade.php ENDPATH**/ ?>