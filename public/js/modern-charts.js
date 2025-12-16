// Modern Charts for PEMS - Enhanced Version
class ModernChartsEngine {
    constructor() {
        this.colors = {
            primary: ['#ff6b6b', '#ee5a52'],
            secondary: ['#4ecdc4', '#44a08d'],
            accent: ['#45b7d1', '#2196f3'],
            success: ['#96ceb4', '#feca57'],
            warning: ['#feca57', '#f39c12'],
            purple: ['#8b5cf6', '#7c3aed'],
            pink: ['#ec4899', '#db2777'],
            indigo: ['#6366f1', '#4f46e5']
        };

        this.chartInstances = {};
        this.animationDuration = 2500;
        this.init();
    }

    async init() {
        await this.loadDependencies();
        this.setupGlobalDefaults();
        this.initializeCharts();
        this.setupEventListeners();
    }

    async loadDependencies() {
        if (typeof Chart === 'undefined') {
            await this.loadScript('https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js');
        }
    }

    loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    setupGlobalDefaults() {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = 'Cairo, sans-serif';
            Chart.defaults.font.size = 12;
            Chart.defaults.color = '#4a5568';
        }
    }

    initializeCharts() {
        this.createModernCategoryChart();
        this.createEnhancedBudgetChart();
        this.createTrendAnalysisChart();
        this.createComparisonChart();
    }

    createModernCategoryChart() {
        const ctx = document.getElementById('modernCategoryChart');
        if (!ctx) return;

        const data = [
            { name: 'مصروفات الممثلين والفنانين', amount: 850000, color: this.colors.primary },
            { name: 'مصروفات الطعام والضيافة', amount: 620000, color: this.colors.secondary },
            { name: 'مصروفات الديكور والإكسسوار', amount: 380000, color: this.colors.accent },
            { name: 'مصروفات النقل والمواصلات', amount: 245000, color: this.colors.success },
            { name: 'مصروفات المعدات والتقنية', amount: 180000, color: this.colors.warning }
        ];

        const gradient = ctx.getContext('2d');
        const backgroundColors = data.map((item, index) => {
            const grad = gradient.createLinearGradient(0, 0, 0, 400);
            grad.addColorStop(0, item.color[0]);
            grad.addColorStop(1, item.color[1]);
            return grad;
        });

        this.chartInstances.categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.name),
                datasets: [{
                    label: 'المبلغ (ريال سعودي)',
                    data: data.map(item => item.amount),
                    backgroundColor: data.map(item => `${item.color[0]}CC`),
                    borderColor: data.map(item => item.color[0]),
                    borderWidth: 3,
                    borderRadius: 15,
                    borderSkipped: false,
                    barThickness: 60,
                    maxBarThickness: 80,
                    hoverBackgroundColor: data.map(item => item.color[0]),
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        cornerRadius: 15,
                        displayColors: false,
                        titleFont: { family: 'Cairo', size: 16, weight: 'bold' },
                        bodyFont: { family: 'Cairo', size: 14 },
                        padding: 15,
                        callbacks: {
                            title: (context) => context[0].label,
                            label: (context) => {
                                const value = new Intl.NumberFormat('ar-SA', {
                                    style: 'currency',
                                    currency: 'SAR',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.x);
                                return `المبلغ: ${value}`;
                            },
                            afterLabel: (context) => {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed.x / total) * 100).toFixed(1);
                                return `النسبة: ${percentage}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.08)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Cairo', size: 12, weight: '600' },
                            color: '#4a5568',
                            callback: (value) => new Intl.NumberFormat('ar-SA', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value)
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Cairo', size: 14, weight: '700' },
                            color: '#2d3748',
                            maxRotation: 0,
                            callback: function(value) {
                                const label = this.getLabelForValue(value);
                                return label.length > 35 ? label.substring(0, 35) + '...' : label;
                            }
                        }
                    }
                },
                animation: {
                    duration: this.animationDuration,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        this.createCustomLegend('categoryLegend', data);
    }

    createEnhancedBudgetChart() {
        const ctx = document.getElementById('enhancedBudgetChart');
        if (!ctx) return;

        const data = {
            labels: ['آمن (65%)', 'تحذير (25%)', 'خطر (10%)'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: [
                    this.colors.secondary[0],
                    this.colors.warning[0],
                    this.colors.primary[0]
                ],
                borderColor: '#ffffff',
                borderWidth: 6,
                hoverBorderWidth: 8,
                hoverOffset: 20
            }]
        };

        this.chartInstances.budgetChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 30,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: 'Cairo', size: 16, weight: '700' },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        cornerRadius: 15,
                        callbacks: {
                            label: (context) => {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${percentage}%`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: this.animationDuration,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    createTrendAnalysisChart() {
        const ctx = document.getElementById('trendAnalysisChart');
        if (!ctx) return;

        const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        const expenseData = [850000, 920000, 780000, 1100000, 950000, 880000];
        const budgetData = [1000000, 1000000, 1000000, 1200000, 1200000, 1200000];

        this.chartInstances.trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'المصروفات الفعلية',
                        data: expenseData,
                        borderColor: this.colors.primary[0],
                        backgroundColor: `${this.colors.primary[0]}20`,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: this.colors.primary[0],
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 4,
                        pointRadius: 8,
                        pointHoverRadius: 12,
                        borderWidth: 4
                    },
                    {
                        label: 'الميزانية المخططة',
                        data: budgetData,
                        borderColor: this.colors.secondary[0],
                        backgroundColor: `${this.colors.secondary[0]}20`,
                        fill: false,
                        tension: 0.4,
                        borderDash: [10, 5],
                        pointBackgroundColor: this.colors.secondary[0],
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 4,
                        pointRadius: 8,
                        pointHoverRadius: 12,
                        borderWidth: 4
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
                            padding: 25,
                            usePointStyle: true,
                            font: { family: 'Cairo', size: 16, weight: '700' },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        cornerRadius: 15,
                        callbacks: {
                            label: (context) => {
                                const value = new Intl.NumberFormat('ar-SA', {
                                    style: 'currency',
                                    currency: 'SAR',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                                return `${context.dataset.label}: ${value}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(0, 0, 0, 0.08)', drawBorder: false },
                        ticks: {
                            font: { family: 'Cairo', size: 14, weight: '600' },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.08)', drawBorder: false },
                        ticks: {
                            font: { family: 'Cairo', size: 14, weight: '600' },
                            color: '#6b7280',
                            callback: (value) => new Intl.NumberFormat('ar-SA', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value)
                        }
                    }
                },
                animation: {
                    duration: this.animationDuration,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    createComparisonChart() {
        const ctx = document.getElementById('comparisonChart');
        if (!ctx) return;

        const projects = ['مشروع الدراما الكوميدية', 'مشروع الوثائقي', 'مشروع الإعلان التجاري'];
        const budgetData = [1200000, 800000, 500000];
        const spentData = [1100000, 750000, 480000];

        this.chartInstances.comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: projects,
                datasets: [
                    {
                        label: 'الميزانية المخططة',
                        data: budgetData,
                        backgroundColor: `${this.colors.accent[0]}80`,
                        borderColor: this.colors.accent[0],
                        borderWidth: 3,
                        borderRadius: 8
                    },
                    {
                        label: 'المصروف الفعلي',
                        data: spentData,
                        backgroundColor: `${this.colors.primary[0]}80`,
                        borderColor: this.colors.primary[0],
                        borderWidth: 3,
                        borderRadius: 8
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
                            padding: 20,
                            font: { family: 'Cairo', size: 14, weight: '600' },
                            color: '#374151'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        cornerRadius: 12,
                        callbacks: {
                            label: (context) => {
                                const value = new Intl.NumberFormat('ar-SA', {
                                    style: 'currency',
                                    currency: 'SAR',
                                    minimumFractionDigits: 0
                                }).format(context.parsed.y);
                                return `${context.dataset.label}: ${value}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Cairo', size: 12, weight: '600' },
                            color: '#4a5568'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.08)', drawBorder: false },
                        ticks: {
                            font: { family: 'Cairo', size: 12, weight: '600' },
                            color: '#6b7280',
                            callback: (value) => new Intl.NumberFormat('ar-SA', {
                                notation: 'compact',
                                compactDisplay: 'short'
                            }).format(value)
                        }
                    }
                },
                animation: {
                    duration: this.animationDuration,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }

    createCustomLegend(containerId, data) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '';
        const total = data.reduce((sum, item) => sum + item.amount, 0);

        data.forEach((item, index) => {
            const legendItem = document.createElement('div');
            legendItem.className = 'modern-legend-item';
            
            const formattedValue = new Intl.NumberFormat('ar-SA', {
                style: 'currency',
                currency: 'SAR',
                minimumFractionDigits: 0
            }).format(item.amount);

            const percentage = ((item.amount / total) * 100).toFixed(1);

            legendItem.innerHTML = `
                <div class="modern-legend-color" style="background: ${item.color[0]}"></div>
                <div class="modern-legend-text">${item.name}</div>
                <div class="modern-legend-value">${formattedValue} (${percentage}%)</div>
            `;

            container.appendChild(legendItem);
        });
    }

    setupEventListeners() {
        // إضافة مستمعي الأحداث للتفاعل
        window.addEventListener('resize', this.handleResize.bind(this));
        
        // تحديث البيانات كل 30 ثانية (للبيانات الحية)
        setInterval(() => {
            this.updateChartsData();
        }, 30000);
    }

    handleResize() {
        Object.values(this.chartInstances).forEach(chart => {
            if (chart) chart.resize();
        });
    }

    updateChartsData() {
        // تحديث البيانات من الخادم (يمكن تخصيصه حسب الحاجة)
        console.log('تحديث بيانات المخططات...');
    }

    // دوال مساعدة للتحكم في المخططات
    showChart(chartName) {
        if (this.chartInstances[chartName]) {
            this.chartInstances[chartName].canvas.parentElement.style.display = 'block';
        }
    }

    hideChart(chartName) {
        if (this.chartInstances[chartName]) {
            this.chartInstances[chartName].canvas.parentElement.style.display = 'none';
        }
    }

    destroyChart(chartName) {
        if (this.chartInstances[chartName]) {
            this.chartInstances[chartName].destroy();
            delete this.chartInstances[chartName];
        }
    }

    exportChart(chartName, format = 'png') {
        if (this.chartInstances[chartName]) {
            const url = this.chartInstances[chartName].toBase64Image();
            const link = document.createElement('a');
            link.download = `chart-${chartName}.${format}`;
            link.href = url;
            link.click();
        }
    }
}

// تهيئة المخططات المحسنة
document.addEventListener('DOMContentLoaded', () => {
    window.modernCharts = new ModernChartsEngine();
});

// إضافة تأثيرات الحركة للعناصر
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-slide-up');
        }
    });
}, observerOptions);

// مراقبة العناصر للحركة
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.modern-chart-container, .modern-stat-card').forEach(el => {
        observer.observe(el);
    });
});