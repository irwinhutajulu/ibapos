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