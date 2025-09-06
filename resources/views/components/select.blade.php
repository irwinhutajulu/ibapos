@props([
    'options' => [],
    'placeholder' => 'Select an option...',
    'name' => '',
    'value' => null,
    'required' => false,
    'multiple' => false,
    'searchable' => false,
    'width' => '100%',
    'size' => 'md',
    'disabled' => false
])

@php
$sizeClasses = match($size) {
    'sm' => 'px-3 py-2 text-sm',
    'lg' => 'px-4 py-3 text-base',
    default => 'px-3 py-2.5 text-sm',
};

$baseClasses = 'w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-colors';
@endphp

@if($searchable && !empty($options))
<!-- Searchable Dropdown -->
<div class="relative" x-data="{ 
    open: false, 
    search: '', 
    selected: '{{ $value }}',
    selectedText: '{{ collect($options)->firstWhere('value', $value)['label'] ?? $placeholder }}',
    options: {{ json_encode($options) }},
    filteredOptions() {
        if (!this.search) return this.options;
        return this.options.filter(option => 
            option.label.toLowerCase().includes(this.search.toLowerCase())
        );
    },
    selectOption(option) {
        this.selected = option.value;
        this.selectedText = option.label;
        this.search = '';
        this.open = false;
        this.$refs.hiddenInput.value = option.value;
        this.$refs.hiddenInput.dispatchEvent(new Event('change'));
    }
}" style="width: {{ $width }}">
    <!-- Hidden Input -->
    <input type="hidden" 
           name="{{ $name }}" 
           x-ref="hiddenInput"
           :value="selected"
           @if($required) required @endif>
    
    <!-- Dropdown Button -->
    <button type="button" 
            @click="open = !open"
            @click.away="open = false"
            :disabled="{{ $disabled ? 'true' : 'false' }}"
            class="{{ $baseClasses }} {{ $sizeClasses }} flex items-center justify-between {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:border-gray-400 dark:hover:border-gray-500' }}">
        <span x-text="selectedText" class="truncate"></span>
        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 ml-2" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <!-- Dropdown Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden"
         style="display: none;">
        
        <!-- Search Input -->
        <div class="p-2 border-b border-gray-200 dark:border-gray-600">
            <input type="text" 
                   x-model="search"
                   placeholder="Search options..."
                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        
        <!-- Options List -->
        <div class="max-h-48 overflow-y-auto">
            <template x-for="option in filteredOptions()" :key="option.value">
                <button type="button"
                        @click="selectOption(option)"
                        class="w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-600 focus:bg-gray-100 dark:focus:bg-gray-600 focus:outline-none"
                        :class="{ 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': selected === option.value }">
                    <span x-text="option.label"></span>
                    <span x-show="option.description" x-text="option.description" class="block text-xs text-gray-500 dark:text-gray-400"></span>
                </button>
            </template>
            
            <!-- No Results -->
            <div x-show="filteredOptions().length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                No options found
            </div>
        </div>
    </div>
</div>

@else
<!-- Standard Select -->
<select name="{{ $name }}" 
        @if($required) required @endif
        @if($multiple) multiple @endif
        @if($disabled) disabled @endif
        class="{{ $baseClasses }} {{ $sizeClasses }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
        style="width: {{ $width }}">
    
    @if(!$multiple && $placeholder)
    <option value="">{{ $placeholder }}</option>
    @endif
    
    @foreach($options as $option)
        @if(is_array($option))
        <option value="{{ $option['value'] }}" 
                @if($multiple && is_array($value) && in_array($option['value'], $value)) selected 
                @elseif($value == $option['value']) selected 
                @endif
                @if(isset($option['disabled']) && $option['disabled']) disabled @endif>
            {{ $option['label'] }}
        </option>
        @else
        <option value="{{ $option }}" @if($value == $option) selected @endif>
            {{ $option }}
        </option>
        @endif
    @endforeach
</select>
@endif
