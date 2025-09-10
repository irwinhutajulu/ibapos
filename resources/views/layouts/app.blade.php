<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="if(darkMode) $el.classList.add('dark'); else $el.classList.remove('dark')" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IBAPOS' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.4); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.6); }
        
        /* Smooth transitions */
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
        
        /* Glass effect */
        .glass { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.8); }
        .glass-dark { backdrop-filter: blur(10px); background: rgba(31, 41, 55, 0.8); }
    </style>
    <script>
        // Safe stub: queue notifications fired before Alpine toast component is ready
        window._notifyQueue = window._notifyQueue || [];
        window.notify = window.notify || function (msg, type = 'info', ttl = 3500) {
            window._notifyQueue.push({ msg, type, ttl });
        };
    </script>
</head>
<body class="h-full bg-gradient-to-br from-slate-50 to-slate-100 dark:from-gray-900 dark:to-gray-800 text-gray-900 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex">
        <!-- Mobile backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
             class="fixed inset-y-0 z-40 w-72 transform bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border-r border-gray-200/50 dark:border-gray-700/50 transition-transform duration-300 ease-in-out lg:static lg:inset-auto">
            
            <!-- Logo & Brand -->
            <div class="h-20 flex items-center px-6 border-b border-gray-200/50 dark:border-gray-700/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-lg">IB</span>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h1 class="font-bold text-xl text-gray-900 dark:text-white">IBAPOS</h1>
                            @if(app()->environment('local') && config('app.developer_mode'))
                                <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-semibold">DEV</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Point of Sale
                            @if(app()->environment('local') && config('app.developer_mode'))
                                <span class="text-orange-500"> • Developer Mode</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-6 space-y-2 overflow-y-auto h-full">
                <!-- Dashboard -->
                <a href="/" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 font-medium text-gray-700 dark:text-gray-200">
                    <div class="w-5 h-5 text-gray-500 group-hover:text-blue-500 transition-colors">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0H8v0z"></path>
                        </svg>
                    </div>
                    <span>Dashboard</span>
                </a>

                <!-- Sales Section -->
                <div class="pt-4">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 pb-2">Sales</p>
                    @can('sales.create')
                    <a href="{{ route('pos.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-blue-500">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span>Point of Sale</span>
                    </a>
                    @endcan
                    @can('sales.read')
                    <a href="{{ route('sales.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-blue-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4"></path>
                            </svg>
                        </div>
                        <span>Sales History</span>
                    </a>
                    @endcan
                </div>

                <!-- Inventory Section -->
                <div class="pt-4">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 pb-2">Inventory</p>
                    @can('products.read')
                    <a href="{{ route('products.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-green-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M9 9l3-3 3 3"></path>
                            </svg>
                        </div>
                        <span>Products</span>
                    </a>
                    @endcan
                    @can('categories.read')
                    <a href="{{ route('categories.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-green-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <span>Categories</span>
                    </a>
                    @endcan
                    @can('stocks.read')
                    <a href="{{ route('stocks.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-green-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span>Stock Levels</span>
                    </a>
                    <a href="{{ route('stock-adjustments.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-green-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        <span>Adjustments</span>
                    </a>
                    @endcan
                    @can('stock_mutations.request')
                    <a href="{{ route('stock-mutations.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-green-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <span>Stock Transfers</span>
                    </a>
                    @endcan
                </div>

                <!-- Procurement Section -->
                <div class="pt-4">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 pb-2">Procurement</p>
                    @can('purchases.read')
                    <a href="{{ route('purchases.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-purple-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span>Purchases</span>
                    </a>
                    @endcan
                    @can('suppliers.read')
                    <a href="{{ route('suppliers.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-purple-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span>Suppliers</span>
                    </a>
                    @endcan
                </div>

                <!-- Customers Section -->
                <div class="pt-4">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 pb-2">Customers</p>
                    @can('customers.read')
                    <a href="{{ route('customers.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-indigo-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span>Customers</span>
                    </a>
                    @endcan
                </div>

                <!-- System Section -->
                <div class="pt-6 border-t border-gray-200/50 dark:border-gray-700/50">
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider px-4 pb-2">System</p>
                    @can('stocks.read')
                    <a href="{{ route('reservations.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-yellow-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <span>Reservations</span>
                    </a>
                    @endcan
                    <a href="#" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-emerald-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span>Reports</span>
                    </a>
                    @can('admin.permissions')
                    <a href="{{ route('admin.notifications.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-red-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9.586 14.414L7.5 16.5 2 11l2.586-2.586L9.586 14.414z"></path>
                            </svg>
                        </div>
                        <span>Notifications</span>
                    </a>
                    @endcan
                    @can('admin.locations')
                    <a href="{{ route('locations.index') }}" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-blue-500 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span>Locations</span>
                    </a>
                    @endcan
                    @can('admin.users')
                    <a href="#" class="group flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500 group-hover:text-gray-600 transition-colors">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span>Settings</span>
                    </a>
                    @endcan
                </div>

                <!-- Dark Mode Toggle -->
                <div class="pt-6 border-t border-gray-200/50 dark:border-gray-700/50">
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); if(darkMode) $root.classList.add('dark'); else $root.classList.remove('dark')" 
                            class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/50 transition-all duration-200 text-gray-700 dark:text-gray-200">
                        <div class="w-5 h-5 text-gray-500">
                            <svg x-show="!darkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg x-show="darkMode" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                    </button>
                </div>
            </nav>
        </div>
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-20 flex items-center justify-between bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-gray-700/50 px-6 sticky top-0 z-20">
                <div class="flex items-center space-x-4">
                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                            @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Search Bar (Hidden on mobile) -->
                    <div class="hidden md:flex items-center">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" placeholder="Search..." 
                                   class="block w-64 pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                        </div>
                    </div>

                    <!-- Location Selector -->
                    @auth
                    <form method="POST" action="{{ route('active-location.set') }}" class="flex items-center space-x-2">
                        @csrf
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Location:</label>
                        <select name="location_id" 
                                class="px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" 
                                onchange="this.form.submit()">
                            @php($activeId = session('active_location_id'))
                            @foreach(auth()->user()->locations as $loc)
                                <option value="{{ $loc->id }}" @selected($activeId == $loc->id)>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    @endauth

                    <!-- Developer Mode Indicator -->
                    @if(app()->environment('local') && config('app.developer_mode'))
                    <div class="flex items-center space-x-2 px-3 py-2 bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300 rounded-xl border border-orange-200 dark:border-orange-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        <span class="text-sm font-medium">Developer Mode</span>
                    </div>
                    @endif

                    <!-- Notifications Button -->
                    <button class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9.586 14.414L7.5 16.5 2 11l2.586-2.586L9.586 14.414z"></path>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User Menu -->
                    @guest
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                        Login
                    </a>
                    @else
                    <x-dropdown-user />
                    @endguest
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 lg:p-8 overflow-auto">
                @isset($breadcrumbs)
                    <x-breadcrumbs :items="$breadcrumbs['items'] ?? []" :title="$breadcrumbs['title'] ?? ($title ?? null)" />
                @endisset
                
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
                        @auth
                        <script>
                            window.appActiveLocationId = {{ (int) session('active_location_id') }};
                        </script>
                        @endauth
                        @stack('scripts')
            
                                    <!-- Toast Notifications -->
                                    <div x-data="{ 
                                             toasts: [], 
                                             add(t) { 
                                                 const id = Date.now() + Math.random(); 
                                                 this.toasts.push({ ...t, id }); 
                                                 setTimeout(() => this.remove(id), t.ttl ?? 3500); 
                                             }, 
                                             remove(id) { 
                                                 this.toasts = this.toasts.filter(x => x.id !== id) 
                                             } 
                                         }"
                                         x-init="
                                             const api = $data; 
                                             window.notify = (msg, type='info', ttl=3500) => api.add({msg, type, ttl}); 
                                             window.addEventListener('notify', e => api.add(e.detail)); 
                                             if (window._notifyQueue && window._notifyQueue.length) { 
                                                 window._notifyQueue.splice(0).forEach(d => api.add(d)); 
                                             }
                                         "
                                         class="fixed top-6 right-6 z-50 space-y-3 max-w-sm">
                                        <template x-for="t in toasts" :key="t.id">
                                            <div x-transition:enter="transform ease-out duration-300 transition"
                                                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                                                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 class="relative max-w-sm w-full rounded-xl shadow-lg overflow-hidden"
                                                 :class="{
                                                     'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700': t.type==='info',
                                                     'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800': t.type==='success',
                                                     'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800': t.type==='warning',
                                                     'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800': t.type==='error',
                                                 }">
                                                <div class="p-4">
                                                    <div class="flex items-start">
                                                        <div class="flex-shrink-0">
                                                            <!-- Info Icon -->
                                                            <svg x-show="t.type === 'info'" class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <!-- Success Icon -->
                                                            <svg x-show="t.type === 'success'" class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <!-- Warning Icon -->
                                                            <svg x-show="t.type === 'warning'" class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                            </svg>
                                                            <!-- Error Icon -->
                                                            <svg x-show="t.type === 'error'" class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3 w-0 flex-1 pt-0.5">
                                                            <p class="text-sm font-medium" 
                                                               :class="{
                                                                   'text-gray-900 dark:text-gray-100': t.type==='info',
                                                                   'text-green-800 dark:text-green-200': t.type==='success',
                                                                   'text-yellow-800 dark:text-yellow-200': t.type==='warning',
                                                                   'text-red-800 dark:text-red-200': t.type==='error',
                                                               }"
                                                               x-text="t.msg"></p>
                                                        </div>
                                                        <div class="ml-4 flex-shrink-0 flex">
                                                            <button @click="$parent.remove(t.id)" 
                                                                    class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                                                    :class="{
                                                                        'text-gray-400 hover:text-gray-500 focus:ring-gray-500': t.type==='info',
                                                                        'text-green-400 hover:text-green-500 focus:ring-green-500': t.type==='success',
                                                                        'text-yellow-400 hover:text-yellow-500 focus:ring-yellow-500': t.type==='warning',
                                                                        'text-red-400 hover:text-red-500 focus:ring-red-500': t.type==='error',
                                                                    }">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
    </div>

    <!-- Floating Success Message Component -->
    <script>
        // Provide a global fallback for product form validation so modal-loaded HTML
        // (inserted via innerHTML) can call validateProductForm() even when inline
        // <script> blocks inside the loaded HTML are not executed.
        window.validateProductForm = window.validateProductForm || function() {
            try {
                const nameEl = document.getElementById('name');
                const categoryEl = document.getElementById('category_id');
                const priceEl = document.getElementById('price');

                const name = nameEl ? nameEl.value.trim() : '';
                const category = categoryEl ? categoryEl.value : '';
                const price = priceEl ? parseFloat(priceEl.value) : NaN;

                if (!name) {
                    alert('Product name is required');
                    return false;
                }

                if (!category) {
                    alert('Category is required');
                    return false;
                }

                if (isNaN(price) || price < 0) {
                    alert('Valid price is required');
                    return false;
                }

                return true;
            } catch (err) {
                // If anything unexpected happens, allow the form to submit so server-side
                // validation can handle it. Log to console for debugging.
                console.error('validateProductForm error:', err);
                return true;
            }
        };
    </script>

    <x-floating-success />
</body>
</html>
