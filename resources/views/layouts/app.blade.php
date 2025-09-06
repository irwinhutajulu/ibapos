<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="if(darkMode) $el.classList.add('dark'); else $el.classList.remove('dark')" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IBAPOS' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
    <script>
        // Safe stub: queue notifications fired before Alpine toast component is ready
        window._notifyQueue = window._notifyQueue || [];
        window.notify = window.notify || function (msg, type = 'info', ttl = 3500) {
            window._notifyQueue.push({ msg, type, ttl });
        };
    </script>
</head>
<body :class="darkMode ? 'bg-gray-900 text-gray-100' : 'bg-gray-50 text-gray-900'" class="h-full">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
             class="fixed inset-y-0 z-40 w-64 transform bg-white border-r border-gray-200 transition-transform duration-200 lg:static lg:inset-auto">
            <div class="h-16 flex items-center px-4 border-b">
                <span class="font-semibold text-lg">IBAPOS</span>
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode); if(darkMode) $root.classList.toggle('dark', darkMode)" class="ml-auto px-2 py-1 rounded text-xs bg-gray-200 dark:bg-gray-800 dark:text-gray-100">🌓 Dark Mode</button>
            </div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 font-medium text-gray-700">
                    <span>🏠</span> <span>Dashboard</span>
                </a>
                @can('sales.create')
                <a href="{{ route('pos.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🧾</span> <span>POS</span>
                </a>
                @endcan
                @can('products.read')
                <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📦</span> <span>Products</span>
                </a>
                @endcan
                @can('categories.read')
                <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🏷️</span> <span>Categories</span>
                </a>
                @endcan
                    @can('customers.read')
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                        <span>👤</span> <span>Customers</span>
                    </a>
                    @endcan
                    @can('suppliers.read')
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                        <span>🏢</span> <span>Suppliers</span>
                    </a>
                    @endcan
                @can('sales.read')
                <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🛒</span> <span>Sales</span>
                </a>
                @endcan
                @can('purchases.read')
                <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📥</span> <span>Purchases</span>
                </a>
                @endcan
                @can('stocks.read')
                <a href="{{ route('stocks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📦</span> <span>Stocks</span>
                </a>
                <a href="{{ route('stock-adjustments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🏷️</span> <span>Stock Adjustments</span>
                </a>
                @endcan
                @can('stock_mutations.request')
                <a href="{{ route('stock-mutations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🔁</span> <span>Stock Mutations</span>
                </a>
                @endcan
                @can('stocks.read')
                <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>⏸️</span> <span>Reservations</span>
                </a>
                @endcan
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📈</span> <span>Reports</span>
                </a>
                @can('admin.users')
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>⚙️</span> <span>Settings</span>
                </a>
                @endcan
                @can('admin.permissions')
                <a href="{{ route('admin.notifications.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🔔</span> <span>Notifications</span>
                </a>
                @endcan
            </nav>
        </div>
        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between bg-white border-b px-4">
                <div class="flex items-center gap-3">
                    <button class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-md hover:bg-gray-100"
                            @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <input type="text" placeholder="Search..." class="hidden md:block w-64 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring" />
                    @auth
                    <form method="POST" action="{{ route('active-location.set') }}" class="flex items-center gap-2">
                        @csrf
                        <label class="text-xs text-gray-500">Lokasi:</label>
                        <select name="location_id" class="px-2 py-1 border rounded-md text-sm" onchange="this.form.submit()">
                            @php($activeId = session('active_location_id'))
                            @foreach(auth()->user()->locations as $loc)
                                <option value="{{ $loc->id }}" @selected($activeId == $loc->id)>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </form>
                                        @endauth
                                        @guest
                                            <a href="{{ route('login') }}" class="text-sm px-3 py-2 border rounded-md">Login</a>
                                        @else
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button class="text-sm px-3 py-2 border rounded-md">Logout</button>
                                            </form>
                                        @endguest
                                        <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                </div>
            </header>

                        <main class="p-4 lg:p-6">
                @isset($breadcrumbs)
                    <x-breadcrumbs :items="$breadcrumbs['items'] ?? []" :title="$breadcrumbs['title'] ?? ($title ?? null)" />
                @endisset
                <h1 class="text-xl font-bold mb-4">{{ $title ?? 'Dashboard' }}</h1>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
                        @auth
                        <script>
                            window.appActiveLocationId = {{ (int) session('active_location_id') }};
                        </script>
                        @endauth
                        @stack('scripts')
            
                                    <!-- Toast notifications -->
                                    <div x-data="{ toasts: [], add(t){ const id = Date.now()+Math.random(); this.toasts.push({ ...t, id }); setTimeout(()=>this.remove(id), t.ttl ?? 3500); }, remove(id){ this.toasts = this.toasts.filter(x=>x.id!==id) } }"
                                             x-init="const api = $data; window.notify = (msg, type='info', ttl=3500) => api.add({msg, type, ttl}); window.addEventListener('notify', e => api.add(e.detail)); if (window._notifyQueue && window._notifyQueue.length) { window._notifyQueue.splice(0).forEach(d => api.add(d)); }"
                                             class="fixed top-4 right-4 z-50 space-y-2">
                                            <template x-for="t in toasts" :key="t.id">
                                                    <div class="max-w-xs shadow-lg rounded-md px-4 py-3 text-sm text-white"
                                                             :class="{
                                                                 'bg-gray-800': t.type==='info',
                                                                 'bg-green-600': t.type==='success',
                                                                 'bg-yellow-600': t.type==='warning',
                                                                 'bg-red-600': t.type==='error',
                                                             }">
                                                            <div x-text="t.msg"></div>
                                                    </div>
                                            </template>
                                    </div>
        </div>
    </div>
</body>
</html>
