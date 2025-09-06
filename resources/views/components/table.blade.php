@props([
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'searchable' => false,
    'sortable' => false,
    'pagination' => null,
    'emptyMessage' => 'No data available',
    'loading' => false
])

<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
    @if($searchable)
    <!-- Table Header with Search -->
    <div class="px-6 py-4 border-b border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title ?? 'Data Table' }}</h3>
                @isset($subtitle)
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
                @endisset
            </div>
            
            @if($searchable)
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
                    {{ $attributes->whereStartsWith('x-') }}
                >
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Table Container -->
    <div class="overflow-x-auto">
        @if($loading)
        <!-- Loading State -->
        <div class="flex items-center justify-center py-12">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-600 dark:text-gray-400">Loading...</span>
            </div>
        </div>
        @elseif(empty($rows) && !$loading)
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-12">
            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $emptyMessage }}</h3>
            <p class="text-gray-500 dark:text-gray-400 text-center max-w-sm">
                {{ $emptyDescription ?? 'There are no records to display at the moment.' }}
            </p>
            @if($slot->isNotEmpty())
                <div class="mt-4">
                    {{ $slot }}
                </div>
            @endif
        </div>
        @else
        <!-- Table -->
        <table class="min-w-full divide-y divide-gray-200/50 dark:divide-gray-700/50">
            <!-- Table Header -->
            <thead class="bg-gray-50/80 dark:bg-gray-700/50">
                <tr>
                    @foreach($headers as $header)
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        @if(is_array($header))
                            <div class="flex items-center space-x-1">
                                <span>{{ $header['label'] }}</span>
                                @if($sortable && isset($header['sortable']) && $header['sortable'])
                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        @else
                            {{ $header }}
                        @endif
                    </th>
                    @endforeach
                    
                    @if($actions)
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                    @endif
                </tr>
            </thead>
            
            <!-- Table Body -->
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/50 dark:divide-gray-700/50">
                @foreach($rows as $index => $row)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/50 transition-colors duration-150 {{ $index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50/30 dark:bg-gray-700/20' }}">
                    @foreach($row['cells'] as $cell)
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if(is_array($cell))
                            @if($cell['type'] === 'badge')
                                @php
                                $badgeStyle = $cell['style'] ?? $cell['color'] ?? 'secondary';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($badgeStyle === 'success' || $badgeStyle === 'green') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                    @elseif($badgeStyle === 'danger' || $badgeStyle === 'red') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                    @elseif($badgeStyle === 'warning' || $badgeStyle === 'yellow') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                    @elseif($badgeStyle === 'primary' || $badgeStyle === 'blue') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                    @elseif($badgeStyle === 'secondary' || $badgeStyle === 'gray') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                    @endif">
                                    @if(isset($cell['icon']))
                                    <span class="mr-1">{!! $cell['icon'] !!}</span>
                                    @endif
                                    {{ $cell['text'] }}
                                </span>
                            @elseif($cell['type'] === 'avatar')
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if(isset($cell['image']))
                                        <img class="h-8 w-8 rounded-full" src="{{ $cell['image'] }}" alt="{{ $cell['name'] ?? '' }}">
                                        @else
                                        <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">{{ substr($cell['name'] ?? 'U', 0, 1) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    @if(isset($cell['name']))
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $cell['name'] }}</p>
                                        @if(isset($cell['subtitle']))
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cell['subtitle'] }}</p>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            @elseif($cell['type'] === 'currency')
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $cell['formatted'] ?? 'Rp ' . number_format($cell['value'] ?? 0, 0, ',', '.') }}
                                </span>
                            @elseif($cell['type'] === 'date')
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $cell['formatted'] ?? \Carbon\Carbon::parse($cell['value'])->format('M d, Y') }}
                                    @if(isset($cell['time']))
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $cell['time'] }}
                                    </div>
                                    @endif
                                </div>
                            @elseif($cell['type'] === 'link')
                                <a href="{{ $cell['url'] ?? '#' }}" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-medium underline">
                                    {{ $cell['text'] ?? $cell['value'] ?? '' }}
                                </a>
                            @else
                                <span class="text-gray-900 dark:text-white">{{ $cell['text'] ?? $cell['value'] ?? '' }}</span>
                            @endif
                        @else
                            <span class="text-gray-900 dark:text-white">{{ $cell }}</span>
                        @endif
                    </td>
                    @endforeach
                    
                    @if($actions && isset($row['actions']))
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            @foreach($row['actions'] as $action)
                                @if($action['type'] === 'link')
                                <a href="{{ $action['url'] }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg 
                                    @if($action['style'] === 'primary') text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                    @elseif($action['style'] === 'success') text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
                                    @elseif($action['style'] === 'danger') text-white bg-red-600 hover:bg-red-700 focus:ring-red-500
                                    @elseif($action['style'] === 'warning') text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                    @else text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-gray-500
                                    @endif
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                    @if(isset($action['icon']))
                                    <span class="mr-1">{!! $action['icon'] !!}</span>
                                    @endif
                                    {{ $action['label'] }}
                                </a>
                                @elseif($action['type'] === 'button')
                                <button 
                                    @if(isset($action['onclick'])) onclick="{{ $action['onclick'] }}" @endif
                                    @if(isset($action['x-data'])) {{ $action['x-data'] }} @endif
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg 
                                    @if($action['style'] === 'primary') text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500
                                    @elseif($action['style'] === 'success') text-white bg-green-600 hover:bg-green-700 focus:ring-green-500
                                    @elseif($action['style'] === 'danger') text-white bg-red-600 hover:bg-red-700 focus:ring-red-500
                                    @elseif($action['style'] === 'warning') text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500
                                    @else text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 focus:ring-gray-500
                                    @endif
                                    focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                                    @if(isset($action['icon']))
                                    <span class="mr-1">{!! $action['icon'] !!}</span>
                                    @endif
                                    {{ $action['label'] }}
                                </button>
                                @elseif($action['type'] === 'dropdown')
                                <x-dropdown-actions align="right" width="48">
                                    @foreach($action['items'] as $item)
                                        @if($item['type'] === 'link')
                                        <a href="{{ $item['url'] }}" 
                                           class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                            @if(isset($item['icon']))
                                            <span class="mr-3 text-gray-400 group-hover:text-gray-500">{!! $item['icon'] !!}</span>
                                            @endif
                                            {{ $item['label'] }}
                                        </a>
                                        @elseif($item['type'] === 'button')
                                        <button 
                                            @if(isset($item['onclick'])) onclick="{{ $item['onclick'] }}" @endif
                                            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-left">
                                            @if(isset($item['icon']))
                                            <span class="mr-3 text-gray-400 group-hover:text-gray-500">{!! $item['icon'] !!}</span>
                                            @endif
                                            {{ $item['label'] }}
                                        </button>
                                        @endif
                                    @endforeach
                                </x-dropdown-actions>
                                @endif
                            @endforeach
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    @if($pagination)
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200/50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/20">
        {{ $pagination }}
    </div>
    @endif
</div>
