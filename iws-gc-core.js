document.addEventListener('DOMContentLoaded', function () {
    const chartElements = document.querySelectorAll('canvas[id^="iws_chart_"]');

    const iwsColors = [
        'rgba(54, 162, 235, 0.75)',   // Corporate Blue
        'rgba(255, 99, 132, 0.75)',   // Accent Crimson
        'rgba(75, 192, 192, 0.75)',   // Mint Teal
        'rgba(255, 206, 86, 0.75)',   // Deep Yellow
        'rgba(153, 102, 255, 0.75)',  // Slate Amethyst
        'rgba(255, 159, 64, 0.75)'    // Orange
    ];

    const iwsBorders = iwsColors.map(color => color.replace('0.75', '1'));

    chartElements.forEach(canvas => {
        try {
            const rawType = canvas.getAttribute('data-type').replace(/^"|"$/g, '').toLowerCase();
            const title = canvas.getAttribute('data-title').replace(/^"|"$/g, '');
            const unit = canvas.getAttribute('data-unit') ? canvas.getAttribute('data-unit').replace(/^"|"$/g, '') : '';
            
            const labels = JSON.parse(canvas.getAttribute('data-labels'));
            const parsedDataValues = JSON.parse(canvas.getAttribute('data-values'));
            const lineLabelsAttr = canvas.getAttribute('data-line-labels');

            let chartJsType = 'bar';
            let isHorizontal = false;

            if (rawType === 'bar') {
                chartJsType = 'bar';
            } else if (rawType === 'horizontalbar' || rawType === 'horizontal-bar') {
                chartJsType = 'bar';
                isHorizontal = true;
            } else if (rawType === 'line') {
                chartJsType = 'line';
            } else if (rawType === 'doughnut' || rawType === 'donut') {
                chartJsType = 'doughnut';
            } else if (rawType === 'pie') {
                chartJsType = 'pie';
            } else if (rawType === 'polararea' || rawType === 'polar-area') {
                chartJsType = 'polarArea';
            } else if (rawType === 'radar') {
                chartJsType = 'radar';
            }

            canvas.setAttribute('data-type', chartJsType);

            let datasetsPayload = [];

            // Detect if the data input is a multidimensional array matrix (for multiple comparative items)
            if (Array.isArray(parsedDataValues) && Array.isArray(parsedDataValues[0]) && ['line', 'bar', 'radar'].includes(chartJsType)) {
                const parsedLineLabels = lineLabelsAttr ? JSON.parse(lineLabelsAttr) : [];

                datasetsPayload = parsedDataValues.map((subArray, index) => {
                    const colorIndex = index % iwsColors.length;
                    return {
                        label: parsedLineLabels[index] ? parsedLineLabels[index] : `Dataset ${index + 1}`,
                        data: subArray,
                        backgroundColor: chartJsType === 'line' ? 'transparent' : iwsColors[colorIndex],
                        borderColor: iwsBorders[colorIndex],
                        borderWidth: 3,
                        tension: 0.35,
                        fill: false
                    };
                });
            } else {
                // Single line base execution path mapping profile
                const isSingleColorType = ['line', 'radar'].includes(chartJsType);
                datasetsPayload = [{
                    label: title || 'Data Metrics',
                    data: parsedDataValues,
                    backgroundColor: isSingleColorType ? iwsColors[0] : iwsColors,
                    borderColor: isSingleColorType ? iwsBorders[0] : iwsBorders,
                    borderWidth: 2,
                    tension: 0.35,
                    fill: chartJsType === 'line' ? 'origin' : false
                }];
            }

            const isCircularChart = ['pie', 'doughnut', 'polarArea'].includes(chartJsType);

            new Chart(canvas, {
                type: chartJsType, 
                data: {
                    labels: labels,
                    datasets: datasetsPayload
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: isHorizontal ? 'y' : 'x',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                font: { family: 'system-ui, -apple-system, sans-serif', size: 13 },
                                generateLabels: function(chart) {
                                    if (isCircularChart) {
                                        const data = chart.data;
                                        if (data.labels.length && data.datasets.length) {
                                            const dataset = data.datasets[0];
                                            return data.labels.map((label, i) => {
                                                const bgColor = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor[i % dataset.backgroundColor.length] : dataset.backgroundColor;
                                                return { text: `${label}: ${dataset.data[i]} ${unit}`.trim(), fillStyle: bgColor, strokeStyle: bgColor, index: i };
                                            });
                                        }
                                    }
                                    return Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                }
                            }
                        },
                        title: {
                            display: !!title,
                            text: title,
                            font: { family: 'system-ui, -apple-system, sans-serif', size: 18, weight: '600' },
                            padding: { bottom: 15 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    
                                    let val = '';
                                    if (context.parsed !== null && typeof context.parsed === 'number') {
                                        val = context.parsed;
                                    } else if (context.parsed !== null && typeof context.parsed === 'object') {
                                        val = isHorizontal ? context.parsed.x : context.parsed.y;
                                    } else {
                                        val = context.raw;
                                    }
                                    return `${label}${val} ${unit}`.trim();
                                }
                            }
                        }
                    },
                    scales: isCircularChart || chartJsType === 'radar' ? {} : {
                        y: {
                            beginAtZero: true,
                            grid: { 
                                display: !isHorizontal,
                                color: 'rgba(0, 0, 0, 0.04)',
                                drawOnChartArea: !isHorizontal
                            },
                            ticks: { callback: function(value) { return isHorizontal ? value : value + ' ' + unit; } }
                        },
                        x: {
                            beginAtZero: true,
                            grid: { 
                                display: isHorizontal,
                                color: 'rgba(0, 0, 0, 0.04)',
                                drawOnChartArea: isHorizontal
                            },
                            ticks: { callback: function(value) { return isHorizontal ? value + ' ' + unit : value; } }
                        }
                    }
                }
            });
        } catch (error) {
            console.error("IWS Graphs Engine Core Exception: ", error);
        }
    });
});
