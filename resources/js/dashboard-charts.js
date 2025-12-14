// Dashboard Charts
class DashboardCharts {
    constructor() {
        this.charts = {};
        this.init();
    }
    
    async init() {
        await this.loadChartJS();
        this.loadDashboardData();
        this.setupRealTimeUpdates();
    }
    
    setupRealTimeUpdates() {
        // تحديث البيانات كل 5 دقائق
        setInterval(() => {
            this.loadDashboardData();
        }, 300000);
    }
    
    async loadChartJS() {
        if (typeof Chart === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            document.head.appendChild(script);
            
            return new Promise(resolve => {
                script.onload = resolve;
            });
        }
    }
    
    async loadDashboardData() {
        try {
            const response = await fetch('/reports/dashboard-data');
            const data = await response.json();
            
            this.createBudgetChart(data.project_status);
            this.createCategoryChart(data.category_spending);
            this.createMonthlyChart(data.monthly_expenses);
        } catch (error) {
            console.error('Error loading dashboard data:', error);
        }
    }
    
    createBudgetChart(data) {
        const ctx = document.getElementById('budgetChart');
        if (!ctx) return;
        
        if (this.charts.budget) {
            this.charts.budget.destroy();
        }
        
        this.charts.budget = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['آمن', 'تحذير', 'خطر'],
                datasets: [{
                    data: [data.normal, data.warning, data.critical],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: true,
                        text: 'حالة ميزانيات المشاريع',
                        font: { size: 16, weight: 'bold' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (مشروع ${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
    
    createCategoryChart(data) {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;
        
        const sortedData = data.sort((a, b) => b.amount - a.amount).slice(0, 8);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sortedData.map(item => item.name),
                datasets: [{
                    label: 'المبلغ المصروف',
                    data: sortedData.map(item => item.amount),
                    backgroundColor: sortedData.map(item => item.color || '#3b82f6'),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'المصروفات حسب الفئات'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('ar-SA').format(value);
                            }
                        }
                    }
                }
            }
        });
    }
    
    createMonthlyChart(data) {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;
        
        const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                       'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        
        const chartData = months.map((month, index) => data[index + 1] || 0);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'المصروفات الشهرية',
                    data: chartData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'اتجاه المصروفات الشهرية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('ar-SA').format(value);
                            }
                        }
                    }
                }
            }
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardCharts();
});