<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'id' => 'confirmation-modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'btn-danger',
    'icon' => 'warning' // warning, danger, info, success
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
    'id' => 'confirmation-modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmClass' => 'btn-danger',
    'icon' => 'warning' // warning, danger, info, success
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$iconClasses = [
    'warning' => 'text-yellow-400',
    'danger' => 'text-red-400', 
    'info' => 'text-blue-400',
    'success' => 'text-green-400'
];
$iconClass = $iconClasses[$icon] ?? $iconClasses['warning'];

$iconSvgs = [
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>',
    'danger' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>',
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
];
$iconSvg = $iconSvgs[$icon] ?? $iconSvgs['warning'];
?>

<div x-data="{ 
    open: false,
    confirmAction: null,
    openModal(action) {
        this.confirmAction = action;
        this.open = true;
    },
    closeModal() {
        this.open = false;
        this.confirmAction = null;
    },
    confirm() {
        if (this.confirmAction && typeof this.confirmAction === 'function') {
            this.confirmAction();
        }
        this.closeModal();
    }
}" 
x-show="open" 
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0 scale-95 translate-y-10"
x-transition:enter-end="opacity-100 scale-100 translate-y-0"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100 scale-100 translate-y-0"
x-transition:leave-end="opacity-0 scale-95 translate-y-10"
class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-50"
x-cloak
id="<?php echo e($id); ?>"
@keydown.escape.window="closeModal()"
>
    <!-- Floating Modal Content -->
    <div class="relative transform overflow-hidden rounded-xl bg-gray-800 dark:bg-gray-800 shadow-2xl border border-gray-700 dark:border-gray-600 w-96 max-w-sm"
         @click.stop
         style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-center p-6">
            <div class="text-center">
                <!-- Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-700 dark:bg-gray-700 mb-4">
                    <svg class="h-8 w-8 <?php echo e($iconClass); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?php echo $iconSvg; ?>

                    </svg>
                </div>
                
                <!-- Title -->
                <h3 class="text-lg font-semibold text-white dark:text-white mb-2">
                    <?php echo e($title); ?>

                </h3>
                
                <!-- Message -->
                <p class="text-gray-300 dark:text-gray-300 text-sm">
                    <?php echo e($message); ?>

                </p>
            </div>
        </div>
        
        <!-- Modal Actions -->
        <div class="flex items-center justify-center space-x-3 p-6 border-t border-gray-600 dark:border-gray-600">
            <button type="button" 
                    @click="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-300 dark:text-gray-300 bg-gray-700 dark:bg-gray-700 border border-gray-600 dark:border-gray-600 rounded-lg hover:bg-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                <?php echo e($cancelText); ?>

            </button>
            <button type="button" 
                    @click="confirm()"
                    class="px-4 py-2 text-sm font-medium text-white <?php echo e($confirmClass); ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                <?php echo e($confirmText); ?>

            </button>
        </div>
        
        <!-- Close X Button -->
        <button type="button" 
                @click="closeModal()"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
// Global function to open confirmation modal
window.openConfirmationModal = function(modalId, action, options = {}) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        // Update modal content if options provided
        if (options.title) {
            const titleElement = modal.querySelector('h3');
            if (titleElement) titleElement.textContent = options.title;
        }
        if (options.message) {
            const messageElement = modal.querySelector('p');
            if (messageElement) messageElement.textContent = options.message;
        }
        
        modal._x_dataStack[0].openModal(action);
    }
};

// Global function to close confirmation modal
window.closeConfirmationModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].closeModal();
    }
};
</script>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/components/confirmation-modal.blade.php ENDPATH**/ ?>