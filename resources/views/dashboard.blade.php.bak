@extends('layouts.app')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome back!</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Here's what's happening with your store today.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center gap-3">
            <!-- Quick Actions Dropdown -->
            @php
            $quickActions = [
                ['type' => 'header', 'label' => 'Quick Actions'],
            ];
            
            // Add only existing routes
            if (Route::has('pos.index')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('pos.index'),
                    'label' => 'Point of Sale',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>'
                ];
            }
            
            if (Route::has('purchases.create')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('purchases.create'),
                    'label' => 'New Purchase',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>'
                ];
            }
            
            if (Route::has('products.create')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('products.create'),
                    'label' => 'Add Product',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>'
                ];
            }
            
            // Add divider and management section
            $quickActions[] = ['type' => 'divider'];
            $quickActions[] = ['type' => 'header', 'label' => 'Management'];
            
            if (Route::has('customers.index')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('customers.index'),
                    'label' => 'Customers',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>'
                ];
            }
            
            if (Route::has('suppliers.index')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('suppliers.index'),
                    'label' => 'Suppliers',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                ];
            }
            
            if (Route::has('stocks.index')) {
                $quickActions[] = [
                    'type' => 'link',
                    'url' => route('stocks.index'),
                    'label' => 'Stock Management',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>'
                ];
            }
            @endphp
            
            <x-dropdown :items="$quickActions" width="56">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Quick Actions
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </x-slot>
            </x-dropdown>

            <!-- Primary Action Button -->
            @if(Route::has('pos.index'))
            <a href="{{ route('pos.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Point of Sale
            </a>
            @elseif(Route::has('sales.index'))
            <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                View Sales
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today Sales -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Sales</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp 0</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                    <span class="inline-flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +0% from yesterday
                    </span>
                </p>
            </div>
        </div>
    </div>

    <!-- Transactions -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Transactions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Today</p>
            </div>
        </div>
    </div>

    <!-- Top Product -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Top Product</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">-</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This month</p>
            </div>
        </div>
    </div>

    <!-- Stock Alerts -->
    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50 hover:shadow-lg transition-all duration-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-4 flex-1">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Stock Alerts</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">0</p>
                <p class="text-xs text-red-600 dark:text-red-400 mt-1">Low stock items</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Sales Chart -->
    <div class="xl:col-span-2">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Overview</h3>
                <div class="flex items-center space-x-2">
                    <select id="sales-range-select" class="px-3 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 3 months</option>
                    </select>
                </div>
            </div>
            <div id="sales-chart-wrapper" data-server-rendered="1" class="h-80 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-2">
                <canvas id="sales-chart" height="240"></canvas>
            </div>
        </div>
    </div>


    <!-- Recent Activity -->
    <div class="space-y-6">
        <!-- Recent Orders -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">View all</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-center h-32 text-gray-500 dark:text-gray-400 text-sm">
                    No recent orders
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-6 border border-gray-200/50 dark:border-gray-700/50">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @can('sales.create')
                <a href="{{ route('pos.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">New Sale</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Create a new transaction</p>
                    </div>
                </a>
                @endcan

                @can('products.create')
                <a href="{{ route('products.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l3-3 3 3"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Manage Products</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">View and edit inventory</p>
                    </div>
                </a>
                @endcan

                @can('purchases.create')
                <a href="{{ route('purchases.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">New Purchase</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Add inventory from suppliers</p>
                    </div>
                </a>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Expose active location and base url to JS for debugging
    window.appActiveLocationId = window.appActiveLocationId ?? @json(session('active_location_id'));
    window.appBaseUrl = window.appBaseUrl ?? '';
    console.debug('window.appActiveLocationId (from blade):', window.appActiveLocationId);
    console.debug('window.appBaseUrl (from blade):', window.appBaseUrl);

</script>
<script>
    (function(){
        async function loadDashboard() {
            try {
                const res = await fetch(`${window.appBaseUrl || ''}/api/reports/dashboard?_cache=${Date.now()}`, { credentials: 'same-origin' });
                if (!res.ok) return console.warn('Dashboard API not available', await res.text());
                const data = await res.json();
                // Fill cards
                const totalEl = document.querySelector('[data-dashboard="today-total"]');
                const countEl = document.querySelector('[data-dashboard="today-count"]');
                const topEl = document.querySelector('[data-dashboard="top-product"]');
                const alertsEl = document.querySelector('[data-dashboard="stock-alerts"]');
                const recentEl = document.querySelector('[data-dashboard="recent-orders"]');

                if (totalEl) totalEl.textContent = new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR' }).format(data.today_total || 0);
                if (countEl) countEl.textContent = (data.today_count || 0);
                if (topEl) topEl.textContent = data.top_product ? `${data.top_product.name} (${data.top_product.qty_sold})` : '-';
                if (alertsEl) alertsEl.textContent = (data.stock_alerts || 0);
                if (recentEl) {
                    recentEl.innerHTML = '';
                    if ((data.recent || []).length === 0) {
                        recentEl.innerHTML = '<div class="flex items-center justify-center h-32 text-gray-500 text-sm">No recent orders</div>';
                    } else {
                        const ul = document.createElement('div');
                        ul.className = 'space-y-2';
                        data.recent.forEach(r=>{
                            const d = document.createElement('div');
                            d.className = 'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg';
                            d.innerHTML = `<div class="text-sm">${r.invoice_no || 'INV-'+r.id}</div><div class="text-sm font-medium">${new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(r.total||0)}</div>`;
                            ul.appendChild(d);
                        })
                        recentEl.appendChild(ul);
                    }
                }

            } catch (err) {
                console.error('Failed loading dashboard:', err);
            }
        }

        loadDashboard();

        // Realtime updates via Echo
        if (window.Echo && window.appActiveLocationId) {
            const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
            ch.listen('.sale.posted', (e)=>{
                // small heuristic: increment counters and prepend recent
                loadDashboard();
            }).listen('.sale.voided', (e)=>{
                loadDashboard();
            }).listen('.stock.updated', (e)=>{
                // stock alert may change
                loadDashboard();
            });
        }

        // Wire to specific placeholders in DOM (mark elements)
        (function markPlaceholders(){
            const totalWrap = document.querySelector('.text-2xl.font-bold');
            if (totalWrap && !totalWrap.hasAttribute('data-dashboard')) totalWrap.setAttribute('data-dashboard','today-total');
            const txCount = document.querySelectorAll('.text-2xl.font-bold')[1];
            if (txCount && !txCount.hasAttribute('data-dashboard')) txCount.setAttribute('data-dashboard','today-count');
            const top = document.querySelectorAll('.text-lg.font-bold')[0];
            if (top && !top.hasAttribute('data-dashboard')) top.setAttribute('data-dashboard','top-product');
            const alerts = document.querySelectorAll('.text-2xl.font-bold')[3];
            if (alerts && !alerts.hasAttribute('data-dashboard')) alerts.setAttribute('data-dashboard','stock-alerts');
            const recent = document.querySelectorAll('.space-y-4')[0];
            if (recent && !recent.hasAttribute('data-dashboard')) recent.setAttribute('data-dashboard','recent-orders');
        })();

    })();
</script>
    <script>
        // Ensure Chart.js is available. If not, load it dynamically from CDN and resolve when ready.
        function ensureChartJs(timeout = 5000) {
            return new Promise((resolve, reject) => {
                if (window.Chart) return resolve(window.Chart);
                const existing = document.querySelector('script[data-chartjs-loader]');
                if (existing) {
                    // wait for it to load
                    existing.addEventListener('load', () => resolve(window.Chart));
                    existing.addEventListener('error', () => reject(new Error('Failed loading Chart.js')));
                    return;
                }
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
                s.setAttribute('data-chartjs-loader', '1');
                s.async = true;
                s.onload = () => resolve(window.Chart);
                s.onerror = () => reject(new Error('Failed loading Chart.js'));
                document.head.appendChild(s);

                // safety timeout
                setTimeout(() => {
                    if (!window.Chart) reject(new Error('Chart.js load timeout'));
                }, timeout);
            });
        }

        document.addEventListener('DOMContentLoaded', function(){
        (function(){
            let salesChart = null;

            // Temporary debug toggle: when true, the dashboard will use the sample debug endpoint
            // so we can verify Chart.js rendering and layout independent from real backend data.
            // Set to false to use the real API again.
            // default to false in normal runs; set to true only for manual debugging
            window.__useDashboardSample = window.__useDashboardSample ?? false;

            async function fetchSeries(days){
                let url;
                // Use real API by default. Developers can set `window.__useDashboardSample = true` in console to override.
                url = `${window.appBaseUrl || ''}/api/reports/sales-series?days=${encodeURIComponent(days)}`;
                if (window.appActiveLocationId) url += `&location_id=${encodeURIComponent(window.appActiveLocationId)}`;
                console.debug('Fetching sales series URL:', url);
                const res = await fetch(url, { credentials: 'same-origin' });
                if (!res.ok) {
                    console.error('Sales series fetch failed, status:', res.status, await res.text());
                    throw new Error('Failed fetching series');
                }
                const json = await res.json();
                console.debug('Sales series response:', json);
                return json;
            }

            function renderChart(ctx, labels, data){
                if (salesChart) {
                    salesChart.data.labels = labels.map(l=>new Date(l).toLocaleDateString());
                    salesChart.data.datasets[0].data = data;
                    salesChart.update();
                    return salesChart;
                }

                salesChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels.map(l=>new Date(l).toLocaleDateString()),
                        datasets: [{
                                label: 'Sales',
                                data: data,
                                borderColor: '#1e40af',
                                backgroundColor: 'rgba(30,64,175,0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#1e40af',
                            }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: v => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(v) }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const v = context.raw || 0;
                                        return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR'}).format(v);
                                    }
                                }
                            }
                        },
                        maintainAspectRatio: false,
                    }
                });

                return salesChart;
            }

            async function loadAndRenderSeries(days){
                try {
                    // ensure Chart.js exists before attempting to render
                    await ensureChartJs();
                    const js = await fetchSeries(days);
                    // Ensure canvas element exists and has proper pixel dimensions — sometimes CSS/Flex collapses it
                    const wrapperEl = document.getElementById('sales-chart-wrapper');
                    if (!document.getElementById('sales-chart')) {
                        if (wrapperEl) wrapperEl.innerHTML = '<canvas id="sales-chart" height="240"></canvas>';
                    }
                    const canvasEl = document.getElementById('sales-chart');
                    if (!canvasEl) {
                        console.error('Cannot find sales-chart canvas after ensuring it');
                        return;
                    }

                    // compute and set explicit pixel size to avoid 0x0 canvas in some layouts
                    const ratio = window.devicePixelRatio || 1;
                    const w = (wrapperEl && wrapperEl.clientWidth) ? wrapperEl.clientWidth : 600;
                    const h = (wrapperEl && wrapperEl.clientHeight) ? wrapperEl.clientHeight : 240;
                    // set style for layout and attributes for pixel buffer
                    canvasEl.style.width = w + 'px';
                    canvasEl.style.height = h + 'px';
                    canvasEl.width = Math.floor(w * ratio);
                    canvasEl.height = Math.floor(h * ratio);
                    // ensure drawing context scaled for DPR
                    const ctx = canvasEl.getContext('2d');
                    try { ctx.setTransform(ratio, 0, 0, ratio, 0, 0); } catch(e) { /* older browsers */ }

                    // temporary visual aid while debugging
                    if (window.__useDashboardSample) {
                        canvasEl.style.border = '2px dashed rgba(59,130,246,0.6)';
                    }

                    console.debug('Canvas pixel size:', canvasEl.width, canvasEl.height, 'style:', canvasEl.style.width, canvasEl.style.height, 'wrapper:', wrapperEl && wrapperEl.clientWidth, wrapperEl && wrapperEl.clientHeight);

                    console.debug('Sales series data values:', js.data);
                    const isAllZero = Array.isArray(js.data) && js.data.every(v=>!v);
                    if (isAllZero) {
                        // show a friendly message in the wrapper
                        if (wrapper) wrapper.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-gray-500">No sales data for the selected range</div>';
                        // destroy existing chart if any
                        if (salesChart) { salesChart.destroy(); salesChart = null; }
                        return;
                    }
                    // restore canvas if wrapper was replaced
                    if (!document.getElementById('sales-chart')) {
                        wrapper.innerHTML = '<canvas id="sales-chart" height="240"></canvas>';
                    }
                    const ctx2 = document.getElementById('sales-chart').getContext('2d');
                    console.debug('Rendering chart with labels length:', (js.labels||[]).length, 'data length:', (js.data||[]).length);
                    const created = renderChart(ctx2, js.labels || [], js.data || []);
                    console.debug('Chart instance:', created);
                } catch (err) {
                    console.error('Chart render failed', err);
                    if (wrapper) wrapper.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-red-500">Failed loading chart data</div>';
                }
            }

            // initial load (ensure canvas exists and sizing is applied)
            const initialSelect = document.getElementById('sales-range-select');
            const initialDays = initialSelect ? initialSelect.value : 7;
            const wrapper = document.getElementById('sales-chart-wrapper');
            const canvas = document.getElementById('sales-chart');
            if (!canvas) {
                if (wrapper) wrapper.innerHTML = '<div class="flex items-center justify-center h-full text-sm text-gray-500">Chart canvas is missing</div>';
                console.warn('sales-chart canvas not found');
            } else {
                // Make canvas fill the wrapper for responsive sizing
                canvas.style.width = '100%';
                canvas.style.height = '100%';
                loadAndRenderSeries(initialDays);
            }

            // change range
            if (initialSelect) {
                initialSelect.addEventListener('change', (e)=>{
                    loadAndRenderSeries(e.target.value);
                });
            }

            // refresh chart when dashboard reloads (used by Echo listeners)
            window.refreshDashboardSeries = function(){
                const days = (document.getElementById('sales-range-select') || {}).value || 7;
                loadAndRenderSeries(days);
            }

            // Subscribe to Echo events to refresh chart as well
            if (window.Echo && window.appActiveLocationId) {
                try{
                    const ch = window.Echo.private(`location.${window.appActiveLocationId}`);
                    ch.listen('.sale.posted', (e)=>{ console.debug('Echo sale.posted received', e); window.refreshDashboardSeries(); }).listen('.sale.voided', (e)=>{ console.debug('Echo sale.voided received', e); window.refreshDashboardSeries(); }).listen('.stock.updated', (e)=>{ console.debug('Echo stock.updated received', e); /* keep for consistency */ });
                } catch (err) { console.warn('Failed to subscribe to Echo channel', err); }
            }
        })();
        });
    </script>
@endpush
