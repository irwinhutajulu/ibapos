@extends('layouts.app')

@section('content')
<div x-data="pos()" class="space-y-6">
  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Point of Sale</h2>
      <p class="text-gray-600 dark:text-gray-400">Create new sales transactions</p>
    </div>
    <div class="flex items-center space-x-2">
      <button @click="clearAll()" class="btn-secondary">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
        Clear All
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Product Search & Selection -->
    <div class="xl:col-span-2 space-y-6">
      <!-- Search Section -->
      <div class="card">
        <div class="card-body">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Product Search</h3>
          <div x-data="{show:false}" @click.away="show=false" class="relative">
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
                  placeholder="Search products by name or barcode..." 
                  class="block w-full pl-10 pr-3 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
              </div>
              <button @click="search();show=true" class="btn-primary px-6">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </button>
            </div>
            
            <!-- Search Results -->
            <template x-if="show && results.length">
              <div class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg max-h-80 overflow-auto">
                <template x-for="p in results" :key="p.id">
                  <div @click="addToCart(p);show=false" class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-colors">
                    <div class="flex items-center justify-between">
                      <div class="flex-1">
                        <div class="font-medium text-gray-900 dark:text-white" x-text="p.name"></div>
                        <div class="text-sm text-gray-500 dark:text-gray-400" x-text="'SKU: ' + (p.sku || '-')"></div>
                      </div>
                      <div class="text-right">
                        <div class="font-semibold text-gray-900 dark:text-white" x-text="format(p.price)"></div>
                        <div class="text-xs text-green-600 dark:text-green-400">In Stock</div>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <p>No products found</p>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- Cart & Checkout -->
    <div class="space-y-6">
      <!-- Customer Selection -->
      <div class="card">
        <div class="card-body">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Customer</h3>
          <div x-data="{q:'',results:[],selected:null,show:false,showModal:false,newCustomer:{name:'',phone:'',address:''}}" @click.away="show=false">
            <div class="relative">
              <input 
                type="text" 
                class="form-input" 
                placeholder="Search customer..." 
                x-model="q" 
                @input.debounce.300ms="fetch('/api/customers?q='+encodeURIComponent(q)).then(r=>r.json()).then(data=>{results=data;show=true})"
                @focus="show=q.length>0">
              
              <template x-if="show && results.length">
                <div class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg max-h-40 overflow-auto">
                  <template x-for="item in results" :key="item.id">
                    <div @click="selected=item;show=false;q=item.name" class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                      <div class="font-medium text-gray-900 dark:text-white" x-text="item.name"></div>
                      <div class="text-sm text-gray-500 dark:text-gray-400" x-text="item.phone"></div>
                    </div>
                  </template>
                </div>
              </template>
              
              <template x-if="show && q && results.length === 0">
                <div class="absolute z-20 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg p-4">
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
              <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="font-medium text-blue-900 dark:text-blue-100" x-text="selected.name"></p>
                    <p class="text-sm text-blue-600 dark:text-blue-300" x-text="selected.phone"></p>
                  </div>
                  <button @click="selected=null;q=''" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </template>

            <!-- Modal for new customer -->
            <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
              <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add New Customer</h3>
                <div class="space-y-4">
                  <input type="text" class="form-input" placeholder="Customer Name" x-model="newCustomer.name">
                  <input type="text" class="form-input" placeholder="Phone Number" x-model="newCustomer.phone">
                  <textarea class="form-textarea" rows="3" placeholder="Address" x-model="newCustomer.address"></textarea>
                </div>
                <div class="flex gap-3 justify-end mt-6">
                  <button type="button" class="btn-secondary" @click="showModal=false">Cancel</button>
                  <button type="button" class="btn-primary" @click="
                    fetch('/customers', {
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
          <div class="flex items-center justify-between">
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
                    <div x-show="it.source_location_id" class="mt-1">
                      <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                        Source: <span x-text="(locations.find(l=>l.id==it.source_location_id)||{}).name||'Active Location'"></span>
                      </span>
                    </div>
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
                    <input type="number" step="0.01" class="w-full px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-model.number="it.discount">
                  </div>
                </div>
                
                <div class="mb-3">
                  <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Source Location</label>
                  <select class="w-full px-2 py-1 text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" x-model.number="it.source_location_id">
                    <option :value="null">Active Location</option>
                    <template x-for="loc in locations" :key="loc.id">
                      <option :value="loc.id" x-text="loc.name"></option>
                    </template>
                  </select>
                </div>
                
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-600">
                  <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                  <span class="font-semibold text-gray-900 dark:text-white" x-text="format(itemSubtotal(it))"></span>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- Payment & Checkout -->
      <div class="card" x-show="cart.length > 0" x-transition>
        <div class="card-header">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment & Checkout</h3>
        </div>
        <div class="card-body space-y-4">
          <!-- Total Summary -->
          <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
            <div class="flex justify-between items-center text-lg font-semibold">
              <span class="text-gray-900 dark:text-white">Total</span>
              <span class="text-gray-900 dark:text-white" x-text="format(total())"></span>
            </div>
          </div>
          
          <!-- Payment Methods -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Methods</label>
              <button @click="payments.push({type:'cash',amount:0,reference:''})" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">+ Add Payment</button>
            </div>
            
            <div class="space-y-3">
              <template x-for="(p, i) in payments" :key="i">
                <div class="flex items-center gap-2">
                  <select class="form-select flex-1" x-model="p.type">
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer</option>
                    <option value="card">Card</option>
                    <option value="qris">QRIS</option>
                  </select>
                  <input type="number" step="0.01" class="form-input flex-1" x-model.number="p.amount" placeholder="Amount">
                  <input type="text" class="form-input flex-1" x-model="p.reference" placeholder="Reference">
                  <button @click="payments.splice(i,1)" class="text-red-500 hover:text-red-700 p-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </template>
            </div>
          </div>
          
          <!-- Payment Summary -->
          <div class="space-y-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Amount Paid</span>
              <span class="font-medium text-gray-900 dark:text-white" x-text="format(payTotal())"></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 dark:text-gray-400">Change</span>
              <span class="font-medium text-gray-900 dark:text-white" x-text="format(Math.max(0, payTotal() - total()))"></span>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="grid grid-cols-2 gap-3 pt-4">
            <button @click="saveDraft()" class="btn-secondary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
              </svg>
              Save Draft
            </button>
            <button @click="checkout()" class="btn-primary">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
              Process Sale
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function pos() {
  return {
    keyword: '',
    results: [],
    cart: [],
    locations: [],
    payments: [],
    
    async search() {
      if (!this.keyword.trim()) {
        this.results = [];
        return;
      }
      
      try {
        const url = new URL('/api/products', window.location.origin);
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
          source_location_id: null 
        });
      }
      this.keyword = '';
      this.results = [];
    },
    
    itemSubtotal(it) { 
      return (Number(it.price) - Number(it.discount || 0)) * Number(it.qty || 0); 
    },
    
    total() { 
      return this.cart.reduce((a, c) => a + this.itemSubtotal(c), 0); 
    },
    
    payTotal() { 
      return this.payments.reduce((a,p) => a + Number(p.amount || 0), 0); 
    },
    
    format(v) { 
      return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR'}).format(v || 0); 
    },
    
    clearAll() {
      if (confirm('Are you sure you want to clear all items?')) {
        this.cart = [];
        this.payments = [];
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
          location_id: it.source_location_id || null 
        }));
        
        const check = await fetch('/api/stock/available-batch', { 
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
            if (Number(it.qty) > av) { 
              window.notify(`Insufficient stock for ${it.name}. Available: ${av}`, 'error');
              return; 
            }
          }
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
            source_location_id: it.source_location_id || null 
          })),
          payments: this.payments,
          status: status
        };
        
        const res = await fetch('/sales', { 
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
    
    async init() {
      try { 
        const r = await fetch('/api/locations'); 
        this.locations = r.ok ? await r.json() : []; 
      } catch (error) {
        console.error('Failed to load locations:', error);
      }
      
      // Initialize with one cash payment method
      this.payments.push({type: 'cash', amount: 0, reference: ''});
    }
  }
}
</script>
@endsection
