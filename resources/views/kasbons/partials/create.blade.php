<form method="POST" action="{{ route('kasbons.store') }}" class="space-y-6">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pemohon</label>
        <select name="user_id" class="form-input w-full" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi</label>
        <select name="location_id" class="form-input w-full" required>
            @foreach($locations as $location)
                <option value="{{ $location->id }}">{{ $location->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal</label>
        <input type="date" name="date" class="form-input w-full" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah</label>
        <input type="number" name="amount" class="form-input w-full" step="0.01" min="0" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
        <textarea name="note" class="form-input w-full"></textarea>
    </div>
    <div class="flex justify-end">
        <button type="submit" class="btn-primary">Simpan</button>
    </div>
</form>
