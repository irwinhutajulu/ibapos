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
            /* Allow truncation when name is too long for the thermal width */
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            display: block;
        }

        /* Name row wraps the product name and subtotal on one line */
        .name-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .item-name {
            flex: 1 1 auto;
            margin-right: 6px;
            min-width: 0; /* allow flex items to shrink so ellipsis works */
        }

        .item-subtotal {
            flex: 0 0 auto;
            margin-left: 6px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
        }

        /* Sub-lines under the product name (qty x price, discount) */
        .item-subline {
            display: flex;
            justify-content: space-between;
            padding-left: 8px;
            font-size: 9px;
            color: #000;
        }
        
        .payment-summary {
            margin: 3mm 0;
            font-size: 10px;
        }
        
        .payment-summary .row {
            display: grid;
            grid-template-columns: 80px 10px 1fr;
            margin-bottom: 1mm;
        }

        .payment-summary .row span:nth-child(3) {
            text-align: right;
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
        <table class="transaction-info">
            <tr><td>Tanggal</td><td>:</td><td id="trx-date">Loading...</td></tr>
            <tr><td>No. Inv</td><td>:</td><td id="trx-no">Loading...</td></tr>
            <tr><td>Kasir</td><td>:</td><td id="cashier">Loading...</td></tr>
            <tr><td>Pembeli</td><td>:</td><td id="buyer-name">Loading...</td></tr>
            </table>
        
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
                <span>Subtotal</span>
                <span>: </span>
                <span id="subtotal">Loading...</span>
            </div>
            <div class="row" id="discount-row" style="display: none;">
                <span>Diskon</span>
                <span>: </span>
                <span id="discount">Loading...</span>
            </div>
            <div class="row" id="fee-row" style="display: none;">
                <span>Biaya Lain</span>
                <span>: </span>
                <span id="additional-fee">Loading...</span>
            </div>
            <div class="row total-row">
                <span>TOTAL</span>
                <span>: </span>
                <span id="total">Loading...</span>
            </div>
            <div class="row">
                <span>Bayar</span>
                <span>: </span>
                <span id="payment">Loading...</span>
            </div>
            <div class="row">
                <span>Kembalian</span>
                <span>: </span>
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
            // Ensure we display whole rupiah without any decimal places
            const rounded = Math.round(Number(amount) || 0);
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(rounded);
        }

        // Escape HTML to avoid XSS when inserting product names
        function escapeHtml(unsafe) {
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Truncate text to a maximum length and add ellipsis.
        // Defaults to 32 characters which fits typical 58mm thermal widths.
        function truncateText(text, maxLen = 32) {
            if (!text) return '';
            const s = String(text);
            if (s.length <= maxLen) return s;
            return s.slice(0, maxLen - 1) + '…';
        }

        // If this view is used as a preview (server-side), the server will pass `previewData` into JS via inline JSON.
        const serverPreview = {!! json_encode(isset($preview) && $preview ? true : false) !!};
        const serverPreviewData = {!! json_encode($previewData ?? null) !!};

        // If not preview, listen for data from parent window (pos) and auto-print
        if (!serverPreview) {
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
        } else {
            // Populate immediately with preview data but do not auto-print
            if (serverPreviewData) {
                populateReceipt(serverPreviewData);
            }
        }

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
                    // Create a header row with name (left) and subtotal (right)
                    const nameRow = document.createElement('div');
                    nameRow.className = 'name-row';

                    const nameDiv = document.createElement('div');
                    nameDiv.className = 'item-name';
                    const fullName = product.name || 'Unknown Product';
                    // set truncated text for display, but keep full name in title for hover/preview
                    nameDiv.textContent = truncateText(fullName, 32);
                    nameDiv.title = fullName;

                    const subtotalDiv = document.createElement('div');
                    subtotalDiv.className = 'item-subtotal';
                    subtotalDiv.textContent = formatRupiah(product.subtotal || 0);

                    nameRow.appendChild(nameDiv);
                    nameRow.appendChild(subtotalDiv);

                    // Quantity row (indented): "{qty} x Rp {price}"
                    const qtyRow = document.createElement('div');
                    qtyRow.className = 'item-subline';
                    const qtyLabel = document.createElement('span');
                    qtyLabel.textContent = `${product.qty || 1} x ${formatRupiah(product.price || 0)}`;
                    const qtyEmpty = document.createElement('span');
                    qtyEmpty.textContent = '';
                    qtyRow.appendChild(qtyLabel);
                    qtyRow.appendChild(qtyEmpty);

                    itemDiv.appendChild(nameRow);
                    itemDiv.appendChild(qtyRow);

                    // Per-item discount row: show "Disc. {qty} x {perUnitDiscount}" on left and the total discount (qty * discount) on the right
                    const perUnitDiscount = Number(product.discount || 0);
                    const qty = Number(product.qty || 1);
                    const totalDiscount = perUnitDiscount * qty;
                    if (perUnitDiscount > 0) {
                        const discRow = document.createElement('div');
                        discRow.className = 'item-subline';
                        const discLabel = document.createElement('span');
                        discLabel.textContent = `Disc. (${qty} x ${formatRupiah(perUnitDiscount)})`;
                        const discValue = document.createElement('span');
                        discRow.appendChild(discLabel);
                        discRow.appendChild(discValue);
                        itemDiv.appendChild(discRow);
                    }
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
                // Use 'grid' to match .payment-summary .row layout so alignment stays consistent
                discountRow.style.display = 'grid';
                document.getElementById('discount').textContent = formatRupiah(discount);
            } else {
                discountRow.style.display = 'none';
            }
            
            // Handle additional fee row
            const feeRow = document.getElementById('fee-row');
            if (additionalFee > 0) {
                // Use 'grid' to match .payment-summary .row layout so alignment stays consistent
                feeRow.style.display = 'grid';
                document.getElementById('additional-fee').textContent = formatRupiah(additionalFee);
            } else {
                feeRow.style.display = 'none';
            }
        }

        // Auto-close window after printing (only when not preview)
        if (!serverPreview) {
            window.addEventListener('afterprint', function() {
                setTimeout(() => {
                    window.close();
                }, 1000);
            });
        }
        
        // Preview mode: show simple controls to print or close
        if (serverPreview) {
            const controls = document.createElement('div');
            controls.style.position = 'fixed';
            controls.style.top = '8px';
            controls.style.right = '8px';
            controls.style.zIndex = 9999;
            controls.innerHTML = `
                <button id="preview-print" style="margin-right:8px;padding:6px 10px;background:#1f8ef1;border:none;color:#fff;border-radius:6px;">Print</button>
                <button id="preview-close" style="padding:6px 10px;background:#6b7280;border:none;color:#fff;border-radius:6px;">Close</button>
            `;
            document.body.appendChild(controls);
            document.getElementById('preview-print').addEventListener('click', function() {
                window.print();
            });
            document.getElementById('preview-close').addEventListener('click', function() {
                window.close();
            });
        }
    </script>
</body>
</html>