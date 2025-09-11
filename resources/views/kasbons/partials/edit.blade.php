<form method="POST" action="{{ route('kasbons.update', $kasbon) }}" class="space-y-6">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
        <input type="date" name="date" class="form-input w-full" value="{{ $kasbon->date->format('Y-m-d') }}" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
        <input type="number" name="amount" class="form-input w-full" step="0.01" min="0" value="{{ $kasbon->amount }}" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
        <textarea name="note" class="form-input w-full">{{ $kasbon->note }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select name="status" class="form-input w-full" required>
            <option value="pending" @if($kasbon->status=='pending') selected @endif>Pending</option>
            <option value="approved" @if($kasbon->status=='approved') selected @endif>Approved</option>
            <option value="rejected" @if($kasbon->status=='rejected') selected @endif>Rejected</option>
        </select>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="btn-primary">Update</button>
    </div>
</form>
