@props(['items' => [], 'title' => null])
<nav class="flex items-center text-sm mb-6" aria-label="Breadcrumb">
    <div class="flex items-center space-x-2">
        <!-- Home Icon -->
        <a href="/" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
            </svg>
        </a>

        @foreach($items as $i => $item)
            <!-- Separator -->
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>

            <!-- Breadcrumb Item -->
            @if(isset($item['url']) && !$loop->last)
                <a href="{{ $item['url'] }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 transition-colors font-medium">
                    {{ $item['label'] }}
                </a>
            @else
                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $item['label'] }}</span>
            @endif
        @endforeach

        @if($title && empty($items))
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $title }}</span>
        @endif
    </div>
</nav>
