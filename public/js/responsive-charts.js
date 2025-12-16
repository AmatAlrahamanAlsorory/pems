// تحسين الرسوم البيانية للجوال والتابلت
document.addEventListener('DOMContentLoaded', function() {
    // إعدادات متجاوبة للرسوم البيانية
    const isMobile = window.innerWidth <= 640;
    const isTablet = window.innerWidth <= 1024;
    
    const responsiveOptions = {
        responsive: true,
        maintainAspectRatio: !isMobile,
        plugins: {
            legend: {
                position: isMobile ? 'bottom' : 'right',
                labels: { 
                    padding: isMobile ? 10 : 15,
                    usePointStyle: true,
                    font: { size: isMobile ? 10 : 12 }
                }
            },
            title: {
                display: true,
                font: { 
                    size: isMobile ? 12 : 14, 
                    weight: 'bold' 
                }
            }
        }
    };
    
    // تحسين حجم الرسوم البيانية للجوال
    if (isMobile) {
        const canvases = document.querySelectorAll('canvas');
        canvases.forEach(canvas => {
            canvas.style.maxHeight = '250px';
            canvas.parentElement.style.height = '250px';
        });
    }
    
    // إعادة رسم الرسوم البيانية عند تغيير حجم الشاشة
    window.addEventListener('resize', function() {
        setTimeout(() => {
            if (window.Chart) {
                Object.values(Chart.instances).forEach(chart => {
                    chart.resize();
                });
            }
        }, 100);
    });
});

// تحسين الجداول للجوال
function makeTablesResponsive() {
    const tables = document.querySelectorAll('table:not(.table-responsive table)');
    tables.forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}

// تطبيق التحسينات عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', makeTablesResponsive);