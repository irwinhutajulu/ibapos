<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Print</title>
    <style>
        /* Thermal Printer Specific Styles */
        @media print {
            @page {
                size: 58mm auto; /* 58mm width, auto height */
                margin: 0;
            }
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        
        /* General Receipt Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            width: 58mm;
            margin: 0 auto;
            padding: 2mm;
        }
        
        .receipt {
            width: 100%;
            max-width: 54mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 4mm;
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
        }
        
        .store-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        
        .store-info {
            font-size: 10px;
            line-height: 1.1;
        }
        
        .transaction-info {
            margin: 3mm 0;
            font-size: 10px;
        }
        
        .transaction-info .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        
        .items-section {
            margin: 3mm 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 2mm 0;
        }
        
        .item {
            margin-bottom: 2mm;
            font-size: 10px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 0.5mm;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }
        
        .payment-summary {
            margin: 3mm 0;
            font-size: 10px;
        }
        
        .payment-summary .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 1mm;
            margin-top: 2mm;
        }
        
        .footer {
            text-align: center;
            margin-top: 4mm;
            font-size: 9px;
            border-top: 1px dashed #000;
            padding-top: 2mm;
        }
        
        .thank-you {
            font-weight: bold;
            margin-bottom: 2mm;
        }
        
        /* Hide from screen, show in print */
        .print-only {
            display: none;
        }
        
        @media print {
            .print-only {
                display: block;
            }
            .no-print {
                display: none !important;
            }
        }
        
        /* For 80mm thermal printers */
        @media print and (min-width: 80mm) {
            @page {
                size: 80mm auto;
            }
            body {
                width: 80mm;
                font-size: 14px;
            }
            .receipt {
                max-width: 76mm;
            }
            .store-name {
                font-size: 16px;
            }
            .item {
                font-size: 12px;
            }
            .transaction-info,
            .payment-summary {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header - Store Information -->
        <div class="header">
            <div class="store-name" id="store-name">Loading...</div>
            <div class="store-info">
                <div id="store-address">Loading...</div>
                <div id="store-phone">Loading...</div>
            </div>
        </div>
        
        <!-- Transaction Information -->
        <div class="transaction-info">
            <div class="row">
                <span>Tanggal:</span>
                <span id="trx-date">Loading...</span>
            </div>
            <div class="row">
                <span>No. Transaksi:</span>
                <span id="trx-no">Loading...</span>
            </div>
            <div class="row">
                <span>Kasir:</span>
                <span id="cashier">Loading...</span>
            </div>
            <div class="row">
                <span>Pembeli:</span>
                <span id="buyer-name">Loading...</span>
            </div>
        </div>
        
        <!-- Items Section -->
        <div class="items-section">
            <div id="items-list">
                <!-- Items will be populated by JavaScript -->
                <div class="item">
                    <div class="item-name">Loading items...</div>
                    <div class="item-details">
                        <span>Please wait...</span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="row">
                <span>Subtotal:</span>
                <span id="subtotal">Loading...</span>
            </div>
            <div class="row" id="discount-row" style="display: none;">
                <span>Diskon:</span>
                <span id="discount">Rp 0</span>
            </div>
            <div class="row" id="fee-row" style="display: none;">
                <span>Biaya Tambahan:</span>
                <span id="additional-fee">Rp 0</span>
            </div>
            <div class="row total-row">
                <span>TOTAL:</span>
                <span id="total">Loading...</span>
            </div>
            <div class="row">
                <span>Bayar:</span>
                <span id="payment">Loading...</span>
            </div>
            <div class="row">
                <span>Kembalian:</span>
                <span id="change">Loading...</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">Terima kasih atas pembelian Anda!</div>
            <div>Barang yang sudah dibeli tidak dapat ditukar</div>
        </div>
    </div>

    <script>
        // Format currency for Indonesian Rupiah
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount || 0);
        }

        // Listen for data from parent window
        window.addEventListener('message', function(event) {
            if (event.data.type === 'RECEIPT_DATA') {
                const data = event.data.data;
                populateReceipt(data);
                
                // Auto print after data is loaded
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });

        function populateReceipt(data) {
            console.log('Populating receipt with data:', data);
            
            // Store Information
            if (data.store) {
                if (data.store.name) document.getElementById('store-name').textContent = data.store.name;
                if (data.store.address) document.getElementById('store-address').textContent = data.store.address;
                if (data.store.phone) document.getElementById('store-phone').textContent = data.store.phone;
            }

            // Transaction Information  
            if (data.trx) {
                if (data.trx.date) document.getElementById('trx-date').textContent = data.trx.date;
                if (data.trx.no) document.getElementById('trx-no').textContent = data.trx.no;
                if (data.trx.buyer) document.getElementById('buyer-name').textContent = data.trx.buyer;
            }
            
            // Add cashier info (get from auth or default)
            const cashierName = '{{ auth()->user()->name ?? "Admin" }}';
            document.getElementById('cashier').textContent = cashierName;

            // Items List
            if (data.products && data.products.length > 0) {
                const itemsList = document.getElementById('items-list');
                itemsList.innerHTML = '';
                
                data.products.forEach(product => {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'item';
                    itemDiv.innerHTML = `
                        <div class="item-name">${product.name || 'Unknown Product'}</div>
                        <div class="item-details">
                            <span>${product.qty || 1} x ${formatRupiah(product.price || 0)}</span>
                            <span>${formatRupiah(product.subtotal || 0)}</span>
                        </div>
                    `;
                    itemsList.appendChild(itemDiv);
                });
            } else {
                // Show message when no items
                const itemsList = document.getElementById('items-list');
                itemsList.innerHTML = '<div class="item"><div class="item-name">No items found</div></div>';
            }

            // Payment Summary
            const subtotal = data.subtotal || 0;
            const additionalFee = data.additional_fee || 0;
            const discount = data.discount || 0;
            const total = data.total || 0;
            const payment = data.payment || 0;
            const change = data.change || 0;
            
            document.getElementById('subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('total').textContent = formatRupiah(total);
            document.getElementById('payment').textContent = formatRupiah(payment);
            document.getElementById('change').textContent = formatRupiah(change);
            
            // Handle discount row
            const discountRow = document.getElementById('discount-row');
            if (discount > 0) {
                discountRow.style.display = 'flex';
                document.getElementById('discount').textContent = formatRupiah(discount);
            } else {
                discountRow.style.display = 'none';
            }
            
            // Handle additional fee row
            const feeRow = document.getElementById('fee-row');
            if (additionalFee > 0) {
                feeRow.style.display = 'flex';
                document.getElementById('additional-fee').textContent = formatRupiah(additionalFee);
            } else {
                feeRow.style.display = 'none';
            }
        }

        // Auto-close window after printing
        window.addEventListener('afterprint', function() {
            setTimeout(() => {
                window.close();
            }, 1000);
        });
    </script>
</body>
</html>