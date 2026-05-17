// as per sir this makes sure that everytime the page loads, the logic inside will also load.
document.addEventListener('DOMContentLoaded', function() {
    var chartLabels = window.barData.label;
    var chartValues = window.barData.data;

    var chartColors = [
        'rgba(255, 160, 122, 1)',
        'rgba(78, 205, 196, 1)',
        'rgba(69, 183, 209, 1)',
        'rgba(255, 107, 107, 1)',
        'rgba(152, 216, 200, 1)'
    ];

    var chartBorderColors = [
        'rgba(255, 107, 107, 1)',
        'rgba(78, 205, 196, 1)',
        'rgba(69, 183, 209, 1)',
        'rgba(255, 160, 122, 1)',
        'rgba(152, 216, 200, 1)'
    ];

    var legendOptions = {
        display: true,
        position: 'bottom',
        labels: {
            boxWidth: 14,
            padding: 14,
            font: { size: 11, family: "'Segoe UI', sans-serif" }
        }
    };

    var scaleOptions = {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1, font: { size: 11 } },
            grid: { color: 'rgba(127, 170, 136, 0.15)' }
        },
        x: {
            ticks: { font: { size: 11 } },
            grid: { display: false }
        }
    };

    var barChart = document.getElementById('myChart');
    if (barChart) {
        new Chart(barChart, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Number of Users per Home',
                    data: chartValues,
                    backgroundColor: chartColors,
                    borderColor: chartBorderColors,
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legendOptions },
                scales: scaleOptions
            }
        });
    }

    var doughnutChart = document.getElementById('myDoughnutChart');
    if (doughnutChart) {
        new Chart(doughnutChart, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Users per Home',
                    data: chartValues,
                    backgroundColor: chartColors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legendOptions }
            }
        });
    }

    var lineChart = document.getElementById('myLineChart');
    if (lineChart) {
        new Chart(lineChart, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Number of Users per Home',
                    data: chartValues,
                    backgroundColor: 'rgba(127, 170, 136, 0.2)',
                    borderColor: 'rgba(95, 148, 103, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: chartColors,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legendOptions },
                scales: scaleOptions
            }
        });
    }
});
