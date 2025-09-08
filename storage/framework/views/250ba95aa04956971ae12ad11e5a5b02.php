<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'searchUrl' => '',
    'searchParams' => [],
    'debounceMs' => 300,
    'minLength' => 1,
    'placeholder' => 'Search...',
    'headers' => [],
    'initialRows' => [],
    'initialPagination' => null,
    'emptyMessage' => 'No results found',
    'emptyDescription' => 'Try adjusting your search criteria',
    'emptyAction' => null
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
    'searchUrl' => '',
    'searchParams' => [],
    'debounceMs' => 300,
    'minLength' => 1,
    'placeholder' => 'Search...',
    'headers' => [],
    'initialRows' => [],
    'initialPagination' => null,
    'emptyMessage' => 'No results found',
    'emptyDescription' => 'Try adjusting your search criteria',
    'emptyAction' => null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div x-data="liveSearchTable({
    searchUrl: '<?php echo e($searchUrl); ?>',
    searchParams: <?php echo e(json_encode($searchParams)); ?>,
    debounceMs: <?php echo e($debounceMs); ?>,
    minLength: <?php echo e($minLength); ?>,
    initialRows: <?php echo e(json_encode($initialRows)); ?>,
    initialPagination: <?php echo e(json_encode($initialPagination)); ?>

})" x-init="init()">
    
    <!-- Search Input -->
    <div class="relative mb-6">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input 
            type="text" 
            x-model="searchQuery"
            @input="debouncedSearch()"
            placeholder="<?php echo e($placeholder); ?>"
            class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all"
        >
        
        <!-- Loading Indicator -->
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    <!-- Results Count -->
    <div x-show="pagination && pagination.total > 0" class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="pagination?.from || 0"></span> to <span x-text="pagination?.to || 0"></span> 
            of <span x-text="pagination?.total || 0"></span> results
            <span x-show="searchQuery" class="font-medium">for "<span x-text="searchQuery"></span>"</span>
        </p>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Table -->
            <div class="overflow-x-auto" x-show="isDesktop" x-cloak>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <!-- Table Headers -->
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <?php echo e($header); ?>

                        </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                
                <!-- Table Body -->
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(row, index) in rows" :key="row.id || index">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- Product Cell (Avatar) -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div x-show="row.cells[0]?.type === 'avatar'" class="flex-shrink-0">
                                        <img :src="row.cells[0]?.image || '<?php echo e(asset('images/default-product.svg')); ?>'" 
                                             :alt="row.cells[0]?.name"
                                             class="w-10 h-10 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                                             onerror="this.src='<?php echo e(asset('images/default-product.svg')); ?>'">
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="row.cells[0]?.name"></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="row.cells[0]?.subtitle"></div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Barcode -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="row.cells[1]"></td>
                            
                            <!-- Category -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white" x-text="row.cells[2]"></td>
                            
                            <!-- Price -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white" x-text="row.cells[3]?.formatted || row.cells[3]"></td>
                            
                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div x-show="row.actions && row.actions.length > 0" class="flex items-center justify-end space-x-2">
                                    <template x-for="action in row.actions" :key="action.label">
                                        <div>
                                            <!-- Link Action -->
                                            <a x-show="action.type === 'link'" 
                                               :href="action.url"
                                               class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-lg transition-colors"
                                               :class="{
                                                   'text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 border-blue-200 dark:border-blue-800': action.style === 'primary',
                                                   'text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600': action.style === 'secondary'
                                               }">
                                                <span x-html="action.icon"></span>
                                                <span x-text="action.label" class="ml-1"></span>
                                            </a>
                                            
                                            <!-- Button Action -->
                                            <button x-show="action.type === 'button'"
                                                    @click="executeAction(action)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded-lg transition-colors"
                                                    :class="{
                                                        'text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 border-red-200 dark:border-red-800': action.style === 'danger',
                                                        'text-green-600 dark:text-green-400 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 border-green-200 dark:border-green-800': action.style === 'success'
                                                    }">
                                                <span x-html="action.icon"></span>
                                                <span x-text="action.label" class="ml-1"></span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        
    <!-- Mobile Card View (hidden on desktop) -->
    <div class="lg:hidden space-y-4 p-4" x-show="!isDesktop" x-cloak>
            <template x-for="(row, index) in rows" :key="row.id || index">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-start space-x-4">
                        <!-- Image -->
                        <div class="flex-shrink-0">
                            <img :src="row.cells[0]?.image || '<?php echo e(asset('images/default-product.svg')); ?>'"
                                 :alt="row.cells[0]?.name"
                                 class="w-16 h-16 rounded-lg object-cover bg-gray-100 dark:bg-gray-700"
                                 onerror="this.src='<?php echo e(asset('images/default-product.svg')); ?>'">
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="row.cells[0]?.name"></h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="row.cells[0]?.subtitle"></p>
                                </div>

                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Qty</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white" x-text="row.cells[1]"></p>
                                </div>
                            </div>

                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Avg Cost</p>
                                <p class="text-md font-medium text-gray-900 dark:text-white" x-text="row.cells[2]"></p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Valuation</p>
                                <p class="text-md font-semibold text-gray-900 dark:text-white" x-text="row.cells[3]"></p>
                            </div>

                            <!-- Mobile Actions -->
                            <div class="mt-4 flex flex-wrap gap-2">
                                <template x-for="action in row.actions" :key="action.label">
                                    <div>
                                                     <!-- If action provides a url use a normal link on mobile for full-page navigation -->
                                                     <a x-show="action.url"
                                                         :href="action.url"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                            <span x-html="action.icon" class="mr-1"></span>
                                            <span x-text="action.label"></span>
                                        </a>

                                                     <!-- Fallback: if no url provided, execute JS handler (button or link without url) -->
                                                     <button x-show="!action.url"
                                                                @click="executeAction(action)"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                            <span x-html="action.icon" class="mr-1"></span>
                                            <span x-text="action.label"></span>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && rows.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($emptyMessage); ?></h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($emptyDescription); ?></p>
            <?php if($emptyAction): ?>
            <div class="mt-6">
                <?php echo $emptyAction; ?>

            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div x-show="pagination && pagination.last_page > 1" class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button @click="loadPage(pagination.current_page - 1)" 
                            :disabled="pagination.current_page <= 1"
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
                        Previous
                    </button>
                    
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        Page <span x-text="pagination.current_page"></span> of <span x-text="pagination.last_page"></span>
                    </span>
                    
                    <button @click="loadPage(pagination.current_page + 1)" 
                            :disabled="pagination.current_page >= pagination.last_page"
                            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700">
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function liveSearchTable(config) {
    return {
        // runtime responsive flag: true on desktop (lg and up)
        isDesktop: true,
        searchQuery: '',
        loading: false,
        rows: config.initialRows || [],
        pagination: config.initialPagination || null,
        searchParams: config.searchParams || {},
        searchTimeout: null,
        
        init() {
            // Set initial search query from URL params if exists
            const urlParams = new URLSearchParams(window.location.search);
            this.searchQuery = urlParams.get('q') || '';
            // initialize responsive flag and attach resize listener
            this.isDesktop = window.innerWidth >= 1024;
            window.addEventListener('resize', () => {
                this.isDesktop = window.innerWidth >= 1024;
            });

            console.log('Live search table initialized', {
                searchUrl: config.searchUrl,
                searchParams: this.searchParams,
                initialRows: this.rows.length,
                isDesktop: this.isDesktop
            });
        },
        
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch();
            }, config.debounceMs);
        },
        
        async performSearch(page = 1) {
            if (this.searchQuery.length < config.minLength && this.searchQuery.length > 0) {
                return;
            }
            
            this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    q: this.searchQuery,
                    page: page,
                    ...this.searchParams
                });
                
                console.log('Searching with params:', params.toString());
                console.log('Search URL:', `${config.searchUrl}?${params}`);
                
                const response = await fetch(`${config.searchUrl}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                console.log('Search response:', data);
                
                if (data.success) {
                    this.rows = data.data;
                    this.pagination = data.pagination;
                    
                    // Update URL without reloading page
                    const url = new URL(window.location);
                    if (this.searchQuery) {
                        url.searchParams.set('q', this.searchQuery);
                    } else {
                        url.searchParams.delete('q');
                    }
                    if (page > 1) {
                        url.searchParams.set('page', page);
                    } else {
                        url.searchParams.delete('page');
                    }
                    
                    // Update other params
                    Object.keys(this.searchParams).forEach(key => {
                        if (this.searchParams[key]) {
                            url.searchParams.set(key, this.searchParams[key]);
                        } else {
                            url.searchParams.delete(key);
                        }
                    });
                    
                    window.history.replaceState({}, '', url);
                } else {
                    console.error('Search failed:', data);
                }
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.loading = false;
            }
        },
        
        loadPage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.performSearch(page);
            }
        },
        
        updateSearchParams(newParams) {
            this.searchParams = { ...this.searchParams, ...newParams };
            console.log('Updated search params:', this.searchParams);
            this.performSearch(1);
        },
        
        executeAction(action) {
            if (action.onclick) {
                // Use Function constructor instead of eval for better security
                try {
                    const actionFunction = new Function('action', action.onclick);
                    actionFunction(action);
                } catch (error) {
                    console.error('Error executing action:', error);
                    // Fallback to eval for existing onclick handlers
                    eval(action.onclick);
                }
            }
        }
    }
}

// Global function to update search table params from external filters
window.updateProductSearch = function(params) {
    const searchTable = document.querySelector('[x-data*="liveSearchTable"]');
    if (searchTable && searchTable._x_dataStack && searchTable._x_dataStack[0]) {
        searchTable._x_dataStack[0].updateSearchParams(params);
    }
};
</script>
<?php /**PATH C:\xampp\htdocs\Data IBA POS\IBAPOS\resources\views/components/live-search-table.blade.php ENDPATH**/ ?>