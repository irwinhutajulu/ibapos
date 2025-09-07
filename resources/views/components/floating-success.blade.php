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

<div x-data="floatingSuccessComponent()" 
x-show="show"
x-transition:enter="transform ease-out duration-800"
x-transition:enter-start="-translate-y-full opacity-50"
x-transition:enter-end="translate-y-0 opacity-50"
x-transition:leave="transform ease-in duration-800 delay-500"
x-transition:leave-start="translate-y-0 opacity-50"
x-transition:leave-end="-translate-y-full opacity-50"
class="{{ $positionClass }} z-30"
x-cloak
id="floating-success">
    <!-- Success Message Card -->
    <div class="bg-green-600 dark:bg-green-700 text-white px-2 py-1 rounded-xl border border-green-500 dark:border-green-600 max-w-md">
        
        <div class="flex items-center">
            <!-- Success Icon -->
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Message Text -->
            <div class="ml-3 flex-1">
                <p class="text-sm font-normal" x-text="message">
                    {{ $message }}
                </p>
            </div>
                        
        </div>
    </div>
</div>

<script>
// Alpine.js component function
function floatingSuccessComponent() {
    return {
        show: false,
        message: '',
        timer: null,
        
        init() {
            console.log('Floating success component initialized');
            
            // Check for session message
            @if(session('ok'))
                this.message = {!! json_encode(session('ok')) !!};
                this.show = true;
                console.log('Session message found:', this.message);
                this.startAutoHide();
            @endif
            
            // Register global functions
            window.showFloatingSuccess = (msg) => this.showMessage(msg);
            window.hideFloatingSuccess = () => this.hideMessage();
        },
        
        showMessage(text) {
            console.log('showMessage called with:', text);
            this.message = text;
            this.show = true;
            this.startAutoHide();
        },
        
        hideMessage() {
            this.show = false;
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }
        },
        
        startAutoHide() {
            if (this.timer) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(() => {
                this.hideMessage();
            }, {{ $duration }});
        }
    }
}

// Backup global function (in case Alpine isn't ready)
window.showFloatingSuccess = window.showFloatingSuccess || function(message) {
    console.log('Backup showFloatingSuccess called:', message);
    
    // Try to find Alpine component
    const component = document.getElementById('floating-success');
    if (component && component._x_dataStack && component._x_dataStack[0]) {
        component._x_dataStack[0].showMessage(message);
        return;
    }
    
    // Fallback: create a simple notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-8 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl z-50 max-w-md';
    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="h-6 w-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
};
</script>
