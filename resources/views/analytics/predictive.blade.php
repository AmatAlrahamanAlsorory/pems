<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            التحليلات التنبؤية - {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- ملخص التنبؤات -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-500">احتمالية تجاوز الميزانية</div>
                                <div class="text-2xl font-bold text-gray-900" id="overrun-probability">--</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-500">المصروفات المتوقعة (4 أسابيع)</div>
                                <div class="text-2xl font-bold text-gray-900" id="predicted-spending">--</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="mr-4">
                                <div class="text-sm font-medium text-gray-500">كفاءة الإنفاق الإجمالية</div>
                                <div class="text-2xl font-bold text-gray-900" id="spending-efficiency">--</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الرسوم البيانية -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- توقعات الإنفاق الأسبوعية -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">توقعات الإنفاق الأسبوعية</h3>
                        <canvas id="weeklyPredictionChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- تحليل كفاءة الإنفاق -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">كفاءة الإنفاق حسب الفئة</h3>
                        <canvas id="efficiencyChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- التوصيات والإجراءات -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- التوصيات -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">التوصيات المقترحة</h3>
                        <div id="recommendations" class="space-y-3">
                            <!-- سيتم ملؤها بـ JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- التواريخ الحرجة -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">التواريخ الحرجة للسيولة</h3>
                        <div id="critical-dates" class="space-y-3">
                            <!-- سيتم ملؤها بـ JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const projectId = {{ $project->id }};
        
        // تحميل البيانات التنبؤية
        async function loadPredictiveData() {
            try {
                const response = await fetch(`/api/analytics/${projectId}/dashboard`);
                const data = await response.json();
                
                updateSummaryCards(data);
                createWeeklyPredictionChart(data.weekly_prediction);
                createEfficiencyChart(data.efficiency_analysis);
                displayRecommendations(data.budget_prediction.recommended_actions);
                displayCriticalDates(data.cash_flow.critical_dates);
                
            } catch (error) {
                console.error('خطأ في تحميل البيانات التنبؤية:', error);
                // عرض بيانات تجريبية في حالة الخطأ
                loadDemoData();
            }
        }
        
        // بيانات حقيقية من قاعدة البيانات
        function loadRealData() {
            const analyticsData = @json($analyticsData);
            
            // حساب احتمالية تجاوز الميزانية
            const budgetUsed = analyticsData.project_stats.budget_used_percentage;
            const overrunProbability = budgetUsed > 90 ? 95 : budgetUsed > 80 ? 75 : budgetUsed > 70 ? 50 : 25;
            
            // توقعات الإنفاق الأسبوعية بناءً على البيانات الحقيقية
            const weeksActive = Math.max(1, Math.floor((Date.now() - new Date('2024-01-01')) / (7 * 24 * 60 * 60 * 1000)));
            const avgWeeklySpending = analyticsData.project_stats.total_spent / weeksActive;
            
            // تعديل التوقعات بناءً على الاتجاه
            const trendFactor = budgetUsed > 80 ? 0.7 : budgetUsed > 60 ? 0.9 : 1.1;
            const weeklyPredictions = [
                { week: 1, predicted_amount: Math.round(avgWeeklySpending * trendFactor * 0.95) },
                { week: 2, predicted_amount: Math.round(avgWeeklySpending * trendFactor * 1.05) },
                { week: 3, predicted_amount: Math.round(avgWeeklySpending * trendFactor * 1.0) },
                { week: 4, predicted_amount: Math.round(avgWeeklySpending * trendFactor * 0.9) }
            ];
            
            // تحليل كفاءة الإنفاق بناءً على البيانات الحقيقية
            const totalCategorySpending = analyticsData.category_expenses.reduce((sum, cat) => sum + cat.amount, 0);
            const categoryAnalysis = analyticsData.category_expenses.map(cat => {
                const categoryPercentage = totalCategorySpending > 0 ? (cat.amount / totalCategorySpending) * 100 : 0;
                // حساب الكفاءة بناءً على نسبة الإنفاق وعدد المعاملات
                const avgPerTransaction = cat.count > 0 ? cat.amount / cat.count : 0;
                const efficiency = avgPerTransaction > 50000 ? 0.7 : avgPerTransaction > 20000 ? 0.85 : 0.95;
                
                return {
                    category: cat.category,
                    efficiency_ratio: efficiency + (Math.random() * 0.2 - 0.1) // تباين طفيف
                };
            });
            
            // التوصيات بناءً على حالة المشروع
            let recommendations = [];
            if (budgetUsed > 90) {
                recommendations = [
                    'ضرورة إيقاف المصروفات غير الضرورية',
                    'مراجعة عاجلة للميزانية',
                    'طلب ميزانية إضافية'
                ];
            } else if (budgetUsed > 70) {
                recommendations = [
                    'مراقبة دقيقة للمصروفات',
                    'تقليل المصروفات الاختيارية',
                    'تحسين عملية الشراء'
                ];
            } else {
                recommendations = [
                    'المشروع يسير بشكل جيد',
                    'مواصلة المراقبة الدورية',
                    'استغلال الفائض في تحسين الجودة'
                ];
            }
            
            // التواريخ الحرجة بناءً على معدل الإنفاق
            const criticalDates = [];
            const remainingBudget = analyticsData.project_stats.remaining_budget;
            const weeklySpending = avgWeeklySpending;
            
            // حساب عدد الأسابيع المتبقية
            const weeksRemaining = weeklySpending > 0 ? Math.floor(remainingBudget / weeklySpending) : 999;
            
            if (weeksRemaining <= 2) {
                criticalDates.push({
                    date: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    cash_need_urgency: 'عاجل جداً',
                    remaining_budget: Math.max(0, remainingBudget - weeklySpending * 0.5)
                });
            } else if (weeksRemaining <= 4) {
                criticalDates.push({
                    date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    cash_need_urgency: 'عاجل',
                    remaining_budget: Math.max(0, remainingBudget - weeklySpending)
                });
            } else if (weeksRemaining <= 8) {
                criticalDates.push({
                    date: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    cash_need_urgency: 'مهم',
                    remaining_budget: Math.max(0, remainingBudget - weeklySpending * 2)
                });
                criticalDates.push({
                    date: new Date(Date.now() + 28 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    cash_need_urgency: 'تحذير',
                    remaining_budget: Math.max(0, remainingBudget - weeklySpending * 4)
                });
            }
            
            // إضافة تواريخ مهمة للمشاريع النشطة
            if (budgetUsed > 50 && criticalDates.length === 0) {
                criticalDates.push({
                    date: new Date(Date.now() + 21 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    cash_need_urgency: 'مراجعة دورية',
                    remaining_budget: remainingBudget
                });
            }
            
            const realData = {
                budget_prediction: {
                    overrun_probability: overrunProbability,
                    recommended_actions: recommendations
                },
                weekly_prediction: {
                    total_predicted: weeklyPredictions.reduce((sum, w) => sum + w.predicted_amount, 0),
                    predictions: weeklyPredictions
                },
                efficiency_analysis: {
                    overall_efficiency: budgetUsed > 100 ? 0.6 : budgetUsed > 80 ? 0.8 : 0.9,
                    category_analysis: categoryAnalysis
                },
                cash_flow: {
                    critical_dates: criticalDates
                }
            };
            
            updateSummaryCards(realData);
            createWeeklyPredictionChart(realData.weekly_prediction);
            createEfficiencyChart(realData.efficiency_analysis);
            displayRecommendations(realData.budget_prediction.recommended_actions);
            displayCriticalDates(realData.cash_flow.critical_dates);
        }
        
        function updateSummaryCards(data) {
            document.getElementById('overrun-probability').textContent = 
                Math.round(data.budget_prediction.overrun_probability) + '%';
            
            document.getElementById('predicted-spending').textContent = 
                new Intl.NumberFormat('ar-SA').format(data.weekly_prediction.total_predicted) + ' ر.س';
            
            document.getElementById('spending-efficiency').textContent = 
                Math.round(data.efficiency_analysis.overall_efficiency * 100) + '%';
        }
        
        function createWeeklyPredictionChart(weeklyData) {
            const ctx = document.getElementById('weeklyPredictionChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: weeklyData.predictions.map(p => `الأسبوع ${p.week}`),
                    datasets: [{
                        label: 'المصروفات المتوقعة',
                        data: weeklyData.predictions.map(p => p.predicted_amount),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('ar-SA').format(value) + ' ر.س';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function createEfficiencyChart(efficiencyData) {
            const ctx = document.getElementById('efficiencyChart').getContext('2d');
            
            const categories = efficiencyData.category_analysis.map(c => c.category);
            const efficiencies = efficiencyData.category_analysis.map(c => c.efficiency_ratio * 100);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'كفاءة الإنفاق (%)',
                        data: efficiencies,
                        backgroundColor: efficiencies.map(e => 
                            e <= 80 ? 'rgba(34, 197, 94, 0.8)' :
                            e <= 100 ? 'rgba(59, 130, 246, 0.8)' :
                            'rgba(239, 68, 68, 0.8)'
                        )
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 150,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function displayRecommendations(recommendations) {
            const container = document.getElementById('recommendations');
            container.innerHTML = '';
            
            recommendations.forEach(rec => {
                const div = document.createElement('div');
                div.className = 'flex items-start p-3 bg-yellow-50 rounded-lg border-r-4 border-yellow-400';
                div.innerHTML = `
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm text-gray-700">${rec}</span>
                `;
                container.appendChild(div);
            });
        }
        
        function displayCriticalDates(criticalDates) {
            const container = document.getElementById('critical-dates');
            container.innerHTML = '';
            
            if (criticalDates.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-green-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-600 font-medium">وضع مالي مستقر</p>
                        <p class="text-gray-500 text-sm mt-1">لا توجد تواريخ حرجة متوقعة</p>
                    </div>
                `;
                return;
            }
            
            criticalDates.slice(0, 5).forEach(date => {
                const div = document.createElement('div');
                const urgencyColors = {
                    'عاجل جداً': 'bg-red-100 border-red-500 text-red-800',
                    'عاجل': 'bg-red-50 border-red-400 text-red-700',
                    'مهم': 'bg-orange-50 border-orange-400 text-orange-700',
                    'تحذير': 'bg-yellow-50 border-yellow-400 text-yellow-700',
                    'مراجعة دورية': 'bg-blue-50 border-blue-400 text-blue-700'
                };
                
                div.className = `flex items-center justify-between p-3 rounded-lg border-r-4 ${urgencyColors[date.cash_need_urgency] || 'bg-gray-50 border-gray-400 text-gray-700'}`;
                div.innerHTML = `
                    <div>
                        <div class="font-medium text-gray-900">${date.date}</div>
                        <div class="text-sm text-gray-600">حالة السيولة: ${date.cash_need_urgency}</div>
                    </div>
                    <div class="text-sm font-medium text-red-600">
                        ${new Intl.NumberFormat('ar-SA').format(date.remaining_budget)} ر.س
                    </div>
                `;
                container.appendChild(div);
            });
        }
        
        // تحميل البيانات عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', function() {
            // استخدام البيانات الحقيقية من قاعدة البيانات
            loadRealData();
        });
    </script>
</x-app-layout>