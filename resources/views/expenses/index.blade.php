@extends('layouts.app')

@section('title','Expenses')
@section('content')

<div class="p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses</h2>
            <p class="text-gray-600 dark:text-gray-400">Manage your expenses and financial records</p>
        </div>
        @can('expenses.create')
        <button onclick="openCreateExpenseModal()" class="btn-primary w-full sm:w-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Expense
        </button>
        @endcan
    </div>

    @if(session('success'))
        <x-floating-success :message="session('success')" />
    @endif

    <!-- Responsive Table for Desktop -->
    <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->date->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap"><x-currency :value="$expense->amount" /></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $expense->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @can('expenses.update')
                                <button onclick="openEditExpenseModal({{ $expense->id }})" class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                @endcan
                                @can('expenses.delete')
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" onclick="return confirm('Delete expense?')">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-6 text-gray-500 dark:text-gray-400">No expenses found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $expenses->links() }}</div>
    </div>

    <!-- Card View for Mobile -->
    <div class="lg:hidden space-y-4">
        @forelse($expenses as $expense)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-start space-x-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $expense->description }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $expense->category->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $expense->date->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-lg font-semibold text-gray-900 dark:text-white"><x-currency :value="$expense->amount" /></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">User: {{ $expense->user->name ?? '-' }}</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @can('expenses.update')
                        <button onclick="openEditExpenseModal({{ $expense->id }})" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        @endcan
                        @can('expenses.delete')
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" onclick="return confirm('Delete expense?')">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No expenses found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new expense to get started.</p>
            @can('expenses.create')
            <div class="mt-6">
                <button onclick="openCreateExpenseModal()" class="btn-primary w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Your First Expense
                </button>
            </div>
            @endcan
        </div>
        @endforelse
    </div>

    <!-- Modal for Create/Edit Expense -->
    <x-modal id="expense-modal" title="Expense" size="xl" :max-height="true">
        <div id="expense-modal-content">
            <div class="flex items-center justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
            </div>
        </div>
    </x-modal>
</div>

@push('scripts')
<script>
function openCreateExpenseModal() {
    updateExpenseModalTitle('Add Expense');
    loadExpenseModalContent('{{ route("expenses.create") }}?modal=1');
    openModal('expense-modal');
}

function openEditExpenseModal(id) {
    updateExpenseModalTitle('Edit Expense');
    loadExpenseModalContent(`{{ url('expenses') }}/${id}/edit?modal=1`);
    openModal('expense-modal');
}

function updateExpenseModalTitle(title) {
    const modalTitle = document.querySelector('#expense-modal h3');
    if (modalTitle) {
        modalTitle.textContent = title;
    }
}

async function loadExpenseModalContent(url) {
    const content = document.getElementById('expense-modal-content');
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-gray-600 dark:text-gray-400">Loading...</span>
        </div>
    `;
    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });
        if (response.ok) {
            const html = await response.text();
            content.innerHTML = html;
        } else {
            content.innerHTML = `<div class="text-center py-8"><svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Error Loading Content</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please try again later.</p></div>`;
        }
    } catch (error) {
        content.innerHTML = `<div class="text-center py-8"><svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg><h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Connection Error</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Please check your internet connection.</p></div>`;
    }
}

// Handle form submission in modal
document.addEventListener('submit', function(e) {
    if (e.target.closest('#expense-modal')) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...`;
        submitBtn.disabled = true;
        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json().then(data => {
                    closeModal('expense-modal');
                    if (data.message) {
                        window.notify(data.message, 'success');
                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        location.reload();
                    }
                });
            } else {
                return response.text().then(text => {
                    const content = document.getElementById('expense-modal-content');
                    content.innerHTML = text;
                });
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
});
</script>
@endpush
@endsection
