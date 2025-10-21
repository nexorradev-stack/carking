document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('lineChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['1 Oct', '3 Oct', '7 Oct', '10 Oct', '14 Oct', '20 Oct', '23 Oct', '27 Oct', '30 Oct'],
            datasets: [{
                label: 'Page Views',
                data: [2000, 2500, 2000, 3000, 4000, 2000, 1000, 2000, 4000],
                borderColor: '#6a5bff',
                backgroundColor: 'transparent',
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6a5bff',
                pointBorderWidth: 2,
                pointRadius: 5,
                tension: 0.4, // Curved line
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Hides the legend
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false // Removes grid lines on x-axis
                    }
                },
                y: {
                    grid: {
                        color: '#eaeaea' // Light grey grid lines
                    },
                    ticks: {
                        callback: (value) => value, 
                    },
                    min: 0,
                    max: 4000,
                    stepSize: 1000
                }
            }
        }
    });
});