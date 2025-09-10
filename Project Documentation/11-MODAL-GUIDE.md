# Confirmation Modal Template - Usage Guide

## Overview
Template modal konfirmasi floating yang reusable untuk semua operasi yang membutuhkan konfirmasi user seperti delete, update, approve, dll. Modal menggunakan floating design yang muncul di center tanpa backdrop overlay, sehingga content di bawahnya tetap terlihat.

## Component Location
`resources/views/components/confirmation-modal.blade.php`

## Design Features
- **Floating Style**: Modal muncul floating di center tanpa menutup content background
- **Middle Center Positioning**: Positioned absolute di tengah layar
- **No Backdrop Overlay**: Content di bawah tetap terlihat dan accessible
- **Enhanced Shadow**: Box shadow yang lebih prominent untuk floating effect
- **Close X Button**: Tombol X di pojok kanan atas untuk kemudahan close
- **Smooth Animations**: Slide dan scale transitions untuk UX yang smooth

## Basic Usage

### 1. Include Modal in View
```blade
<x-confirmation-modal 
    id="unique-modal-id"
    title="Confirm Action"
    message="Are you sure you want to proceed?"
    confirm-text="Confirm"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>
```

### 2. JavaScript Function Call
```javascript
// Open modal with action
openConfirmationModal('unique-modal-id', function() {
    // Your action code here
    console.log('User confirmed action');
});

// With dynamic options
openConfirmationModal('unique-modal-id', function() {
    // Action code
}, {
    title: 'Custom Title',
    message: 'Custom message'
});
```

## Component Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `id` | string | 'confirmation-modal' | Unique modal ID |
| `title` | string | 'Confirm Action' | Modal title |
| `message` | string | 'Are you sure you want to proceed?' | Confirmation message |
| `confirmText` | string | 'Confirm' | Confirm button text |
| `cancelText` | string | 'Cancel' | Cancel button text |
| `confirmClass` | string | 'btn-danger' | CSS class for confirm button |
| `icon` | string | 'warning' | Icon type: warning, danger, info, success |

## CSS Classes Available

### Button Classes
- `btn-danger` - Red delete/danger button
- `btn-primary` - Blue primary button  
- `btn-warning` - Yellow warning button
- `btn-success` - Green success button

### Icon Types
- `warning` - Yellow warning triangle
- `danger` - Red trash/delete icon
- `info` - Blue info circle
- `success` - Green checkmark

## Usage Examples

### 1. Delete Product (Products Module)
```blade
<!-- In view -->
<x-confirmation-modal 
    id="delete-confirmation-modal"
    title="Delete Product"
    message="Are you sure you want to delete this product? It can be restored later."
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>

<!-- JavaScript -->
<script>
function deleteProduct(id) {
    openConfirmationModal('delete-confirmation-modal', function() {
        // Submit delete form
        document.getElementById(`delete-form-${id}`).submit();
    });
}
</script>
```

### 2. Force Delete (Permanent)
```blade
<x-confirmation-modal 
    id="force-delete-modal"
    title="Permanently Delete"
    message="This action cannot be undone. Are you sure?"
    confirm-text="Delete Forever"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="danger"
/>
```

### 3. Approve Action
```blade
<x-confirmation-modal 
    id="approve-modal"
    title="Approve Request"
    message="Do you want to approve this request?"
    confirm-text="Approve"
    cancel-text="Cancel"
    confirm-class="btn-success"
    icon="success"
/>
```

### 4. General Warning
```blade
<x-confirmation-modal 
    id="warning-modal"
    title="Important Notice"
    message="This will affect multiple records. Continue?"
    confirm-text="Continue"
    cancel-text="Cancel"
    confirm-class="btn-warning"
    icon="info"
/>
```

## Implementation in Other Modules

### Categories Module
```blade
<!-- categories/index.blade.php -->
<x-confirmation-modal 
    id="delete-category-modal"
    title="Delete Category"
    message="Are you sure you want to delete this category?"
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>

<script>
function deleteCategory(id) {
    openConfirmationModal('delete-category-modal', function() {
        document.getElementById(`delete-form-${id}`).submit();
    });
}
</script>
```

### Customers Module
```blade
<!-- customers/index.blade.php -->
<x-confirmation-modal 
    id="delete-customer-modal"
    title="Delete Customer"
    message="Are you sure you want to delete this customer?"
    confirm-text="Delete"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>
```

### Sales Module
```blade
<!-- sales/index.blade.php -->
<x-confirmation-modal 
    id="void-sale-modal"
    title="Void Sale"
    message="Are you sure you want to void this sale transaction?"
    confirm-text="Void Sale"
    cancel-text="Cancel"
    confirm-class="btn-danger"
    icon="warning"
/>
```

## Advanced Usage

### Dynamic Content Update
```javascript
// Update modal content dynamically
function confirmWithCustomMessage(action, title, message) {
    openConfirmationModal('dynamic-modal', action, {
        title: title,
        message: message
    });
}

// Usage
confirmWithCustomMessage(
    function() { /* delete action */ },
    'Delete Multiple Items',
    'You have selected 5 items. Delete all?'
);
```

### Multiple Confirmation Modals
```blade
<!-- Multiple modals in same view -->
<x-confirmation-modal id="delete-modal" title="Delete" icon="warning" />
<x-confirmation-modal id="approve-modal" title="Approve" icon="success" />
<x-confirmation-modal id="reject-modal" title="Reject" icon="danger" />
```

## Best Practices

### 1. Use Descriptive IDs
```blade
<!-- Good -->
<x-confirmation-modal id="delete-product-modal" />
<x-confirmation-modal id="approve-order-modal" />

<!-- Avoid -->
<x-confirmation-modal id="modal1" />
<x-confirmation-modal id="modal2" />
```

### 2. Clear Messages
```blade
<!-- Good -->
message="Are you sure you want to delete this product? It can be restored later."

<!-- Avoid -->
message="Delete?"
```

### 3. Appropriate Icons
```blade
<!-- Delete actions -->
icon="warning" or icon="danger"

<!-- Approve actions -->
icon="success"

<!-- Info/Warning -->
icon="info" or icon="warning"
```

### 4. Consistent Button Classes
```blade
<!-- Delete/Dangerous actions -->
confirm-class="btn-danger"

<!-- Approve/Success actions -->
confirm-class="btn-success"

<!-- Primary actions -->
confirm-class="btn-primary"
```

## Global Functions

### Available Functions
- `openConfirmationModal(modalId, action, options)` - Open modal with action
- `closeConfirmationModal(modalId)` - Close specific modal

### Function Parameters
```javascript
openConfirmationModal(
    'modal-id',           // Modal ID
    function() { },       // Action to execute on confirm
    {                     // Optional dynamic options
        title: 'Title',
        message: 'Message'
    }
);
```

## Styling

### Floating Modal Features
- **Positioning**: `fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2`
- **Z-Index**: `z-50` untuk overlay di atas content lain
- **Shadow**: Enhanced box-shadow untuk floating effect
- **Width**: Fixed width 384px (w-96) untuk konsistensi
- **Background**: Dark theme dengan `bg-gray-800`
- **No Backdrop**: Content di bawah tetap visible dan accessible

### Animation Effects
- **Enter**: Slide dari bawah dengan scale effect
- **Leave**: Slide ke bawah dengan fade out
- **Duration**: 300ms enter, 200ms leave
- **Smooth Transitions**: All buttons dan hover states

### Dark Theme Support
Modal secara otomatis adapt dengan dark theme menggunakan:
- Dark backgrounds (`bg-gray-800`)
- White text (`text-white`)
- Proper borders (`border-gray-600`)
- Consistent button styling

### Responsive Design
- Mobile-friendly dengan max-width constraints
- Touch-friendly button sizes
- Proper spacing untuk semua device sizes
- Center positioning konsisten di semua screen sizes

### Close Options
1. **Cancel Button**: Primary method untuk cancel action
2. **X Button**: Secondary close di pojok kanan atas  
3. **Escape Key**: Keyboard shortcut untuk close modal
4. **No Click Outside**: Background click tidak close modal (floating design)

## Troubleshooting

### Modal Not Opening
1. Check Alpine.js is loaded
2. Verify modal ID is unique
3. Ensure `openConfirmationModal` function exists

### Action Not Executing
1. Check function is properly defined in action parameter
2. Verify no JavaScript errors in console
3. Test action function independently

### Styling Issues
1. Rebuild assets: `npm run build`
2. Check CSS classes are defined
3. Verify dark theme classes

## Future Enhancements

### Planned Features
- Custom modal sizes
- Animation options
- Multiple button support
- Form integration
- Auto-close timers
