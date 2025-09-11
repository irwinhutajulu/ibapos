<div class="space-y-4">
    <div>
        <span class="font-semibold">Kode:</span> {{ $kasbon->code }}
    </div>
    <div>
        <span class="font-semibold">Pemohon:</span> {{ $kasbon->requester->name ?? '-' }}
    </div>
    <div>
        <span class="font-semibold">Lokasi:</span> {{ $kasbon->location->name ?? '-' }}
    </div>
    <div>
        <span class="font-semibold">Tanggal:</span> {{ $kasbon->date->format('d-m-Y') }}
    </div>
    <div>
        <span class="font-semibold">Jumlah:</span> {{ number_format($kasbon->amount,2,',','.') }}
    </div>
    <div>
        <span class="font-semibold">Status:</span> {{ ucfirst($kasbon->status) }}
    </div>
    <div>
        <span class="font-semibold">Catatan:</span> {{ $kasbon->note }}
    </div>
    @if($kasbon->approver)
    <div>
        <span class="font-semibold">Disetujui oleh:</span> {{ $kasbon->approver->name }} pada {{ $kasbon->approved_at?->format('d-m-Y H:i') }}
    </div>
    @endif
</div>
