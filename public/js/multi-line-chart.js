document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('activityChart').getContext('2d');

    const data = {
        labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Calls',
                data: [30, 40, 35, 50, 45, 60, 55],
                borderColor: '#f5a623',
                tension: 0.4,
                fill: false,
            },
            {
                label: 'Texts',
                data: [20, 30, 25, 40, 35, 50, 45],
                borderColor: '#3fa3ff',
                tension: 0.4,
                fill: false,
            },
            {
                label: 'Emails',
                data: [15, 25, 20, 30, 25, 40, 35],
                borderColor: '#333',
                tension: 0.4,
                fill: false,
            }
        ]
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
                position: 'top',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                },
            },
            tooltip: {
                mode: 'index', // Show tooltip for all datasets at the hovered index
                intersect: false, // Allows hovering anywhere along the x-axis
                callbacks: {
                    label: function (tooltipItem) {
                        const datasetLabel = tooltipItem.dataset.label || '';
                        const value = tooltipItem.raw;
                        return `${datasetLabel}: ${value}`;
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    drawOnChartArea: false,
                    drawTicks: false,
                }
            },
            y: {
                ticks: {
                    stepSize: 25,
                    callback: function (value) {
                        return value;
                    }
                },
                grid: {
                    drawTicks: false,
                    drawBorder: false,
                    drawOnChartArea: true,
                    color: function (context) {
                        return context.tick.value === 0 ? '#ddd' : '#ccc';
                    },
                    lineWidth: 1,
                }
            }
        }
    };
    
    const myChart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: options
    });    
});
