@extends('layouts.app', ['title' => 'Dropdown Components Demo'])

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Dropdown Components Demo</h2>
        <p class="text-gray-600 dark:text-gray-400">Interactive examples of all dropdown component variations</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Basic Dropdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Dropdown</h3>
            <div class="flex justify-center">
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="btn-primary">
                            Basic Menu
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>
                    
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Option 1</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Option 2</a>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                    <a href="#" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">Delete</a>
                </x-dropdown>
            </div>
        </div>

        <!-- Actions Dropdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions Dropdown</h3>
            <div class="flex justify-center">
                <x-dropdown-actions>
                    <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="mr-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="mr-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-600 my-1"></div>
                    <button class="group flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 text-left">
                        <svg class="mr-3 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                </x-dropdown-actions>
            </div>
        </div>

        <!-- Programmatic Dropdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Programmatic Items</h3>
            <div class="flex justify-center">
                @php
                $menuItems = [
                    ['type' => 'header', 'label' => 'Navigation'],
                    [
                        'type' => 'link',
                        'url' => route('dashboard'),
                        'label' => 'Dashboard',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4m8-4v4"></path></svg>',
                        'shortcut' => 'Ctrl+D'
                    ],
                    [
                        'type' => 'link',
                        'url' => Route::has('products.index') ? route('products.index') : '#',
                        'label' => 'Products',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                        'badge' => '23'
                    ],
                    ['type' => 'divider'],
                    ['type' => 'header', 'label' => 'Actions'],
                    [
                        'type' => 'button',
                        'label' => 'Refresh Data',
                        'style' => 'success',
                        'onclick' => 'alert("Refreshing...")',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
                    ],
                    [
                        'type' => 'button',
                        'label' => 'Clear Cache',
                        'style' => 'warning',
                        'onclick' => 'alert("Cache cleared!")',
                        'confirm' => 'Are you sure you want to clear the cache?',
                        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
                    ]
                ];
                @endphp
                
                <x-dropdown :items="$menuItems" width="64">
                    <x-slot name="trigger">
                        <button class="btn-secondary">
                            Complex Menu
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>

        <!-- Notification Dropdown -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notification Style</h3>
            <div class="flex justify-center">
                <x-dropdown width="80" align="right">
                    <x-slot name="trigger">
                        <button class="relative p-3 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9.586 14.414L7.5 16.5 2 11l2.586-2.586L9.586 14.414z"></path>
                            </svg>
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                        </button>
                    </x-slot>
                    
                    <x-slot name="header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">3 new</span>
                        </div>
                    </x-slot>
                    
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">New order received</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Order #12345 from John Doe</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">2 minutes ago</p>
                    </div>
                    
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Low stock alert</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Product "Widget A" is running low</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">1 hour ago</p>
                    </div>
                    
                    <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Payment received</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Rp 1,500,000 from Invoice #INV-001</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">3 hours ago</p>
                    </div>
                    
                    <div class="p-3 border-t border-gray-200 dark:border-gray-600">
                        <a href="#" class="block text-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">View all notifications</a>
                    </div>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Code Examples -->
    <div class="mt-12 bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Usage Examples</h3>
        
        <div class="space-y-6">
            <!-- Basic Dropdown Example -->
            <div>
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Basic Dropdown</h4>
                <pre class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 text-sm overflow-x-auto"><code>&lt;x-dropdown&gt;
    &lt;x-slot name="trigger"&gt;
        &lt;button class="btn-primary"&gt;Menu&lt;/button&gt;
    &lt;/x-slot&gt;
    
    &lt;a href="#" class="block px-4 py-2 hover:bg-gray-100"&gt;Option 1&lt;/a&gt;
    &lt;a href="#" class="block px-4 py-2 hover:bg-gray-100"&gt;Option 2&lt;/a&gt;
&lt;/x-dropdown&gt;</code></pre>
            </div>

            <!-- Actions Dropdown Example -->
            <div>
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Actions Dropdown</h4>
                <pre class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 text-sm overflow-x-auto"><code>&lt;x-dropdown-actions&gt;
    &lt;a href="/edit" class="block px-4 py-2 hover:bg-gray-100"&gt;Edit&lt;/a&gt;
    &lt;button class="block w-full text-left px-4 py-2 text-red-600"&gt;Delete&lt;/button&gt;
&lt;/x-dropdown-actions&gt;</code></pre>
            </div>

            <!-- User Dropdown Example -->
            <div>
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">User Dropdown</h4>
                <pre class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 text-sm overflow-x-auto"><code>&lt;x-dropdown-user /&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
