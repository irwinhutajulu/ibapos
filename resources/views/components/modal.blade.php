@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'lg', // sm, md, lg, xl, 2xl, full
    'closeButton' => true,
    'backdrop' => true,
    'keyboard' => true,
    'footer' => null,
    'maxHeight' => true
])

@php
$sizeClasses = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md', 
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    'full' => 'max-w-full mx-4',
    'sidebar' => 'max-w-md'
];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['sidebar'];
@endphp

<div x-data="{ 
    open: false,
    openModal() {
        this.open = true;
        // Don't prevent scrolling on main page
    },
    closeModal() {
        this.open = false;
    }
}" 
x-show="open" 
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0 scale-95"
x-transition:enter-end="opacity-100 scale-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-95"
x-transition:leave-end="opacity-0 scale-95"
class="fixed inset-0 z-50 flex items-center justify-center p-4"
x-cloak
id="{{ $id }}"
@if($keyboard)
@keydown.escape.window="closeModal()"
@endif
>
    <!-- Modal Content (Center Middle) -->
    <div class="relative transform overflow-hidden rounded-xl bg-gray-800 dark:bg-gray-800 shadow-2xl border border-gray-700 dark:border-gray-600 w-full {{ $sizeClass }}"
         @click.stop
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-600 dark:border-gray-600 bg-gray-700 dark:bg-gray-700">
                <h3 class="text-lg font-semibold text-white dark:text-white">
                    {{ $title }}
                </h3>
                @if($closeButton)
                <button type="button" 
                        @click="closeModal()"
                        class="text-gray-300 hover:text-white dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
            </div>
            
            <!-- Modal Body -->
            <div class="p-4 @if($maxHeight) max-h-[80vh] overflow-y-auto custom-scrollbar @endif bg-gray-800 dark:bg-gray-800 text-white">
                {{ $slot }}
            </div>
            
            <!-- Modal Footer -->
            @if($footer)
            <div class="flex items-center justify-end space-x-3 p-4 border-t border-gray-600 dark:border-gray-600 bg-gray-700 dark:bg-gray-700">
                {{ $footer }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Global function to open modal
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].openModal();
    }
};

// Global function to close modal
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].closeModal();
    }
};
</script>
