@props([
    'paginator',
    'showInfo' => true,
    'showPerPage' => true,
    'perPageOptions' => [10, 25, 50, 100]
])

@if($paginator->hasPages())
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    @if($showInfo)
    <!-- Results Info -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        <span>Showing</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->firstItem() ?? 0 }}</span>
        <span>to</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->lastItem() ?? 0 }}</span>
        <span>of</span>
        <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
        <span>results</span>
    </div>
    @endif

    <div class="flex items-center justify-between sm:justify-end gap-4">
        @if($showPerPage)
        <!-- Per Page Selector -->
        <div class="flex items-center space-x-2">
            <label class="text-sm text-gray-600 dark:text-gray-400">Show:</label>
            <select class="px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    onchange="window.location.href = '{{ request()->fullUrlWithQuery(['per_page' => '__PER_PAGE__']) }}'.replace('__PER_PAGE__', this.value)">
                @foreach($perPageOptions as $option)
                <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Pagination Links -->
        <nav class="flex items-center space-x-1" aria-label="Pagination">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-l-lg cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="ml-1 hidden sm:block">Previous</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-l-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="ml-1 hidden sm:block">Previous</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ $page }}
                    </span>
                @else
                    @if($page == 1 || $page == $paginator->lastPage() || abs($page - $paginator->currentPage()) <= 2)
                        <a href="{{ $url }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                            {{ $page }}
                        </a>
                    @elseif(abs($page - $paginator->currentPage()) == 3)
                        <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
                            ...
                        </span>
                    @endif
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-r-lg hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <span class="mr-1 hidden sm:block">Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-r-lg cursor-not-allowed">
                    <span class="mr-1 hidden sm:block">Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </nav>
    </div>
</div>
@endif
