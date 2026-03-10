document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('chartsWrapper')
    if (!wrapper || typeof allChartData === 'undefined' || Object.keys(allChartData).length === 0) {
        console.warn("No chart data available or wrapper not found.");
        return;
    }

    const isMonthly = (currentView === 'monthly');

    if (isMonthly) {
        
        const container = document.createElement('div');
        container.className = "monthly-chart-box";
        container.style.cssText = "background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #eee; height: 500px; width: 100%; margin-bottom: 20px;";
        container.innerHTML = `<canvas id="mainMonthlyChart"></canvas>`;
        wrapper.appendChild(container);

        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const labels = [];
        const dateKeys = [];

   
        for (let d = 1; d <= daysInMonth; d++) {
            labels.push(d.toString());
            const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            dateKeys.push(fullDate);
        }

        const colors = ['#db3434', '#3498db', '#2ecc71', '#f1c40f', '#9b59b6', '#e67e22', '#1abc9c', '#34495e'];
        
        const datasets = Object.keys(allChartData).map((item, index) => ({
            label: item,
            data: dateKeys.map(key => allChartData[item][key] || 0),
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + '15',
            borderWidth: 2,
            tension: 0.3,
            pointRadius: 2, 
            fill: false
        }));

        new Chart(document.getElementById('mainMonthlyChart'), {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    title: { display: true, text: `Usage for ${now.toLocaleString('default', { month: 'long' })} ${year}` }
                },
                scales: {
                    x: { title: { display: true, text: 'Day of Month' }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

    } else {
       
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        wrapper.style.display = "grid";
        wrapper.style.gridTemplateColumns = "repeat(auto-fit, minmax(300px, 1fr))";
        wrapper.style.gap = "20px";
        
        Object.keys(allChartData).forEach(item => {
            const chartDiv = document.createElement('div');
            chartDiv.className = "weekly-chart-box";
            chartDiv.style.cssText = "background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee;";
            
            const canvasId = `chart-${item.replace(/[^a-z0-9]/gi, '-')}`;
            chartDiv.innerHTML = `
                <strong style="display:block; margin-bottom:10px; color: #334155;">${item}</strong>
                <div style="height:250px;"><canvas id="${canvasId}"></canvas></div>
            `;
            wrapper.appendChild(chartDiv);

            new Chart(document.getElementById(canvasId), {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Usage',
                        data: allChartData[item],
                        borderColor: '#072d7abd',
                        backgroundColor: 'rgba(92, 157, 203, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { 
                        y: { beginAtZero: true, ticks: { stepSize: 1 } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    }
});