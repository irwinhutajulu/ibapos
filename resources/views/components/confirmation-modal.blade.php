@props([
    'modalId' => 'confirmModal',
    'title' => 'Delete',
    'confirmLabel' => 'Delete',
    'cancelLabel' => 'Cancel',
    'message' => null,
])

<div id="{{ $modalId }}" style="display:none;" class="fixed inset-0 bg-black bg-opacity-40 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center" aria-hidden="true">
    <div class="relative mx-auto p-6 border w-96 max-w-sm shadow-2xl rounded-xl" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}_title" aria-describedby="{{ $modalId }}_desc">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 id="{{ $modalId }}_title" class="text-xl font-semibold text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
            <div id="{{ $modalId }}_desc" class="mb-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                    {!! $message ?? 'Are you sure you want to perform this action?' !!}
                </p>
                <p class="text-red-500 dark:text-red-400 text-xs mt-2 font-medium">
                    This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3">
                <button type="button" data-close-button="{{ $modalId }}"
                        class="px-6 py-2.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200 min-w-[80px]">
                    {{ $cancelLabel }}
                </button>

                <form id="{{ $modalId }}_form" method="POST" class="inline" data-ajax="true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" id="{{ $modalId }}_confirm" class="px-6 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 min-w-[80px]">
                        <span class="confirm-text">{{ $confirmLabel }}</span>
                        <svg class="confirm-loading w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* Confirmation modal helper (available globally) */
window.openConfirmationModal = window.openConfirmationModal || function(modalId, actionUrl, name, options = {}) {
    const modal = document.getElementById(modalId);
    const form = document.getElementById(modalId + '_form');
    const confirmBtn = document.getElementById(modalId + '_confirm');

    if (!modal || !form || !confirmBtn) {
        console.error('Confirmation modal elements not found:', modalId);
        return false;
    }

    // Fill name placeholders inside modal (if any)
    const nameSpans = modal.querySelectorAll('.confirm-target-name');
    nameSpans.forEach(s => s.textContent = name);

    // set form action so fallback non-JS works
    form.action = actionUrl;

    // Store data attributes for use in submit handler
    form.dataset.ajax = options.ajax === false ? 'false' : 'true';
    form.dataset.rowId = options.rowId || '';

    // Save the previously focused element so we can restore focus later
    modal.__previouslyFocused = document.activeElement;

    // Show modal and set aria
    modal.style.display = '';
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    // Lock background scroll
    document.body.style.overflow = 'hidden';

    // Focus first focusable element inside modal (confirm button or other)
    const focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const firstFocusable = focusable.length ? focusable[0] : confirmBtn;
    (firstFocusable || confirmBtn).focus();

    // Setup simple focus trap
    function trapTabKey(e) {
        if (e.key !== 'Tab') return;
        const focusableElems = Array.from(modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')).filter(el => !el.disabled && el.offsetParent !== null);
        if (!focusableElems.length) return;
        const first = focusableElems[0];
        const last = focusableElems[focusableElems.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    }

    function onKeydown(e) {
        if (e.key === 'Escape') {
            closeModal(modalId);
        }
        trapTabKey(e);
    }

    modal.__onKeydown = onKeydown;
    document.addEventListener('keydown', modal.__onKeydown);

    // Prevent clicking background from focusing away; close on background click
    modal.addEventListener('click', function bgClick(e) {
        if (e.target === modal) closeModal(modalId);
    }, { once: false });

    return true;
};

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    // Restore focus first to avoid hiding a focused descendant (ARIA requirement)
    try {
        const prev = modal.__previouslyFocused;
        if (prev && typeof prev.focus === 'function') {
            prev.focus();
        } else if (document.activeElement && modal.contains(document.activeElement)) {
            // If no previous focus saved, blur any focused element inside modal
            try { document.activeElement.blur(); } catch (err) {}
        }
    } catch (err) {
        // ignore
    }

    // Remove keydown listener if set
    if (modal.__onKeydown) {
        document.removeEventListener('keydown', modal.__onKeydown);
        delete modal.__onKeydown;
    }

    // Hide modal and mark aria hidden
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.add('hidden');
    modal.style.display = 'none';

    // Restore scrolling
    document.body.style.overflow = '';
}

// Delegated close button handler
document.addEventListener('click', function(e) {
    const closeAttr = e.target.closest('[data-close-button]');
    if (closeAttr) {
        const modalId = closeAttr.getAttribute('data-close-button');
        closeModal(modalId);
    }
});

// Delegated open handler: if a button with data-delete-modal is clicked, open modal
document.addEventListener('click', function(e) {
    const opener = e.target.closest('[data-delete-modal]');
    if (!opener) return;

    const modalId = opener.getAttribute('data-delete-modal');
    const id = opener.getAttribute('data-delete-id');
    const name = opener.getAttribute('data-delete-name');
    if (!modalId || !id) return;

    // Prevent default if it's a button inside a link or similar
    e.preventDefault();

    // Debug: log opener click
    try { console.debug('[confirm-modal] opener clicked', { modalId, id, name, opener }); } catch (err) {}

    // Call the global helper, with a small retry fallback if it's not yet defined
    if (window.openConfirmationModal) {
        // Build action URL generically: take provided data-delete-action (or current path)
        // and append the id. This works for both /admin/users and /admin/locations
    const baseAction = opener.getAttribute('data-delete-action') || window.location.pathname.replace(/\/+$/, '');
    const actionUrl = baseAction.replace(/\/+$/, '') + '/' + id;
        window.openConfirmationModal(modalId, actionUrl, name, { rowId: id });
    } else {
        try { console.warn('[confirm-modal] openConfirmationModal not defined yet, retrying'); } catch (err) {}
        // retry a few times
        let attempts = 0;
        const t = setInterval(() => {
            attempts++;
            if (window.openConfirmationModal) {
                const baseAction = opener.getAttribute('data-delete-action') || window.location.pathname.replace(/\/+$/, '');
                const actionUrl = baseAction.replace(/\/+$/, '') + '/' + id;
                window.openConfirmationModal(modalId, actionUrl, name, { rowId: id });
                clearInterval(t);
            } else if (attempts > 10) {
                try { console.error('[confirm-modal] openConfirmationModal still not defined after retries'); } catch (err) {}
                clearInterval(t);
            }
        }, 50);
    }
});

// Intercept form submissions for our modal forms
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.id || !form.id.endsWith('_form')) return;

    // Only intercept when data-ajax isn't explicitly false
    if (form.dataset.ajax === 'false') return;

    e.preventDefault();

    const confirmBtn = document.getElementById(form.id.replace('_form', '_confirm'));
    const confirmText = confirmBtn ? confirmBtn.querySelector('.confirm-text') : null;
    const loading = confirmBtn ? confirmBtn.querySelector('.confirm-loading') : null;

    if (confirmBtn) {
        confirmBtn.disabled = true;
        if (confirmText) confirmText.textContent = 'Deleting...';
        if (loading) loading.classList.remove('hidden');
    }

    const url = form.action;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
    }).then(async res => {
        const modalId = form.id.replace('_form', '');
        const modal = document.getElementById(modalId);

        if (res.ok) {
            // Close modal
            closeModal(modalId);

            // Remove row if rowId set (support both data-location-id and data-user-id)
            const rowId = form.dataset.rowId;
            if (rowId) {
                const trLoc = document.querySelector('[data-location-id="' + rowId + '"]');
                const trUser = document.querySelector('[data-user-id="' + rowId + '"]');
                if (trLoc) trLoc.remove();
                if (trUser) trUser.remove();
            }

            // Try parse JSON message
            try {
                const data = await res.json();
                if (data && data.message) {
                    window.notify(data.message, 'success');
                } else {
                    window.notify('Deleted successfully', 'success');
                }
            } catch (err) {
                window.notify('Deleted successfully', 'success');
            }
        } else {
            // Attempt to read error
            try {
                const data = await res.json();
                window.notify(data.message || 'Delete failed', 'error');
            } catch (err) {
                window.notify('Delete failed', 'error');
            }
        }
    }).catch(err => {
        console.error('Delete error', err);
        window.notify('Network error while deleting', 'error');
    }).finally(() => {
        if (confirmBtn) {
            confirmBtn.disabled = false;
            if (confirmText) confirmText.textContent = '{{ $confirmLabel }}';
            if (loading) loading.classList.add('hidden');
        }
    });
});
</script>

