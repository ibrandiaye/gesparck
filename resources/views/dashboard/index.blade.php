@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tableau de bord</h2>

    <canvas id="fuelChart" height="100"></canvas>
    <canvas id="panneChart" height="100" class="mt-5"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const fuelCtx = document.getElementById('fuelChart').getContext('2d');
const fuelChart = new Chart(fuelCtx, {
    type: 'bar',
    data: {
        labels: @json($consommation->pluck('vehicle.immatriculation')),
        datasets: [{
            label: 'Litres consommés',
            data: @json($consommation->pluck('total')),
            backgroundColor: 'rgba(54, 162, 235, 0.6)'
        }]
    }
});

const panneCtx = document.getElementById('panneChart').getContext('2d');
const panneChart = new Chart(panneCtx, {
    type: 'pie',
    data: {
        labels: @json($pannes->pluck('type_panne')),
        datasets: [{
            label: 'Pannes',
            data: @json($pannes->pluck('total')),
            backgroundColor: ['#f44336', '#ff9800', '#4caf50', '#2196f3']
        }]
    }
});
</script>
@endsection
