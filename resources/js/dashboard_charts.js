document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('attendanceChart');
    if (!canvas) return;

    const rawData = canvas.getAttribute('data-chart');
    if (!rawData) return;

    try {
        const chartData = JSON.parse(rawData);

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Tỷ lệ đi học (%)',
                        data: chartData.presence,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Tỷ lệ vắng (%)',
                        data: chartData.absence,
                        backgroundColor: '#f43f5e',
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Inter, sans-serif', size: 12, weight: '600' },
                            usePointStyle: true,
                            boxWidth: 8,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ` ${context.dataset.label}: ${context.raw}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter, sans-serif', size: 12 } }
                    },
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: function (value) { return value + '%'; }
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    } catch (e) {
        console.error('Lỗi khởi tạo Chart.js:', e);
    }
});