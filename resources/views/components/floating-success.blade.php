@props([
    'message' => '',
    'duration' => 3000,
    'position' => 'top-center' // top-center, top-right, bottom-center, etc
])

@php
$positionClasses = [
    'top-center' => 'fixed top-8 left-1/2 transform -translate-x-1/2',
    'top-right' => 'fixed top-8 right-8',
    'bottom-center' => 'fixed bottom-8 left-1/2 transform -translate-x-1/2',
    'bottom-right' => 'fixed bottom-8 right-8'
];
$positionClass = $positionClasses[$position] ?? $positionClasses['top-center'];
@endphp

<div x-data="{ 
    show: {{ session('ok') ? 'true' : 'false' }},
    message: {!! session('ok') ? json_encode(session('ok')) : "''" !!},
    autoHide: true,
    timer: null,
    
    showMessage(text, autoHide = true) {
        this.message = text;
        this.autoHide = autoHide;
        this.show = true;
        
        if (autoHide) {
            // Clear existing timer
            if (this.timer) {
                clearTimeout(this.timer);
            }
            
            // Set new timer
            this.timer = setTimeout(() => {
                this.hideMessage();
            }, {{ $duration }});
        }
    },
    
    hideMessage() {
        this.show = false;
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
    }
}" 
x-init="
    @if(session('ok'))
        console.log('Floating success initialized with session message:', message);
        // Auto-hide after duration if there's a session message
        if (autoHide && show) {
            timer = setTimeout(() => hideMessage(), {{ $duration }});
        }
    @else
        console.log('Floating success initialized - no session message');
    @endif
"
x-show="show"
x-transition:enter="transform ease-out duration-300"
x-transition:enter-start="-translate-y-full opacity-0"
x-transition:enter-end="translate-y-0 opacity-100"
x-transition:leave="transform ease-in duration-200"
x-transition:leave-start="translate-y-0 opacity-100"
x-transition:leave-end="-translate-y-full opacity-0"
class="{{ $positionClass }} z-50"
x-cloak
id="floating-success"
>
    <!-- Success Message Card -->
    <div class="bg-green-600 dark:bg-green-700 text-white px-6 py-4 rounded-xl shadow-2xl border border-green-500 dark:border-green-600 max-w-md"
         style="box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.3), 0 10px 10px -5px rgba(16, 185, 129, 0.2);">
        
        <div class="flex items-center">
            <!-- Success Icon -->
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Message Text -->
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" x-text="message">
                    {{ $message }}
                </p>
            </div>
            
            <!-- Close Button -->
            <div class="ml-4 flex-shrink-0">
                <button @click="hideMessage()" 
                        class="bg-green-700 dark:bg-green-800 rounded-md p-1 inline-flex items-center justify-center text-green-200 hover:text-white hover:bg-green-600 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global function to show floating success message
window.showFloatingSuccess = function(message, duration = 3000) {
    const successComponent = document.getElementById('floating-success');
    if (successComponent && successComponent._x_dataStack) {
        successComponent._x_dataStack[0].showMessage(message, true);
    }
};

// Global function to hide floating success message
window.hideFloatingSuccess = function() {
    const successComponent = document.getElementById('floating-success');
    if (successComponent && successComponent._x_dataStack) {
        successComponent._x_dataStack[0].hideMessage();
    }
};
</script>
