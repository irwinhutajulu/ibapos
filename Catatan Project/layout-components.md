# Layout Components Documentation

Dokumentasi ini menjelaskan cara menggunakan komponen layout yang telah dibuat untuk menjaga konsistensi desain di seluruh aplikasi.

## Komponen Yang Tersedia

### 1. Page Header Component (`x-page-header`)

Komponen ini digunakan untuk header halaman yang konsisten dengan breadcrumbs, title, dan actions.

#### Props:
- `title` (string): Judul halaman
- `subtitle` (string, optional): Subtitle halaman  
- `icon` (string, optional): HTML SVG icon
- `breadcrumbs` (array, optional): Array breadcrumb items
- `actions` (slot, optional): Tombol atau actions di header
- `stats` (array, optional): Statistik yang ditampilkan di bawah header

#### Contoh Penggunaan:

```blade
<x-page-header 
    title="Products" 
    subtitle="Manage your product inventory"
    :icon="'<svg class=\"w-6 h-6 text-white\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">
        <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4\"></path>
    </svg>'"
    :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => '<svg>...</svg>'],
        ['label' => 'Products']
    ]"
>
    <x-slot name="actions">
        <button onclick="openCreateModal()" class="btn-primary">
            Add New Item
        </button>
    </x-slot>
</x-page-header>
```

### 2. Data Table Component (`x-data-table`)

Komponen responsif untuk menampilkan data dalam format table (desktop) dan card (mobile).

#### Props:
- `headers` (array): Header kolom table
- `rows` (array): Data rows dengan format khusus
- `pagination` (object, optional): Object pagination Laravel
- `emptyTitle` (string): Judul ketika data kosong
- `emptyDescription` (string): Deskripsi ketika data kosong
- `emptyIcon` (string, optional): Icon ketika data kosong
- `emptyAction` (string, optional): HTML button untuk action ketika kosong
- `actions` (boolean, default true): Menampilkan kolom actions

#### Format Data Rows:

```php
$tableData = [
    [
        'cells' => [
            // Avatar cell
            [
                'type' => 'avatar',
                'image' => 'path/to/image.jpg', // optional
                'name' => 'Product Name',
                'subtitle' => 'SKU: ABC123',
                'bgColor' => 'bg-gradient-to-r from-blue-400 to-blue-600',
                'icon' => '<svg>...</svg>' // fallback icon
            ],
            // Currency cell
            [
                'type' => 'currency',
                'value' => 100000,
                'formatted' => 'Rp 100.000'
            ],
            // Badge cell
            [
                'type' => 'badge',
                'text' => 'Active',
                'class' => 'bg-green-100 text-green-800'
            ],
            // Simple text
            'Simple text value'
        ],
        'actions' => [
            [
                'type' => 'button',
                'onclick' => 'editItem(1)',
                'color' => 'blue',
                'label' => 'Edit',
                'icon' => '<svg>...</svg>'
            ],
            [
                'type' => 'link',
                'url' => '/items/1',
                'color' => 'green',
                'label' => 'View',
                'icon' => '<svg>...</svg>'
            ]
        ]
    ]
];
```

#### Contoh Penggunaan:

```blade
<x-data-table 
    :headers="['Product', 'SKU', 'Category', 'Price', 'Stock', 'Status']"
    :rows="$tableData"
    :pagination="$products"
    empty-title="No products found"
    empty-description="Start by adding your first product."
    :empty-action="'<button onclick=\"openCreateModal()\" class=\"btn-primary\">Add First Product</button>'"
/>
```

### 3. Modal Component (`x-modal`)

Komponen modal yang konsisten dengan Alpine.js.

#### Props:
- `id` (string): ID unik modal
- `title` (string): Judul modal
- `size` (string): Ukuran modal (sm, md, lg, xl, 2xl)
- `closable` (boolean, default true): Bisa ditutup dengan ESC/click outside
- `footer` (slot, optional): Footer modal dengan buttons

#### Contoh Penggunaan:

```blade
<x-modal id="createModal" title="Add New Item" size="lg">
    <form id="createForm">
        <!-- Form content -->
    </form>

    <x-slot name="footer">
        <button type="button" onclick="closeModal('createModal')" class="btn-secondary mr-3">
            Cancel
        </button>
        <button type="submit" form="createForm" class="btn-primary">
            Create Item
        </button>
    </x-slot>
</x-modal>
```

## Helper Functions

### Modal Functions
```javascript
// Buka modal
openModal('modalId');

// Tutup modal  
closeModal('modalId');
```

### Toast Notifications
```javascript
// Tampilkan notifikasi
window.notify('success', 'Message here');
window.notify('error', 'Error message');
window.notify('warning', 'Warning message');
window.notify('info', 'Info message');
```

## Controller Implementation

### Menyiapkan Data untuk Data Table

Di controller, buat method untuk memformat data:

```php
/**
 * Prepare table data for data-table component
 */
private function prepareTableData($items)
{
    $tableData = [];
    
    foreach ($items as $item) {
        $tableData[] = [
            'cells' => [
                // Avatar cell
                [
                    'type' => 'avatar',
                    'image' => $item->image_path ? asset('storage/' . $item->image_path) : null,
                    'name' => $item->name,
                    'subtitle' => $item->sku ? "SKU: {$item->sku}" : 'No SKU',
                    'bgColor' => 'bg-gradient-to-r from-blue-400 to-blue-600',
                    'icon' => '<svg>...</svg>'
                ],
                // Other cells...
                $item->sku ?: '-',
                $item->category->name ?? '-',
                [
                    'type' => 'currency',
                    'value' => $item->price ?? 0,
                    'formatted' => 'Rp ' . number_format($item->price ?? 0, 0, ',', '.')
                ],
                [
                    'type' => 'badge',
                    'text' => $item->is_active ? 'Active' : 'Inactive',
                    'class' => $item->is_active 
                        ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' 
                        : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                ]
            ],
            'actions' => $this->getItemActions($item)
        ];
    }
    
    return $tableData;
}

/**
 * Get actions for an item
 */
private function getItemActions($item)
{
    return [
        [
            'type' => 'button',
            'onclick' => "editItem({$item->id})",
            'color' => 'green',
            'label' => 'Edit',
            'icon' => '<svg>...</svg>'
        ],
        [
            'type' => 'button',
            'onclick' => "deleteItem({$item->id}, '" . addslashes($item->name) . "')",
            'color' => 'red',
            'label' => 'Delete',
            'icon' => '<svg>...</svg>'
        ]
    ];
}
```

## CSS Classes yang Tersedia

### Button Classes
- `btn-primary`: Button utama (biru)
- `btn-secondary`: Button sekunder (abu)
- `btn-success`: Button sukses (hijau)
- `btn-danger`: Button bahaya (merah)
- `btn-warning`: Button peringatan (kuning)

### Form Classes
- `form-input`: Input field standard
- `form-select`: Select dropdown standard
- `form-textarea`: Textarea standard
- `form-checkbox`: Checkbox standard

### Card Classes
- `card`: Container kartu standard
- `card-body`: Body kartu dengan padding
- `card-footer`: Footer kartu

## Best Practices

1. **Konsistensi Data**: Selalu gunakan format data yang sama untuk data-table component
2. **Responsive Design**: Komponen sudah responsive, tidak perlu CSS tambahan
3. **Icon Consistency**: Gunakan SVG icons dengan ukuran yang konsisten (w-4 h-4 untuk actions, w-6 h-6 untuk headers)
4. **Color Scheme**: Ikuti color scheme yang sudah ditentukan (blue untuk view, green untuk edit, red untuk delete)
5. **Loading States**: Selalu sediakan loading states untuk operasi async
6. **Error Handling**: Implementasikan error handling yang konsisten dengan toast notifications

## Migration Guide

Untuk mengubah halaman existing ke layout baru:

1. **Replace page header** dengan `<x-page-header>`
2. **Update controller** dengan method `prepareTableData()`
3. **Replace table HTML** dengan `<x-data-table>`
4. **Update modal** dengan `<x-modal>` component
5. **Test responsive** design di mobile dan desktop

Contoh file sudah dibuat:
- `resources/views/products/index-with-layout.blade.php`
- `resources/views/categories/index-with-layout.blade.php`

Anda dapat menggunakan file ini sebagai referensi untuk halaman lainnya.
