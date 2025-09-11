<?php

return [
    // Daftar channel notifikasi yang didukung
    'channels' => [
        'email',
        'webpush',
        'inapp',
    ],

    // Daftar tipe notifikasi yang didukung
    'types' => [
        'sale_created'      => 'Penjualan Baru',
        'purchase_created'  => 'Pembelian Baru',
        'low_stock'         => 'Stok Menipis',
        'stock_out'         => 'Stok Habis',
        'kasbon_created'    => 'Kasbon Baru',
        'kasbon_approved'   => 'Kasbon Disetujui',
        'kasbon_rejected'   => 'Kasbon Ditolak',
        'mutation_in'       => 'Mutasi Masuk',
        'mutation_out'      => 'Mutasi Keluar',
        'delivery_status'   => 'Status Pengiriman',
    ],
];
