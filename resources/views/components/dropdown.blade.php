@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white dark:bg-gray-800',
    'trigger' => null,
    'triggerClass' => '',
    'dropdownClass' => '',
    'items' => [],
    'header' => null
])

@php
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
@endphp

<div class="relative inline-block text-left {{ $dropdownClass }}" x-data="{ open: false }">
    <!-- Trigger -->
    <div @click="open = !open">
        @if($trigger)
            {!! $trigger !!}
        @else
            <button type="button" 
                    class="inline-flex items-center justify-center w-full rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors {{ $triggerClass }}">
                <slot name="trigger">
                    Options
                    <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </slot>
            </button>
        @endif
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
         class="absolute {{ $alignmentClasses }} mt-2 {{ $widthClass }} rounded-xl shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 border border-gray-200 dark:border-gray-600 z-50 {{ $contentClasses }}"
         style="display: none;">
         
        @if($header)
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
            {!! $header !!}
        </div>
        @endif

        @if($slot->isNotEmpty())
        <!-- Custom Content Slot -->
        <div class="py-1">
            {{ $slot }}
        </div>
        @endif

        @if(!empty($items))
        <!-- Programmatic Items -->
        <div class="py-1">
            @foreach($items as $item)
                @if($item['type'] === 'header')
                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ $item['label'] }}
                    </div>
                @elseif($item['type'] === 'divider')
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                @elseif($item['type'] === 'link')
                    <a href="{{ $item['url'] ?? '#' }}" 
                       @if(isset($item['target'])) target="{{ $item['target'] }}" @endif
                       class="group flex items-center px-4 py-2 text-sm transition-colors
                              @if(isset($item['style']) && $item['style'] === 'danger')
                                  text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                              @elseif(isset($item['style']) && $item['style'] === 'warning')
                                  text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20
                              @elseif(isset($item['style']) && $item['style'] === 'success')
                                  text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20
                              @else
                                  text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700
                              @endif
                              {{ $item['class'] ?? '' }}">
                        @if(isset($item['icon']))
                        <span class="mr-3 flex-shrink-0 w-4 h-4">
                            {!! $item['icon'] !!}
                        </span>
                        @endif
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if(isset($item['badge']))
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $item['badge'] }}
                        </span>
                        @endif
                        @if(isset($item['shortcut']))
                        <span class="ml-2 text-xs text-gray-400 dark:text-gray-500">{{ $item['shortcut'] }}</span>
                        @endif
                    </a>
                @elseif($item['type'] === 'button')
                    <button type="{{ $item['buttonType'] ?? 'button' }}"
                            @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
                            @if(isset($item['form'])) form="{{ $item['form'] }}" @endif
                            @if(isset($item['confirm'])) onclick="return confirm('{{ $item['confirm'] }}')" @endif
                            class="group flex items-center w-full px-4 py-2 text-sm text-left transition-colors
                                   @if(isset($item['style']) && $item['style'] === 'danger')
                                       text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20
                                   @elseif(isset($item['style']) && $item['style'] === 'warning')
                                       text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20
                                   @elseif(isset($item['style']) && $item['style'] === 'success')
                                       text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20
                                   @else
                                       text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700
                                   @endif
                                   {{ $item['class'] ?? '' }}">
                        @if(isset($item['icon']))
                        <span class="mr-3 flex-shrink-0 w-4 h-4">
                            {!! $item['icon'] !!}
                        </span>
                        @endif
                        <span class="flex-1">{{ $item['label'] }}</span>
                        @if(isset($item['badge']))
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            {{ $item['badge'] }}
                        </span>
                        @endif
                        @if(isset($item['shortcut']))
                        <span class="ml-2 text-xs text-gray-400 dark:text-gray-500">{{ $item['shortcut'] }}</span>
                        @endif
                    </button>
                @elseif($item['type'] === 'custom')
                    <div class="px-4 py-2">
                        {!! $item['content'] !!}
                    </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>
