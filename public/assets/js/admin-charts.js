/**
 * Admin dashboard charts
 */
document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('salesChart');
    if (!canvas || typeof Chart === 'undefined') return;

    try {
        const res = await fetch(`${ECAFE_BASE}/api/admin/charts`);
        const data = await res.json();
        const sales = data.salesByDay || [];

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: sales.map(s => s.day),
                datasets: [{
                    label: 'Revenue (KES)',
                    data: sales.map(s => parseFloat(s.revenue)),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const popularCanvas = document.getElementById('popularChart');
        if (popularCanvas && data.popularFoods) {
            new Chart(popularCanvas, {
                type: 'doughnut',
                data: {
                    labels: data.popularFoods.map(f => f.name),
                    datasets: [{
                        data: data.popularFoods.map(f => f.order_count || 1),
                        backgroundColor: ['#2563eb', '#f97316', '#10b981', '#8b5cf6', '#ef4444'],
                    }]
                },
                options: { responsive: true }
            });
        }
    } catch (e) {
        console.error('Chart load failed', e);
    }
});
