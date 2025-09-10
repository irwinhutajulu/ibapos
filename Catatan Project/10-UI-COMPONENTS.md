# Dropdown Components Documentation

## Overview
This application provides a comprehensive dropdown component system that can be used throughout the application for consistent UI patterns.

## Components

### 1. Base Dropdown Component (`<x-dropdown>`)
The main dropdown component that provides all functionality.

#### Props:
- `align` - Alignment of dropdown ('left', 'right', 'top') - default: 'right'
- `width` - Width of dropdown ('48', '56', '64', '72', '80', '96') - default: '48'
- `contentClasses` - Custom CSS classes for dropdown content
- `trigger` - Custom trigger HTML (optional)
- `triggerClass` - CSS classes for default trigger button
- `dropdownClass` - CSS classes for dropdown container
- `items` - Array of items to display programmatically
- `header` - Header content for dropdown

#### Basic Usage:
```blade
<x-dropdown>
    <x-slot name="trigger">
        <button class="btn-primary">Click Me</button>
    </x-slot>
    
    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
    <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
</x-dropdown>
```

#### Programmatic Items:
```blade
@php
$dropdownItems = [
    ['type' => 'header', 'label' => 'Account'],
    ['type' => 'link', 'url' => '/profile', 'label' => 'Profile', 'icon' => '<svg>...</svg>'],
    ['type' => 'link', 'url' => '/settings', 'label' => 'Settings', 'icon' => '<svg>...</svg>'],
    ['type' => 'divider'],
    ['type' => 'button', 'label' => 'Logout', 'style' => 'danger', 'onclick' => 'logout()'],
];
@endphp

<x-dropdown :items="$dropdownItems" />
```

### 2. Actions Dropdown (`<x-dropdown-actions>`)
A specialized dropdown for action menus with three-dot trigger.

#### Usage:
```blade
<x-dropdown-actions>
    <a href="/edit/1" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a>
    <button onclick="deleteItem(1)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Delete</button>
</x-dropdown-actions>
```

### 3. User Dropdown (`<x-dropdown-user>`)
A specialized dropdown for user account menu.

#### Usage:
```blade
<x-dropdown-user />
<!-- or with custom user -->
<x-dropdown-user :user="$customUser" />
```

## Item Types for Programmatic Items

### Header
```php
['type' => 'header', 'label' => 'Section Title']
```

### Divider
```php
['type' => 'divider']
```

### Link
```php
[
    'type' => 'link',
    'url' => '/profile',
    'label' => 'Profile',
    'icon' => '<svg>...</svg>', // optional
    'style' => 'danger|warning|success', // optional
    'target' => '_blank', // optional
    'badge' => 'New', // optional
    'shortcut' => 'Ctrl+P', // optional
    'class' => 'custom-class' // optional
]
```

### Button
```php
[
    'type' => 'button',
    'label' => 'Delete',
    'onclick' => 'deleteItem()',
    'style' => 'danger|warning|success', // optional
    'icon' => '<svg>...</svg>', // optional
    'buttonType' => 'button|submit', // optional
    'form' => 'form-id', // optional
    'confirm' => 'Are you sure?', // optional
    'badge' => '5', // optional
    'shortcut' => 'Del', // optional
    'class' => 'custom-class' // optional
]
```

### Custom Content
```php
[
    'type' => 'custom',
    'content' => '<div class="p-4">Custom HTML here</div>'
]
```

## Advanced Examples

### Complex Dropdown with Multiple Sections:
```blade
@php
$complexItems = [
    ['type' => 'header', 'label' => 'Quick Actions'],
    [
        'type' => 'link', 
        'url' => '/create', 
        'label' => 'Create New', 
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>',
        'shortcut' => 'Ctrl+N'
    ],
    [
        'type' => 'link', 
        'url' => '/import', 
        'label' => 'Import Data', 
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>'
    ],
    ['type' => 'divider'],
    ['type' => 'header', 'label' => 'Settings'],
    [
        'type' => 'link', 
        'url' => '/preferences', 
        'label' => 'Preferences', 
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
    ],
    ['type' => 'divider'],
    [
        'type' => 'button', 
        'label' => 'Clear Cache', 
        'style' => 'warning',
        'onclick' => 'clearCache()',
        'confirm' => 'This will clear all cached data. Continue?',
        'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
    ]
];
@endphp

<x-dropdown :items="$complexItems" width="64" align="left">
    <x-slot name="trigger">
        <button class="btn-primary">
            Actions
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </x-slot>
</x-dropdown>
```

### Notification Dropdown:
```blade
<x-dropdown width="80" align="right">
    <x-slot name="trigger">
        <button class="relative p-2 text-gray-600 hover:text-gray-900">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9.586 14.414L7.5 16.5 2 11l2.586-2.586L9.586 14.414z"></path>
            </svg>
            <span class="absolute top-0 right-0 w-3 h-3 bg-red-500 rounded-full"></span>
        </button>
    </x-slot>
    
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            <span class="text-xs text-gray-500">3 new</span>
        </div>
    </x-slot>
    
    @foreach($notifications as $notification)
    <div class="px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
        <p class="text-xs text-gray-600 mt-1">{{ $notification->message }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
    </div>
    @endforeach
    
    <div class="p-2">
        <a href="/notifications" class="block text-center text-sm text-blue-600 hover:text-blue-800">View all notifications</a>
    </div>
</x-dropdown>
```

## Migration Guide

### From Old Dropdown to New Component:

**Old Code:**
```blade
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open">Menu</button>
    <div x-show="open" @click.away="open = false">
        <a href="/profile">Profile</a>
    </div>
</div>
```

**New Code:**
```blade
<x-dropdown>
    <x-slot name="trigger">
        <button>Menu</button>
    </x-slot>
    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
</x-dropdown>
```

## Styling

All dropdown components use consistent styling with:
- Glassmorphism design effects
- Dark mode support
- Smooth transitions
- Proper focus management
- Accessible keyboard navigation
- Mobile-responsive design

The components automatically handle:
- Click outside to close
- Escape key to close
- Proper z-index layering
- Responsive positioning
- Animation transitions

## Modals

This section consolidates the modal/confirmation guidance for confirmation/floating modals used across the app.

### Overview
Reusable floating confirmation modal used for operations that require user confirmation (delete, approve, etc.). The modal uses a floating design with no backdrop overlay so underlying content stays visible.

### Component location
`resources/views/components/confirmation-modal.blade.php`

### Basic usage
Include component:

```blade
<x-confirmation-modal 
        id="delete-confirmation-modal"
        title="Delete Item"
        message="Are you sure you want to delete?"
        confirm-text="Delete"
        cancel-text="Cancel"
        confirm-class="btn-danger"
        icon="warning"
/>
```

Open from JS:

```javascript
openConfirmationModal('delete-confirmation-modal', function() {
    document.getElementById(`delete-form-${id}`).submit();
});
```

### Props and options (summary)
- `id`, `title`, `message`, `confirmText`, `cancelText`, `confirmClass`, `icon`.

### Design & behavior
- Floating center modal (no backdrop), z-index `z-50`, shadow, smooth enter/leave animations.
- Close options: Cancel button, X button, Escape key. Background click does NOT close (floating design).

### JS Integration
- Global helpers:
    - `openConfirmationModal(modalId, action, options)` — open modal and run `action` on confirm.
    - `closeConfirmationModal(modalId)` — close modal.
- The modal component supports dynamic updates of title/message before opening.

### Examples
- Delete, Force Delete, Approve, Warning examples — use `confirm-class` to style confirm button appropriately.

### Troubleshooting
- Modal not opening: ensure Alpine.js is loaded and `openConfirmationModal` exists.
- Action not executing: check console for errors and verify the action function is defined.
- Styling issues: run `npm run build` and verify Tailwind classes.

### Future enhancements
- Custom sizes, animation options, multiple buttons, form integration, auto-close timers.

---
Note: The full original modal guide is archived in git history; this merged summary keeps the component guidance close to the UI components registry.

## Layout components

The following layout-related components were migrated from the legacy layout documentation to centralize UI documentation.

### Page Header Component (`x-page-header`)

Komponen ini digunakan untuk header halaman yang konsisten dengan breadcrumbs, title, dan actions.

Props:
- `title` (string): Judul halaman
- `subtitle` (string, optional): Subtitle halaman
- `icon` (string, optional): HTML SVG icon
- `breadcrumbs` (array, optional): Array breadcrumb items
- `actions` (slot, optional): Tombol atau actions di header
- `stats` (array, optional): Statistik yang ditampilkan di bawah header

Contoh penggunaan disarankan di dalam file referensi; implementasi detail ada di repo.

### Data Table Component (`x-data-table`)

Komponen responsif untuk menampilkan data dalam format table (desktop) dan card (mobile). Key props: `headers`, `rows`, `pagination`, `emptyTitle`, `emptyDescription`, `actions`.

Row format expects `cells` array and optional `actions` array; see examples in repo for canonical shapes.

### Modal Component (`x-modal`)

General-purpose modal component integrated with Alpine.js. Props: `id`, `title`, `size`, `closable`, `footer` slot. Use `openModal(id)` / `closeModal(id)` helpers.

### Helpers and Best Practices
- Modal functions: `openModal('modalId')`, `closeModal('modalId')`.
- Toast: `window.notify(type, message)` (types: success, error, warning, info).
- Prepare table data via controller helper `prepareTableData($items)` to normalize row/cell shapes.

For full examples and controller helpers, check views under `resources/views/components` in the repo or the layout section in this document.
