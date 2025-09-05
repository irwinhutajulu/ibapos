<!DOCTYPE html>
<html lang="en" class="h-full" x-data="{ sidebarOpen: false }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'IBAPOS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="h-full bg-gray-50 text-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
             class="fixed inset-y-0 z-40 w-64 transform bg-white border-r border-gray-200 transition-transform duration-200 lg:static lg:inset-auto">
            <div class="h-16 flex items-center px-4 border-b">
                <span class="font-semibold text-lg">IBAPOS</span>
            </div>
            <nav class="p-4 space-y-1 text-sm">
                <a href="/" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 font-medium text-gray-700">
                    <span>🏠</span> <span>Dashboard</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🧾</span> <span>POS</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📦</span> <span>Products</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🛒</span> <span>Sales</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📥</span> <span>Purchases</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>🏷️</span> <span>Stocks</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>📈</span> <span>Reports</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700">
                    <span>⚙️</span> <span>Settings</span>
                </a>
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
                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                </div>
            </header>

            <main class="p-4 lg:p-6">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
