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
        
        /* Modal scrollbar - explicit styles */
        .modal-scroll {
            overflow-y: auto !important;
            scrollbar-width: thin !important;
            scrollbar-color: rgba(156, 163, 175, 0.6) rgba(0, 0, 0, 0.1) !important;
        }
        .modal-scroll::-webkit-scrollbar { 
            width: 8px !important; 
            height: 8px !important;
        }
        .modal-scroll::-webkit-scrollbar-track { 
            background: rgba(0, 0, 0, 0.1) !important; 
            border-radius: 4px !important; 
        }
        .modal-scroll::-webkit-scrollbar-thumb { 
            background: rgba(156, 163, 175, 0.6) !important; 
            border-radius: 4px !important; 
        }
        .modal-scroll::-webkit-scrollbar-thumb:hover { 
            background: rgba(156, 163, 175, 0.8) !important; 
        }
        
        /* Smooth transitions */
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }
        
        /* Glass effect */
        .glass { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.8); }
        .glass-dark { backdrop-filter: blur(10px); background: rgba(31, 41, 55, 0.8); }
    </style>
    <script>
        window.appBaseUrl = "{{ url('/') }}";
        // Safe stub: queue notifications fired before Alpine toast component is ready
        window._notifyQueue = window._notifyQueue || [];
        window.notify = window.notify || function (msg, type = 'info', ttl = 3500) {
            window._notifyQueue.push({ msg, type, ttl });
        };
    </script>
    
    <!-- Hidden store information will be populated dynamically -->
    <div style="display: none;">
        <span id="store-name">Loading...</span>
        <span id="store-address">Loading...</span>
        <span id="store-phone">Loading...</span>
    </div>
</head>
<body class="h-full bg-slate-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 antialiased">
    <div class="min-h-screen flex">
        
      <!-- Page Content -->
        <div class="flex-1 flex flex-col min-w-0" x-data="pos()">
            <!-- Header -->
              <header class="border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-20">
                <!-- Header Title -->
                  <div class="h-16 flex items-center justify-between px-3 lg:px-6 py-2 lg:py-4 bg-white/80 dark:bg-gray-800/80">
                    <div class="flex items-center space-x-4 ml-auto">
                          <!-- Location Selector Component -->
                          <x-location-selector />

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
                    </div>

                <!-- Search Bar -->
                  <div class="bg-slate-100 dark:bg-gray-900 border-t border-gray-200/50 dark:border-gray-700/50 px-4 lg:px-8 py-4">
                      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                              <div class="flex items-center space-x-4 flex-1">
                                  <!-- Product Search -->
                                  <div x-data="{show:false}" @click.away="show=false" class="relative flex-1">
                                    <div class="flex gap-3">
                                      <div class="flex-1 relative">
                                              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                              <input 
                                                  x-model="keyword" 
                                                  @input.debounce.300ms="search();show=true" 
                                                  @focus="show=true" 
                                                  type="text" 
                                                  placeholder="Search products..." 
                                                  class="block w-full pl-10 pr-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                      
                                      <!-- Search Results -->
                                      <template x-if="show && results.length">
                                          <div class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg max-h-80 overflow-auto">
                                              <template x-for="p in results" :key="p.id">
                                                  <div @click="addToCart(p);show=false" class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-colors">
                                                      <div class="flex items-center justify-between">
                                                          <div class="flex-1">
                                                              <div class="font-small text-gray-900 dark:text-white" x-text="p.name"></div>
                                                              <div class="font-small text-gray-900 dark:text-white" x-text="format(p.price)"></div>
                                                            </div>
                                                          <div class="text-right">
                                                              
                                                              <div class="text-xs" 
                                                                  :class="(p.stocks && p.stocks.length > 0) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                                                  x-text="(p.stocks && p.stocks.length > 0) ? `Stock: ${p.stocks.reduce((total, s) => total + parseFloat(s.qty || 0), 0)}` : 'No Stock'">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                      
                                      <template x-if="show && keyword && results.length === 0">
                                          <div class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg p-4">
                                              <div class="text-center text-gray-500 dark:text-gray-400">
                                                  <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                  </svg>
                                                  <p>No products found</p>
                                              </div>
                                          </div>
                                        </template>
                                    </div>
                                
                                <!-- Clear All Button -->
                                <!-- Kebab Menu for Clear & Draft -->
                                <div x-data="{ open: false }" class="relative">
                                  <button @click="open = !open" class="py-2 text-gray-700 dark:text-gray-100 rounded-xl flex items-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <circle cx="12" cy="5" r="2" />
                                      <circle cx="12" cy="12" r="2" />
                                      <circle cx="12" cy="19" r="2" />
                                    </svg>
                                  </button>
                                  <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-40 rounded-xl shadow-lg z-30 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex flex-col">
                                    <button @click="clearAll(); open = false" class="w-full flex flex-col items-start px-4 py-2 text-red-600 gap-2">
                                      <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Hapus Semua
                                      </span>
                                    </button>
                                    <button @click="showDraftModal = true; open = false" class="w-full flex items-center px-4 py-2 text-amber-700 gap-2">
                                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                      </svg>
                                      Draft Sales
                                    </button>
                                  </div>
                                </div>
                              </div>
                        </div>
                    </div>
                </header>

            <!-- Main Content -->
              <main class="flex-1 p-4 lg:p-8">
                <div class="overflow-auto max-h-screen">
                <div class="space-y-6">

                  <div class="max-w-md mx-auto">
                    <!-- Cart & Checkout -->
                    <div class="space-y-6 px-1">
                      <!-- Customer Selection -->
                      <div class="card">
                        <div class="card-body">
                          <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Customer</h3>
                          </div>
                          <div x-data="{q:'',results:[],selected:null,show:false,showModal:false,newCustomer:{name:'',phone:'',address:''}}" @click.away="show=false">
                            <div x-show="!selected" class="relative px-2">
                              <input 
                                type="text" 
                                class="form-input w-full border border-gray-200 dark:border-gray-600 rounded bg-white dark:bg-gray-700 px-4" 
                                placeholder="Search customer..." 
                                x-model="q" 
                                @input.debounce.300ms="fetch(`${window.appBaseUrl}/api/customers?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(data=>{results=data;show=true})"
                                @focus="show=q.length>0">
                              
                              <template x-if="show && results.length">
                                <div class="absolute z-20 left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 max-h-40 overflow-auto">
                                  <template x-for="item in results" :key="item.id">
                                    <div @click="selected=item;show=false;q=item.name" class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                      <div class="font-medium text-gray-900 dark:text-white" x-text="item.name"></div>
                                      <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.phone"></div>
                                      <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.address"></div>
                                      </div>
                                    </template>
                                  </div>
                                </template>
                              
                              <template x-if="show && q && results.length === 0">
                                <div class="absolute z-20 left-0 right-0 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 p-4">
                                  <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Customer not found</p>
                                    <button type="button" class="btn-primary btn-sm" @click="showModal=true">
                                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                      Add New Customer
                                      </button>
                                    </div>
                                  </div>
                                </template>
                            </div>
                            
                            <input type="hidden" name="customer_id" :value="selected?.id">
                            
                            <template x-if="selected">
                              <div class="mt-2 py-1 px-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center justify-between">
                                  <div>
                                    <p class="font-medium text-gray-900 dark:text-white" x-text="selected.name"></p>
                                    <p class="text-sm text-gray-900 dark:text-white" x-text="selected.phone"></p>
                                  </div>
                                  <button @click="selected=null;q=''" class="text-gray-900 dark:text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                  </button>
                                </div>
                              </div>
                            </template>

                            <!-- Modal for new customer -->
                            <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40 p-4">
                              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[80vh] overflow-hidden">
                                <!-- Header -->
                                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Pelanggan Baru</h3>
                                </div>

                                <!-- Body (scrollable) -->
                                <div class="p-6 modal-scroll" style="max-height: calc(80vh - 140px);">
                                  <div class="space-y-4">
                                    <div class="flex items-center gap-4">
                                      <label class="block text-gray-700 dark:text-gray-200 w-32">Nama</label>
                                      <input type="text" class="form-input bg-white dark:bg-gray-700 w-full border border-gray-200 dark:border-gray-600 px-2" placeholder="Nama Pelanggan" x-model="newCustomer.name">
                                    </div>

                                    <div class="flex items-center gap-4">
                                      <label class="block text-gray-700 dark:text-gray-200 w-32">Telepon</label>
                                      <input type="tel" class="form-input bg-white dark:bg-gray-700 w-full border border-gray-200 dark:border-gray-600 px-2" placeholder="Nomor Telepon" x-model="newCustomer.phone">
                                    </div>

                                    <div>
                                      <label class="block text-gray-700 dark:text-gray-200 mb-2">Alamat</label>
                                      <textarea class="form-textarea bg-white dark:bg-gray-700 w-full border border-gray-200 dark:border-gray-600 px-2" rows="4" placeholder="Alamat" x-model="newCustomer.address"></textarea>
                                    </div>
                                  </div>
                                </div>

                                <!-- Footer -->
                                <div class="px-6 pb-4 flex justify-end gap-3">
                                  <button type="button" class="btn-secondary" @click="showModal=false">Cancel</button>
                                  <button type="button" class="btn-primary px-3 py-1" @click="
                                    fetch(`${window.appBaseUrl}/customers`, {
                                      method: 'POST',
                                      headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content
                                      },
                                      body: JSON.stringify(newCustomer)
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                      if(data.id){ selected = data; showModal=false; q=data.name; results=[]; window.notify('Customer added successfully','success'); }
                                      else { window.notify('Failed to add customer','error'); }
                                    })
                                    .catch(()=>window.notify('Failed to add customer','error'))
                                  ">Save</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Shopping Cart -->
                      <div class="card">
                        <div class="card-header">
                          <div class="flex items-center justify-between pb-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Shopping Cart</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300" x-text="cart.length + ' items'"></span>
                          </div>
                        </div>
                        <div class="card-body">
                          <template x-if="cart.length === 0">
                            <div class="text-center py-8">
                              <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4"></path>
                              </svg>
                              <p class="text-gray-500 dark:text-gray-400">Cart is empty</p>
                              <p class="text-sm text-gray-400 dark:text-gray-500">Search and add products to get started</p>
                            </div>
                          </template>
                          
                          <div class="space-y-4">
                            <template x-for="(it,idx) in cart" :key="idx">
                              <div class="p-4 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-start justify-between mb-3">
                                  <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-white" x-text="it.name"></h4>
                                  </div>
                                  <button @click="cart.splice(idx,1)" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                  </button>
                                </div>
                                
                                <div class="grid grid-cols-3 gap-3 mb-3">
                                  <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Quantity</label>
                                    <input type="number" step="0.001" class="w-full px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-model.number="it.qty">
                                  </div>
                                  <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price</label>
                                    <div class="px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300" x-text="format(it.price)"></div>
                                  </div>
                                  <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Discount</label>
                                    <input type="number" step="0.01" class="w-full px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-model.number="it.discount" >
                                  </div>
                                </div>
                                
                                <div class="mb-3">
                                  <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Source Location</label>
                                  <select class="w-full px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-model.number="it.source_location_id">
                                    <template x-for="loc in locations" :key="loc.id">
                                      <option :value="loc.id" :selected="loc.id === window.appActiveLocationId" x-text="`${loc.name} (Stok: ${formatStock((it.stocks.find(s => s.location_id === loc.id) || {}).qty || 0)})`"></option>
                                    </template>
                                    <option :value="null" :selected="!window.appActiveLocationId">Other Location</option>
                                  </select>
                                </div>
                                
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                                  <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                                  <span class="font-semibold text-gray-900 dark:text-white" x-text="format(itemSubtotal(it, idx))"></span>
                                </div>
                              </div>
                            </template>
                          </div>
                        </div>
                      </div>

                      <!-- Checkout -->
                      <div class="card" x-show="cart.length > 0" x-transition>
                        <div class="card-body space-y-4">
                          <!-- Total Summary -->
                          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                            <div class="flex justify-between items-center text-lg font-semibold">
                              <span class="text-gray-900 dark:text-white">Total</span>
                              <div class="flex items-center gap-4">
                                <span class="text-gray-900 dark:text-white" x-text="format(total())"></span>
                                <button @click="showCheckoutModal = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                                  Check Out
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>


                    </div>
                  </div>
                </div>
                </div>
            </main>
            
            <!-- Checkout Modal -->
            <div x-show="showCheckoutModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 rounded-t-2xl">
                  <div class="flex items-center justify-between >
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Checkout</h3>
                    <button @click="showCheckoutModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="p-6">
                  @include('pos.partials._checkout')
                </div>
              </div>
            </div>
            
            <!-- Draft Sales Modal -->
            <div x-show="showDraftModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 z-50" style="z-index: 9999;">
              <div class="fixed inset-4 bg-white dark:bg-gray-800 rounded-2xl shadow-xl" style="top: 5vh; bottom: 5vh; left: 10%; right: 10%;">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Draft Sales</h3>
                  <button @click="showDraftModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
                
                <!-- Scrollable Content -->
                <div style="height: calc(100% - 80px); overflow-y: scroll !important; padding: 24px;">
                  <div x-init="loadDraftSales()">
                    <!-- Original draft sales content -->
                    <div class="space-y-4">
                      <template x-if="draftSales.length === 0">
                        <div class="text-center py-8">
                          <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          <p class="text-gray-500 dark:text-gray-400">No draft sales found</p>
                        </div>
                      </template>
                    
                    <template x-for="draft in draftSales" :key="draft.id">
                      <div class="border border-gray-200 dark:border-gray-600 rounded-xl p-4 bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-start justify-between mb-3">
                          <div class="flex-1">
                            <h4 class="font-medium text-gray-900 dark:text-white" x-text="draft.invoice_no"></h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="new Date(draft.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })"></p>
                            <p class="text-sm text-gray-600 dark:text-gray-300" x-text="`${draft.items?.length || 0} items`"></p>
                          </div>
                          <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="format(draft.total || 0)"></p>
                            <div class="flex gap-2 mt-2">
                              <button @click="loadDraftToCart(draft.id)" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors">
                                Load
                              </button>
                              <button @click="deleteDraft(draft.id)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded-lg transition-colors">
                                Delete
                              </button>
                            </div>
                          </div>
                        </div>
                        
                        <template x-if="draft.items && draft.items.length > 0">
                          <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                            <h5 class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Items:</h5>
                            <div class="space-y-1 max-h-24" style="overflow-y: auto;">
                              <template x-for="item in draft.items" :key="item.id">
                                <div class="flex justify-between text-sm">
                                  <span class="text-gray-700 dark:text-gray-300" x-text="`${item.product?.name || 'Unknown'} (${item.qty}x)`"></span>
                                  <span class="text-gray-600 dark:text-gray-400" x-text="format(item.subtotal || 0)"></span>
                                </div>
                              </template>
                            </div>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
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
         class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 space-y-3 max-w-md">
        <template x-for="t in toasts" :key="t.id">
            <div x-transition:enter="transform ease-out duration-800"
                 x-transition:enter-start="-translate-y-full opacity-50"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transform ease-in duration-800 delay-500"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="-translate-y-full opacity-0"
                 class="rounded-xl border max-w-md shadow-lg flex items-center px-4 py-2"
                 :class="{
                     'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100': t.type==='info',
                     'bg-green-600 dark:bg-green-700 border-green-500 dark:border-green-600 text-white': t.type==='success',
                     'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 text-yellow-900 dark:text-yellow-200': t.type==='warning',
                     'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 text-red-900 dark:text-red-200': t.type==='error',
                 }">
                <div class="flex-shrink-0">
                    <!-- Info Icon -->
                    <svg x-show="t.type === 'info'" class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <!-- Success Icon -->
                    <svg x-show="t.type === 'success'" class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <span class="ml-3 text-sm font-normal" x-text="t.msg"></span>
            </div>
        </template>
    </div>

    <script>
    function pos() {
      return {
        keyword: '',
        results: [],
        cart: [],
        locations: [],
        payments: [],
        showCheckoutModal: false,
        showDraftModal: false,
        draftSales: [],
        saleProcessed: false,
        additional_fee: null,
        discount: null,
        activeLocationId: window.appActiveLocationId || null,
        
        async search() {
          if (!this.keyword.trim()) {
            this.results = [];
            return;
          }
          
          try {
            const url = new URL(`${window.appBaseUrl}/api/products`);
            url.searchParams.set('q', this.keyword);
            const r = await fetch(url);
            const data = await r.json();
            this.results = data || [];
          } catch (error) {
            console.error('Search error:', error);
            this.results = [];
          }
        },
        
        addToCart(p) {
          const existingIndex = this.cart.findIndex(item => item.id === p.id);
          if (existingIndex >= 0) {
            this.cart[existingIndex].qty += 1;
          } else {
            this.cart.push({ 
              id: p.id, 
              name: p.name, 
              qty: 1, 
              price: Number(p.price) || 0, 
              discount: 0, 
              source_location_id: window.appActiveLocationId || null,
              stocks: p.stocks || []
            });
          }
          this.keyword = '';
          this.results = [];
        },
        
        itemSubtotal(it, idx) { 
          return (Number(it.price) - Number(it.discount || 0)) * Number(it.qty || 0); 
        },
        
        total() { 
          const subtotal = this.cart.reduce((a, c) => a + this.itemSubtotal(c), 0);
          const fee = parseFloat(this.additional_fee) || 0;
          const disc = parseFloat(this.discount) || 0;
          return Math.max(0, subtotal + fee - disc); 
        },
        
        payTotal() { 
          return this.payments.reduce((a,p) => a + Number(p.amount || 0), 0); 
        },
        
        format(v) { 
          return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(v || 0); 
        },
        
        formatStock(qty) {
          const num = parseFloat(qty) || 0;
          // Check if it's a whole number
          if (num % 1 === 0) {
            return num.toString();
          }
          // Return with maximum 2 decimal places, removing trailing zeros
          return parseFloat(num.toFixed(2)).toString();
        },
        
        clearAll() {
          if (confirm('Are you sure you want to clear all items?')) {
            this.cart = [];
            this.payments = [];
            this.saleProcessed = false;
            this.additional_fee = null;
            this.discount = null;
          }
        },
        
        async saveDraft() {
          if (this.cart.length === 0) {
            window.notify('Cart is empty', 'warning');
            return;
          }
          
          await this.processTransaction('draft');
        },
        
        async checkout() {
          if (this.cart.length === 0) {
            window.notify('Cart is empty', 'warning');
            return;
          }
          
          if (this.payTotal() < this.total()) {
            window.notify('Payment amount is insufficient', 'warning');
            return;
          }
          
          await this.processTransaction('posted');
        },
        
        async processTransaction(status = 'draft') {
          try {
            // Validate stock availability
            const batch = this.cart.map(it => ({ 
              product_id: it.id, 
              location_id: it.source_location_id || window.appActiveLocationId || null 
            }));
            
            const check = await fetch(`${window.appBaseUrl}/api/stock/available-batch`, { 
              method: 'POST', 
              headers: { 
                'Content-Type':'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
              }, 
              body: JSON.stringify({ items: batch }) 
            });
            
            if (check.ok) {
              const resCheck = await check.json();
              for (let i = 0; i < this.cart.length; i++) {
                const it = this.cart[i];
                const av = resCheck.data[i]?.available ?? 0;
                const requestedQty = Number(it.qty);
                if (requestedQty > av) { 
                  const sourceLocation = (it.source_location_id && this.locations.find(l => l.id === it.source_location_id)?.name) || 'Active Location';
                  window.notify(`Insufficient stock for ${it.name} at ${sourceLocation}. Available: ${av}, Requested: ${requestedQty}`, 'error');
                  return; 
                }
              }
            } else {
              window.notify('Failed to validate stock availability', 'warning');
            }

            const payload = {
              invoice_no: (status === 'draft' ? 'DR-' : 'INV-') + Date.now(),
              date: new Date().toISOString(),
              items: this.cart.map(it => ({ 
                product_id: it.id, 
                qty: it.qty, 
                price: it.price, 
                discount: it.discount, 
                subtotal: this.itemSubtotal(it), 
                source_location_id: it.source_location_id || window.appActiveLocationId || null 
              })),
              payments: this.payments,
              status: status
            };
            
            const res = await fetch(`${window.appBaseUrl}/sales`, { 
              method: 'POST', 
              headers: { 
                'Content-Type': 'application/json', 
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
              }, 
              body: JSON.stringify(payload) 
            });
            
            if (res.ok) { 
              this.cart = []; 
              this.payments = [];
              
              // Set saleProcessed to true for successful posted transactions (not drafts)
              if (status === 'posted') {
                this.saleProcessed = true;
                // Keep modal open for print receipt after successful transaction
                // User can close manually after printing
              } else {
                // Close modal for draft transactions
                this.showCheckoutModal = false;
              }
              
              window.notify(`Transaction ${status === 'draft' ? 'saved as draft' : 'completed'} successfully`, 'success');
            } else { 
              const errorText = await res.text(); 
              window.notify('Transaction failed: ' + errorText, 'error');
            }
          } catch (error) {
            console.error('Transaction error:', error);
            window.notify('Transaction failed', 'error');
          }
        },
        
        printStruk() {
          // Open receipt window
          const receiptWindow = window.open(window.appBaseUrl + '/pos/print-receipt', '_blank', 'width=400,height=600');
          
          // Get store info from current location or fallback to defaults
          const currentLoc = this.currentLocation || {};
          const storeName = currentLoc.name || 'IBA POS - Istana Batu Alam';
          const storeAddress = currentLoc.address || 'Jl. Raya Batu Alam No. 123';
          const storePhone = currentLoc.phone || 'Telp: 021-7654321';
          
          // Calculate subtotal from cart items
          const subtotal = this.cart.reduce((sum, item) => sum + this.itemSubtotal(item), 0);
          
          // Prepare receipt data
          const receiptData = {
            store: {
              name: storeName,
              address: storeAddress,
              phone: storePhone,
            },
            trx: {
              date: new Date().toLocaleDateString('id-ID') + ' ' + new Date().toLocaleTimeString('id-ID'),
              no: this.trxNo || ('TRX' + Date.now()),
              buyer: this.buyerName || 'Customer',
            },
            products: this.cart.map(item => ({
              name: item.name || 'Unknown Product',
              qty: item.qty || 1,
              price: Number(item.price) || 0,
              subtotal: this.itemSubtotal(item)
            })),
            total: this.total(),
            payment: this.payTotal(),
            change: Math.max(0, this.payTotal() - this.total()),
            additional_fee: parseFloat(this.additional_fee) || 0,
            discount: parseFloat(this.discount) || 0,
            subtotal: subtotal,
            note: 'Terima kasih atas pembelian Anda!'
          };
          
          console.log('Sending receipt data with location:', {
            locationId: this.activeLocationId,
            locationName: storeName,
            receiptData: receiptData
          });
          
          // Send data to receipt window when it's loaded
          const sendData = () => {
            receiptWindow.postMessage({ type: 'RECEIPT_DATA', data: receiptData }, '*');
          };
          
          // Wait for window to load before sending data
          const timer = setInterval(() => {
            if (receiptWindow && receiptWindow.document && receiptWindow.document.readyState === 'complete') {
              clearInterval(timer);
              sendData();
            }
          }, 300);
          
          // Fallback - send data after 1 second regardless
          setTimeout(() => {
            sendData();
          }, 1000);
        },
        
        async loadDraftSales() {
          try {
            const response = await fetch(`${window.appBaseUrl}/api/sales?status=draft&_cache=${Date.now()}`, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              }
            });
            if (response.ok) {
              const data = await response.json();
              this.draftSales = data.data || []; // Extract data array from paginated response
            } else {
              window.notify('Failed to load draft sales', 'error');
            }
          } catch (error) {
            console.error('Error loading draft sales:', error);
            window.notify('Failed to load draft sales', 'error');
          }
        },
        
        async loadDraftToCart(draftId) {
          try {
            const response = await fetch(`${window.appBaseUrl}/api/sales/${draftId}`, {
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              }
            });
            if (response.ok) {
              const draft = await response.json();
              
              // Clear current cart
              this.cart = [];
              this.payments = [];
              
              // Load draft items to cart
              if (draft.items) {
                this.cart = draft.items.map(item => ({
                  id: item.product_id,
                  name: item.product?.name || 'Unknown Product',
                  qty: item.qty,
                  price: item.price,
                  discount: item.discount || 0,
                  source_location_id: item.source_location_id,
                  stocks: item.product?.stocks || []
                }));
              }
              
              // Load payments if any
              if (draft.payments) {
                this.payments = draft.payments.map(payment => ({
                  type: payment.type,
                  amount: payment.amount,
                  reference: payment.reference || ''
                }));
              }
              
              this.showDraftModal = false;
              window.notify('Draft loaded to cart successfully', 'success');
            } else {
              window.notify('Failed to load draft', 'error');
            }
          } catch (error) {
            console.error('Error loading draft:', error);
            window.notify('Failed to load draft', 'error');
          }
        },
        
        async deleteDraft(draftId) {
          if (!confirm('Are you sure you want to delete this draft?')) return;
          
          try {
            const response = await fetch(`${window.appBaseUrl}/api/sales/${draftId}`, {
              method: 'DELETE',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
              }
            });
            
            if (response.ok) {
              this.draftSales = this.draftSales.filter(draft => draft.id !== draftId);
              window.notify('Draft deleted successfully', 'success');
            } else {
              window.notify('Failed to delete draft', 'error');
            }
          } catch (error) {
            console.error('Error deleting draft:', error);
            window.notify('Failed to delete draft', 'error');
          }
        },
        
        async init() {
          try { 
            const r = await fetch(`${window.appBaseUrl}/api/locations`); 
            this.locations = r.ok ? await r.json() : [];
            
            // Update store info based on active location
            this.updateStoreInfo();
            
            // Listen for location changes
            this.startLocationWatcher();
          } catch (error) {
            console.error('Failed to load locations:', error);
          }
          
          // Initialize with one cash payment method
          this.payments.push({type: 'cash', amount: 0, reference: ''});
        },
        
        startLocationWatcher() {
          // Watch for changes in window.appActiveLocationId
          const checkLocationChange = () => {
            const newLocationId = window.appActiveLocationId;
            if (this.activeLocationId !== newLocationId) {
              this.activeLocationId = newLocationId;
              this.updateStoreInfo();
            }
          };
          
          // Check every 500ms for location changes
          setInterval(checkLocationChange, 500);
        },
        
        updateStoreInfo() {
          const activeLocationId = this.activeLocationId || window.appActiveLocationId;
          const activeLocation = this.locations.find(loc => loc.id === activeLocationId);
          
          if (activeLocation) {
            // Update hidden elements for receipt printing
            const storeNameEl = document.getElementById('store-name');
            const storeAddressEl = document.getElementById('store-address');
            const storePhoneEl = document.getElementById('store-phone');
            
            if (storeNameEl) storeNameEl.textContent = `{{ config('app.name', 'IBA POS') }} - ${activeLocation.name}`;
            if (storeAddressEl) storeAddressEl.textContent = activeLocation.address || 'Alamat tidak tersedia';
            if (storePhoneEl) storePhoneEl.textContent = activeLocation.phone ? `Telp / WA: ${activeLocation.phone}` : 'Telp: -';
            
            console.log('Store info updated for location:', activeLocation.name);
          } else {
            // Fallback to default values
            const storeNameEl = document.getElementById('store-name');
            const storeAddressEl = document.getElementById('store-address');
            const storePhoneEl = document.getElementById('store-phone');
            
            if (storeNameEl) storeNameEl.textContent = '{{ config('app.name', 'IBA POS') }}';
            if (storeAddressEl) storeAddressEl.textContent = 'Alamat tidak tersedia';
            if (storePhoneEl) storePhoneEl.textContent = 'Telp: -';
            
            console.log('Store info reset to default values');
          }
        }
      }
    }
    </script>
</body>
</html>
