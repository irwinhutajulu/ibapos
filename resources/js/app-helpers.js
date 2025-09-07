// Global helper functions used by Blade-inserted forms and modals
export function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

export function validateProductForm() {
    try {
        const nameEl = document.getElementById('name');
        const categoryEl = document.getElementById('category_id');
        const priceEl = document.getElementById('price');

        const name = nameEl ? nameEl.value.trim() : '';
        const category = categoryEl ? categoryEl.value : '';
        const price = priceEl ? parseFloat(priceEl.value) : NaN;

        if (!name) {
            // Use window.notify if available
            if (window.notify) window.notify('Product name is required', 'error');
            else alert('Product name is required');
            return false;
        }

        if (!category) {
            if (window.notify) window.notify('Category is required', 'error');
            else alert('Category is required');
            return false;
        }

        if (isNaN(price) || price < 0) {
            if (window.notify) window.notify('Valid price is required', 'error');
            else alert('Valid price is required');
            return false;
        }

        return true;
    } catch (err) {
        console.error('validateProductForm error:', err);
        return true;
    }
}

// Make these available globally for legacy inline calls
if (typeof window !== 'undefined') {
    window.previewImage = previewImage;
    window.validateProductForm = validateProductForm;
}
