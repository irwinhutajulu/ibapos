@extends('layouts.app')

@section('content')
<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Chart Test (isolated)</h2>
    <div class="w-full max-w-3xl bg-white p-4 rounded-lg shadow">
        <div id="chart-area" style="height:360px">
            <canvas id="chart-test-canvas" height="320"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(async function(){
    try {
        const res = await fetch(`${window.appBaseUrl || ''}/debug/reports/sales-series-sample?days=7`, { credentials: 'same-origin' });
        const js = await res.json();
        console.log('sample series', js);
        const ctx = document.getElementById('chart-test-canvas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: js.labels.map(l=>new Date(l).toLocaleDateString()),
                datasets: [{
                    label: 'Sample Sales',
                    data: js.data,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.12)',
                    fill: true,
                }]
            },
            options: { maintainAspectRatio: false }
        });
    } catch (err) { console.error('chart test failed', err); }
})();
</script>
@endpush
