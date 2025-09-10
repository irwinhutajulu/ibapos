@props([])

@auth
<div class="flex items-center space-x-2" x-data="locationSelector()">
    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Location:</label>
    <select x-model="selected" @change="set()"
            :disabled="loading"
            class="px-3 py-2 border border-gray-200 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
        @php($activeId = session('active_location_id'))
        @foreach(auth()->user()->locations as $loc)
            <option value="{{ $loc->id }}" @selected($activeId == $loc->id)>{{ $loc->name }}</option>
        @endforeach
    </select>
    <!-- loading spinner -->
    <div x-show="loading" class="w-6 h-6 flex items-center justify-center">
        <svg class="animate-spin h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
    </div>
</div>

<script>
    function locationSelector() {
        return {
            loading: false,
            selected: {{ (int) session('active_location_id') ?? 'null' }},
            async set() {
                const prev = this.selected;
                // optimistic UI: save selection locally and set global var
                try {
                    // write immediately to localStorage for instant persistence
                    localStorage.setItem('active_location_id', String(this.selected));
                    window.appActiveLocationId = this.selected;
                } catch (e) {
                    console.warn('localStorage not available', e);
                }

                this.loading = true;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch('{{ route('active-location.set') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ location_id: this.selected })
                    });

                    if (!res.ok) {
                        const err = await res.text();
                        window.notify('Failed to change active location', 'error');
                        console.error('Active location error', err);
                        // revert optimistic change
                        this.revert(prev);
                        return;
                    }

                    const data = await res.json();
                    if (data.success) {
                        window.appActiveLocationId = data.active_location_id;
                        try { localStorage.setItem('active_location_id', String(data.active_location_id)); } catch (e) {}
                        window.notify('Active location set', 'success');
                    } else {
                        window.notify('Failed to set location', 'error');
                        this.revert(prev);
                    }
                } catch (e) {
                    console.error(e);
                    window.notify('Network error while setting location', 'error');
                    this.revert(prev);
                } finally {
                    this.loading = false;
                }
            },
            revert(prev) {
                // revert UI and localStorage
                this.selected = prev;
                try { localStorage.setItem('active_location_id', String(prev)); } catch (e) {}
                window.appActiveLocationId = prev;
            }
        }
    }
</script>
@endauth
