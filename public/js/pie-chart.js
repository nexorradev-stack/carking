document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('pieChart').getContext('2d');

    const data = {
        labels: ['Calls', 'Text', 'Emails'],
        datasets: [{
            data: [12, 23, 4], // Percentage values
            backgroundColor: ['#f5a623', '#333', '#3fa3ff'],
            hoverOffset: 4,
            borderWidth: 0
        }]
    };

    const options = {
        plugins: {
            legend: {
                display: false // Disabling the default legend as we're creating custom ones
            }
        },
        responsive: true,
        maintainAspectRatio: false,
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: options
    });
});
