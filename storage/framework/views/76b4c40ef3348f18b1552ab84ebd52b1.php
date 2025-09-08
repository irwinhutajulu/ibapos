<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'searchable' => false,
    'sortable' => false,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'loading' => false
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
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'searchable' => false,
    'sortable' => false,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'loading' => false
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
    <?php if($searchable): ?>
    <!-- Table Header with Search -->
    <div class="px-6 py-4 border-b border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($title ?? 'Data Table'); ?></h3>
                <?php if(isset($subtitle)): ?>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
            
            <?php if($searchable): ?>
            <div class="relative max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm" 
                    placeholder="Search..."
                    <?php echo e($attributes->whereStartsWith('x-')); ?>

                >
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table Container -->
    <div class="overflow-x-auto">
        <?php if($loading): ?>
        <!-- Loading State -->
        <div class="flex items-center justify-center py-12">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-600 dark:text-gray-400">Loading...</span>
            </div>
        </div>
        <?php elseif(empty($rows) && !$loading): ?>
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-12">
            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"><?php echo e($emptyMessage); ?></h3>
            <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm">
                <?php echo e($emptyDescription ?? 'There are no records to display at the moment.'); ?>

            </p>
            <?php if($slot->isNotEmpty()): ?>
                <div class="mt-4">
                    <?php echo e($slot); ?>

                </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
            <!-- Table Header -->
            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                <tr>
                    <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <?php if(is_array($header)): ?>
                            <div class="flex items-center space-x-1">
                                <span><?php echo e($header['label']); ?></span>
                                <?php if($sortable && isset($header['sortable']) && $header['sortable']): ?>
                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <?php echo e($header); ?>

                        <?php endif; ?>
                    </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($actions): ?>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                    <?php endif; ?>
                </tr>
            </thead>
            
            <!-- Table Body -->
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/50 dark:divide-gray-700/50">
                <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors duration-150 <?php echo e($index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/30 dark:bg-gray-700/20'); ?>">
                    <?php $__currentLoopData = $row['cells']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if(is_array($cell)): ?>
                            <?php if($cell['type'] === 'badge'): ?>
                                <?php
                                $badgeStyle = $cell['style'] ?? $cell['color'] ?? 'secondary';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php if($badgeStyle === 'success' || $badgeStyle === 'green'): ?> bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    <?php elseif($badgeStyle === 'danger' || $badgeStyle === 'red'): ?> bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    <?php elseif($badgeStyle === 'warning' || $badgeStyle === 'yellow'): ?> bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    <?php elseif($badgeStyle === 'primary' || $badgeStyle === 'blue'): ?> bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    <?php elseif($badgeStyle === 'secondary' || $badgeStyle === 'gray'): ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    <?php else: ?> bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    <?php endif; ?>">
                                    <?php if(isset($cell['icon'])): ?>
                                    <span class="mr-1"><?php echo $cell['icon']; ?></span>
                                    <?php endif; ?>
                                    <?php echo e($cell['text']); ?>

                                </span>
                            <?php elseif($cell['type'] === 'avatar'): ?>
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <?php if(isset($cell['image'])): ?>
                                        <img class="h-8 w-8 rounded-full" src="<?php echo e($cell['image']); ?>" alt="<?php echo e($cell['name'] ?? ''); ?>">
                                        <?php else: ?>
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white"><?php echo e(substr($cell['name'] ?? 'U', 0, 1)); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(isset($cell['name'])): ?>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white"><?php echo e($cell['name']); ?></p>
                                        <?php if(isset($cell['subtitle'])): ?>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($cell['subtitle']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif($cell['type'] === 'currency'): ?>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    <?php echo e($cell['formatted'] ?? 'Rp ' . number_format($cell['value'] ?? 0, 0, ',', '.')); ?>

                                </span>
                            <?php elseif($cell['type'] === 'date'): ?>
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <?php echo e($cell['formatted'] ?? \Carbon\Carbon::parse($cell['value'])->format('M d, Y')); ?>

                                    <?php if(isset($cell['time'])): ?>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo e($cell['time']); ?>

                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php elseif($cell['type'] === 'link'): ?>
                                <a href="<?php echo e($cell['url'] ?? '#'); ?>" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium underline">
                                    <?php echo e($cell['text'] ?? $cell['value'] ?? ''); ?>

                                </a>
                            <?php else: ?>
                                <span class="text-gray-900 dark:text-white"><?php echo e($cell['text'] ?? $cell['value'] ?? ''); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-gray-900 dark:text-white"><?php echo e($cell); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($actions && isset($row['actions'])): ?>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <?php $__currentLoopData = $row['actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $action): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($action['type'] === 'link'): ?>
                                <a href="<?php echo e($action['url']); ?>" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg 
                                    <?php if($action['style'] === 'primary'): ?> text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                    <?php elseif($action['style'] === 'success'): ?> text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
                                    <?php elseif($action['style'] === 'danger'): ?> text-white bg-red-600 hover:bg-red-700 focus:ring-red-500
                                    <?php elseif($action['style'] === 'warning'): ?> text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                    <?php else: ?> text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-gray-500
                                    <?php endif; ?>
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                    <?php if(isset($action['icon'])): ?>
                                    <span class="mr-1"><?php echo $action['icon']; ?></span>
                                    <?php endif; ?>
                                    <?php echo e($action['label']); ?>

                                </a>
                                <?php elseif($action['type'] === 'button'): ?>
                                <button 
                                    <?php if(isset($action['onclick'])): ?> onclick="<?php echo e($action['onclick']); ?>" <?php endif; ?>
                                    <?php if(isset($action['x-data'])): ?> <?php echo e($action['x-data']); ?> <?php endif; ?>
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg 
                                    <?php if($action['style'] === 'primary'): ?> text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                    <?php elseif($action['style'] === 'success'): ?> text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
                                    <?php elseif($action['style'] === 'danger'): ?> text-white bg-red-600 hover:bg-red-700 focus:ring-red-500
                                    <?php elseif($action['style'] === 'warning'): ?> text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                    <?php else: ?> text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-gray-500
                                    <?php endif; ?>
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                    <?php if(isset($action['icon'])): ?>
                                    <span class="mr-1"><?php echo $action['icon']; ?></span>
                                    <?php endif; ?>
                                    <?php echo e($action['label']); ?>

                                </button>
                                <?php elseif($action['type'] === 'dropdown'): ?>
                                <?php if (isset($component)) { $__componentOriginal84612f962a8c8d792f9c8f200e80faac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal84612f962a8c8d792f9c8f200e80faac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dropdown-actions','data' => ['align' => 'right','width' => '48']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dropdown-actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'right','width' => '48']); ?>
                                    <?php $__currentLoopData = $action['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($item['type'] === 'link'): ?>
                                        <a href="<?php echo e($item['url']); ?>" 
                                           class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            <?php if(isset($item['icon'])): ?>
                                            <span class="mr-3 text-gray-400 group-hover:text-gray-500"><?php echo $item['icon']; ?></span>
                                            <?php endif; ?>
                                            <?php echo e($item['label']); ?>

                                        </a>
                                        <?php elseif($item['type'] === 'button'): ?>
                                        <button 
                                            <?php if(isset($item['onclick'])): ?> onclick="<?php echo e($item['onclick']); ?>" <?php endif; ?>
                                            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                                            <?php if(isset($item['icon'])): ?>
                                            <span class="mr-3 text-gray-400 group-hover:text-gray-500"><?php echo $item['icon']; ?></span>
                                            <?php endif; ?>
                                            <?php echo e($item['label']); ?>

                                        </button>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal84612f962a8c8d792f9c8f200e80faac)): ?>
<?php $attributes = $__attributesOriginal84612f962a8c8d792f9c8f200e80faac; ?>
<?php unset($__attributesOriginal84612f962a8c8d792f9c8f200e80faac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal84612f962a8c8d792f9c8f200e80faac)): ?>
<?php $component = $__componentOriginal84612f962a8c8d792f9c8f200e80faac; ?>
<?php unset($__componentOriginal84612f962a8c8d792f9c8f200e80faac); ?>
<?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <?php if($pagination): ?>
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/20">
        <?php echo e($pagination); ?>

    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/components/table.blade.php ENDPATH**/ ?>