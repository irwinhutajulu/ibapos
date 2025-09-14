<!-- Payment & Checkout -->
<div class="card" x-show="cart.length > 0" x-transition x-data="{
  getCart() {
    // Ambil cart dari window atau parent scope jika tidak ada di data
    return this.cart ?? (typeof cart !== 'undefined' ? cart : []);
  },
  subtotal() {
  return this.getCart().reduce((acc, item) => acc + ((Number(item.price) - Number(item.discount || 0)) * Number(item.qty || 0)), 0);
  },
  total() {
    let fee = parseFloat(this.additional_fee) || 0;
    let disc = parseFloat(this.discount) || 0;
    return Math.max(0, this.subtotal() + fee - disc);
  },
  payTotal() {
    return this.payments.reduce((acc, p) => acc + (parseFloat(p.amount) || 0), 0);
  },
  printStruk() {
    // Open receipt window
    const receiptWindow = window.open('/pos/print-receipt', '_blank', 'width=400,height=600');
    
    // Prepare receipt data
    const receiptData = {
      store: {
        name: document.getElementById('store-name') ? document.getElementById('store-name').textContent : 'NAMA TOKO',
        address: document.getElementById('store-address') ? document.getElementById('store-address').textContent : 'Alamat Toko',
        phone: document.getElementById('store-phone') ? document.getElementById('store-phone').textContent : 'Telp: 08xxxxxxxxxx',
      },
      trx: {
        date: new Date().toLocaleString('id-ID'),
        no: this.trxNo || ('TRX' + Date.now()),
        buyer: this.buyerName || '-',
      },
      products: this.getCart().map(item => ({
        name: item.name,
        qty: item.qty,
        price: item.price,
        subtotal: (Number(item.price) - Number(item.discount || 0)) * Number(item.qty || 0)
      })),
      total: this.total(),
      payment: this.payTotal(),
      change: (this.payTotal() - this.total()),
      additional_fee: this.additional_fee,
      note: 'Terima kasih atas pembelian Anda!'
    };
    
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
  }
}">
  <div class="card-header">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment & Checkout</h3>
  </div>
  <div class="card-body space-y-4">
    <!-- Total Summary -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-2 space-y-2">
      <div class="flex justify-between items-center text-base">
        <span class="text-gray-600 dark:text-gray-300">Subtotal</span>
  <span class="text-gray-900 dark:text-white" x-text="format(subtotal())"></span>
      </div>
      <div class="flex justify-between items-center text-base">
        <span class="text-gray-600 dark:text-gray-300">Additional Fee</span>
        <input type="text" inputmode="numeric" class="form-input text-right"
          :value="additional_fee === null ? '' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(additional_fee)"
          @input="
            let raw = $event.target.value.replace(/\D/g, '');
            if(raw === '') { additional_fee = null; return; }
            let padded = raw.padStart(3, '0');
            let rupiah = parseFloat(padded.slice(0, -2) + '.' + padded.slice(-2));
            additional_fee = isNaN(rupiah) ? 0 : rupiah;
            $event.target.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(additional_fee);
          "
          placeholder="Masukkan nominal">
      </div>
      <div class="flex justify-between items-center text-base">
        <span class="text-gray-600 dark:text-gray-300">Discount</span>
        <input type="text" inputmode="numeric" class="form-input text-right"
          :value="discount === null ? '' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(discount)"
          @input="
            let raw = $event.target.value.replace(/\D/g, '');
            if(raw === '') { discount = null; return; }
            let padded = raw.padStart(3, '0');
            let rupiah = parseFloat(padded.slice(0, -2) + '.' + padded.slice(-2));
            discount = isNaN(rupiah) ? 0 : rupiah;
            $event.target.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(discount);
          "
          placeholder="Masukkan nominal">
      </div>
      <div class="flex justify-between items-center text-lg font-semibold pt-2">
        <span class="text-gray-900 dark:text-white">Total</span>
        <span class="text-gray-900 dark:text-white" x-text="format(total())"></span>
      </div>
    </div>
    
   
    <!-- Payment Methods -->
    <div>
      <div class="flex items-center justify-between mb-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Payment Methods</label>
        <button @click="payments.push({type:'cash',amount:null,reference:null})" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">+ Add Payment</button>
      </div>
      
      <div class="space-y-3 bg-gray-50 dark:bg-gray-700 rounded-xl p-2">
        <template x-for="(p, i) in payments" :key="i">
          <div class= "space-y-2">
          <div class="flex items-center gap-2">
          <select class="form-select flex-1 bg-gray-50 dark:bg-gray-700 text-white" x-model="p.type">
              <option value="cash">Cash</option>
              <option value="transfer">Transfer</option>
              <option value="card">Card</option>
              <option value="qris">QRIS</option>
            </select>
            <input type="text" inputmode="numeric" class="form-input flex-1 text-right"
              :value="p.amount === null || p.amount === undefined ? '' : new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(p.amount)"
              @input="
                let raw = $event.target.value.replace(/\D/g, '');
                if(raw === '') { p.amount = null; return; }
                let padded = raw.padStart(3, '0');
                let rupiah = parseFloat(padded.slice(0, -2) + '.' + padded.slice(-2));
                p.amount = isNaN(rupiah) ? 0 : rupiah;
                $event.target.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(p.amount);
              "
              placeholder="Masukkan nominal">
            
            </div>

            <div class="flex items-center gap-2">
            <input type="text" class="form-input flex-1" x-model="p.reference" placeholder="Masukan No Referensi">
            <button @click="payments.splice(i,1)" class="text-red-500 hover:text-red-700 p-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
            </div>
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
    
    <!-- Print Receipt Button (appears after successful transaction) -->
    <div class="pt-4" x-show="saleProcessed">
      <div class="space-y-3">
        <button @click="printStruk()" class="btn-success w-full">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
          </svg>
          Print Struk
        </button>
        
        <div class="grid grid-cols-2 gap-3">
          <button @click="saleProcessed = false; showCheckoutModal = false;" class="btn-secondary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Close
          </button>
          <button @click="saleProcessed = false; additional_fee = null; discount = null;" class="btn-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Sale
          </button>
        </div>
      </div>
    </div>
  </div>
</div>