@extends('layouts.app')
@section('title','Kasbon')
@section('content')
<div class="min-h-screen flex">
    <div class="flex-1 flex flex-col min-w-0">
        <main class="flex-1">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Kasbon</h2>
                        <p class="text-gray-600 dark:text-gray-400">Daftar kasbon karyawan</p>
                    </div>
                    @can('kasbons.create')
                    <button onclick="openCreateKasbonModal()" class="btn-primary w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Kasbon
                    </button>
                    @endcan
                </div>
                @if(session('success'))
                    <x-floating-success :message="session('success')" />
                @endif
                <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pemohon</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($kasbons as $kasbon)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $kasbon->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $kasbon->requester->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $kasbon->location->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $kasbon->date->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">{{ number_format($kasbon->amount,2,',','.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($kasbon->status) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            @can('kasbons.update')
                                            <button onclick="openEditKasbonModal({{ $kasbon->id }})" class="inline-flex items-center px-3 py-1.5 border border-blue-200 dark:border-blue-800 text-xs font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </button>
                                            @endcan
                                            @can('kasbons.delete')
                                            <form action="{{ route('kasbons.destroy', $kasbon) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-200 dark:border-red-800 text-xs font-medium rounded-lg text-red-600 dark:text-red-400 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" onclick="return confirm('Hapus kasbon?')">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">Tidak ada data kasbon.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $kasbons->links() }}</div>
                </div>
                <!-- Modal untuk Create/Edit Kasbon -->
                <x-modal id="kasbon-modal" title="Kasbon" size="xl" :max-height="true">
                    <div id="kasbon-modal-content">
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
        </main>
    </div>
</div>
@push('scripts')
<script>
function openCreateKasbonModal() {
    updateKasbonModalTitle('Tambah Kasbon');
    loadKasbonModalContent('{{ route("kasbons.create") }}?modal=1');
    openModal('kasbon-modal');
}
function openEditKasbonModal(id) {
    updateKasbonModalTitle('Edit Kasbon');
    loadKasbonModalContent(`{{ url('kasbons') }}/${id}/edit?modal=1`);
    openModal('kasbon-modal');
}
function updateKasbonModalTitle(title) {
    const modalTitle = document.querySelector('#kasbon-modal h3');
    if (modalTitle) {
        modalTitle.textContent = title;
    }
}
async function loadKasbonModalContent(url) {
    const content = document.getElementById('kasbon-modal-content');
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
</script>
@endpush
