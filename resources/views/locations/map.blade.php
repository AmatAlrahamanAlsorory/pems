<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            خريطة مواقع التصوير
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- أدوات التحكم -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <select id="project-filter" class="form-select">
                        <option value="">جميع المشاريع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    
                    <select id="status-filter" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشط</option>
                        <option value="completed">مكتمل</option>
                        <option value="planned">مخطط</option>
                    </select>
                    
                    <button id="add-location-btn" class="btn btn-primary">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إضافة موقع جديد
                    </button>
                    
                    <button id="current-location-btn" class="btn btn-secondary">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        موقعي الحالي
                    </button>
                    
                    <button id="save-current-location-btn" class="btn btn-success">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        حفظ الموقع الحالي
                    </button>
                </div>
            </div>

            <!-- الخريطة -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div id="map" style="height: 600px;"></div>
            </div>

            <!-- معلومات الموقع المحدد -->
            <div id="location-info" class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="location-name"></h3>
                    <button id="close-info" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">معلومات أساسية</h4>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-gray-600">المشروع:</span> <span id="location-project"></span></div>
                            <div><span class="text-gray-600">الحالة:</span> <span id="location-status"></span></div>
                            <div><span class="text-gray-600">العنوان:</span> <span id="location-address"></span></div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">الميزانية</h4>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-gray-600">المخصص:</span> <span id="location-budget"></span></div>
                            <div><span class="text-gray-600">المصروف:</span> <span id="location-spent"></span></div>
                            <div><span class="text-gray-600">المتبقي:</span> <span id="location-remaining"></span></div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">إحصائيات</h4>
                        <div class="space-y-2 text-sm">
                            <div><span class="text-gray-600">عدد المصروفات:</span> <span id="location-expenses-count"></span></div>
                            <div><span class="text-gray-600">آخر نشاط:</span> <span id="location-last-activity"></span></div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex gap-2">
                    <button id="view-expenses-btn" class="btn btn-primary btn-sm">عرض المصروفات</button>
                    <button id="edit-location-btn" class="btn btn-secondary btn-sm">تعديل الموقع</button>
                    <button id="navigate-btn" class="btn btn-outline btn-sm">التنقل للموقع</button>
                </div>
            </div>

        </div>
    </div>

    <!-- نافذة إضافة موقع جديد -->
    <div id="add-location-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center" style="z-index: 9999;">
        <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">إضافة موقع جديد</h3>
            
            <form id="add-location-form">
                <div class="space-y-4">
                    <div>
                        <label class="form-label">اسم الموقع *</label>
                        <input type="text" id="new-location-name" class="form-input" required placeholder="مثال: استوديو التصوير الرئيسي">
                    </div>
                    
                    <div>
                        <label class="form-label">المشروع *</label>
                        <select id="new-location-project" class="form-select" required>
                            <option value="">اختر المشروع</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label">العنوان</label>
                        <input type="text" id="new-location-address" class="form-input" placeholder="سيتم تعبئته تلقائياً عند استخدام الموقع الحالي">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">خط الطول</label>
                            <input type="number" step="any" id="new-location-lng" class="form-input" readonly>
                        </div>
                        <div>
                            <label class="form-label">خط العرض</label>
                            <input type="number" step="any" id="new-location-lat" class="form-input" readonly>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <p class="text-sm text-green-800 font-medium">تم تحديد الموقع على الخريطة ✓</p>
                        <p class="text-xs text-green-600">يمكنك تعديل الإحداثيات يدوياً إذا لزم الأمر</p>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="btn btn-primary">حفظ الموقع</button>
                    <button type="button" id="cancel-add-location" class="btn btn-secondary">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        #add-location-modal {
            z-index: 9999 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        #add-location-modal > div {
            position: relative;
            z-index: 10000;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
    
    <script>
        let map;
        let markers = [];
        let locations = @json($locations);
        let isAddingLocation = false;
        let tempMarker = null;

        // تهيئة الخريطة
        function initMap() {
            map = L.map('map').setView([24.7136, 46.6753], 6); // الرياض كنقطة مركزية

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // إضافة المواقع الموجودة
            addLocationsToMap();
            
            // معالج النقر على الخريطة
            map.on('click', function(e) {
                console.log('تم النقر على الخريطة:', e.latlng);
                
                if (isAddingLocation) {
                    addTempMarker(e.latlng);
                }
            });
        }

        function addLocationsToMap() {
            locations.forEach(location => {
                if (location.latitude && location.longitude) {
                    addLocationMarker(location);
                }
            });
        }

        function addLocationMarker(location) {
            const statusColors = {
                'active': '#10b981',
                'completed': '#6b7280',
                'planned': '#f59e0b'
            };

            const marker = L.circleMarker([location.latitude, location.longitude], {
                radius: 8,
                fillColor: statusColors[location.status] || '#3b82f6',
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            marker.bindPopup(`
                <div class="p-2">
                    <h4 class="font-semibold">${location.name}</h4>
                    <p class="text-sm text-gray-600">${location.project?.name || 'غير محدد'}</p>
                    <p class="text-xs text-gray-500">${location.address || 'لا يوجد عنوان'}</p>
                </div>
            `);

            marker.on('click', function() {
                showLocationInfo(location);
            });

            markers.push({ marker, location });
        }

        function addTempMarker(latlng) {
            console.log('إضافة علامة مؤقتة:', latlng);
            
            // إزالة العلامة القديمة
            if (tempMarker) {
                map.removeLayer(tempMarker);
            }
            
            // إزالة رسالة التعليمات
            const instruction = document.getElementById('map-instruction');
            if (instruction) {
                document.body.removeChild(instruction);
            }

            // إضافة علامة جديدة
            tempMarker = L.marker(latlng, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);

            // تعبئة الإحداثيات
            document.getElementById('new-location-lat').value = latlng.lat.toFixed(6);
            document.getElementById('new-location-lng').value = latlng.lng.toFixed(6);
            
            // فتح النافذة
            document.getElementById('add-location-modal').classList.remove('hidden');
            
            // إعادة المؤشر للحالة العادية
            map.getContainer().style.cursor = '';
        }

        function showLocationInfo(location) {
            document.getElementById('location-name').textContent = location.name;
            document.getElementById('location-project').textContent = location.project?.name || 'غير محدد';
            document.getElementById('location-status').textContent = getStatusText(location.status);
            document.getElementById('location-address').textContent = location.address || 'غير محدد';
            document.getElementById('location-budget').textContent = formatCurrency(location.budget || 0);
            document.getElementById('location-spent').textContent = formatCurrency(location.spent_amount || 0);
            document.getElementById('location-remaining').textContent = formatCurrency((location.budget || 0) - (location.spent_amount || 0));
            document.getElementById('location-expenses-count').textContent = location.expenses_count || 0;
            document.getElementById('location-last-activity').textContent = location.last_activity || 'لا يوجد';

            document.getElementById('location-info').classList.remove('hidden');
        }

        function getStatusText(status) {
            const statusTexts = {
                'active': 'نشط',
                'completed': 'مكتمل',
                'planned': 'مخطط'
            };
            return statusTexts[status] || status;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('ar-SA', {
                style: 'currency',
                currency: 'SAR'
            }).format(amount);
        }

        function filterLocations() {
            const projectFilter = document.getElementById('project-filter').value;
            const statusFilter = document.getElementById('status-filter').value;

            markers.forEach(({ marker, location }) => {
                let show = true;

                if (projectFilter && location.project_id != projectFilter) {
                    show = false;
                }

                if (statusFilter && location.status !== statusFilter) {
                    show = false;
                }

                if (show) {
                    marker.addTo(map);
                } else {
                    map.removeLayer(marker);
                }
            });
        }

        // معالجات الأحداث
        document.getElementById('project-filter').addEventListener('change', filterLocations);
        document.getElementById('status-filter').addEventListener('change', filterLocations);

        document.getElementById('add-location-btn').addEventListener('click', function() {
            console.log('تم الضغط على زر إضافة موقع');
            
            isAddingLocation = true;
            map.getContainer().style.cursor = 'crosshair';
            
            // مسح الحقول
            document.getElementById('new-location-name').value = '';
            document.getElementById('new-location-project').value = '';
            document.getElementById('new-location-address').value = '';
            document.getElementById('new-location-lat').value = '';
            document.getElementById('new-location-lng').value = '';
            
            // إظهار رسالة توضيحية
            const instruction = document.createElement('div');
            instruction.id = 'map-instruction';
            instruction.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #4CAF50; color: white; padding: 15px 25px; border-radius: 8px; z-index: 10000; font-size: 16px; box-shadow: 0 4px 8px rgba(0,0,0,0.3);';
            instruction.textContent = 'انقر على الخريطة لتحديد الموقع';
            document.body.appendChild(instruction);
            
            // إخفاء الرسالة بعد 3 ثوان
            setTimeout(() => {
                if (document.getElementById('map-instruction')) {
                    document.body.removeChild(instruction);
                }
            }, 3000);
        });

        document.getElementById('cancel-add-location').addEventListener('click', function() {
            console.log('تم إلغاء إضافة الموقع');
            
            isAddingLocation = false;
            document.getElementById('add-location-modal').classList.add('hidden');
            map.getContainer().style.cursor = '';
            
            if (tempMarker) {
                map.removeLayer(tempMarker);
                tempMarker = null;
            }
            
            // إزالة رسالة التعليمات إن وجدت
            const instruction = document.getElementById('map-instruction');
            if (instruction) {
                document.body.removeChild(instruction);
            }
        });

        document.getElementById('current-location-btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                const btn = this;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4 ml-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>جاري تحديد الموقع...';
                btn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    
                    map.setView([lat, lng], 18);
                    
                    const marker = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map);
                    
                    // إضافة دائرة توضح دقة الموقع
                    L.circle([lat, lng], {
                        radius: accuracy,
                        color: '#0066ff',
                        fillColor: '#0066ff',
                        fillOpacity: 0.1,
                        weight: 2
                    }).addTo(map);
                    
                    marker.bindPopup(`موقعك الحالي<br>دقة: ${Math.round(accuracy)} متر`).openPopup();
                    
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, function(error) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('لا يمكن الحصول على موقعك. تأكد من السماح بالوصول للموقع.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                });
            }
        });

        document.getElementById('save-current-location-btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    document.getElementById('new-location-lat').value = lat.toFixed(6);
                    document.getElementById('new-location-lng').value = lng.toFixed(6);
                    document.getElementById('add-location-modal').classList.remove('hidden');
                    
                    if (tempMarker) {
                        map.removeLayer(tempMarker);
                    }
                    
                    tempMarker = L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    }).addTo(map);
                    
                    map.setView([lat, lng], 15);
                    
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=ar`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.display_name) {
                                document.getElementById('new-location-address').value = data.display_name;
                            }
                        })
                        .catch(error => console.log('لا يمكن الحصول على العنوان:', error));
                        
                }, function(error) {
                    alert('لا يمكن الحصول على موقعك. تأكد من السماح بالوصول للموقع.');
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                });
            } else {
                alert('المتصفح لا يدعم خدمة تحديد الموقع.');
            }
        });

        document.getElementById('close-info').addEventListener('click', function() {
            document.getElementById('location-info').classList.add('hidden');
        });

        document.getElementById('add-location-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // التحقق من الحقول المطلوبة
            const name = document.getElementById('new-location-name').value;
            const projectId = document.getElementById('new-location-project').value;
            const lat = document.getElementById('new-location-lat').value;
            const lng = document.getElementById('new-location-lng').value;
            
            if (!name || !projectId || !lat || !lng) {
                alert('يرجى ملء جميع الحقول المطلوبة');
                return;
            }
            
            const formData = {
                name: name,
                project_id: projectId,
                address: document.getElementById('new-location-address').value,
                latitude: lat,
                longitude: lng,
                budget_allocated: 0
            };

            try {
                const response = await fetch('/api/locations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    const newLocation = await response.json();
                    locations.push(newLocation);
                    
                    // إضافة الموقع للخريطة
                    const locationData = {
                        id: newLocation.id,
                        name: newLocation.name,
                        latitude: parseFloat(newLocation.latitude),
                        longitude: parseFloat(newLocation.longitude),
                        address: newLocation.address,
                        status: 'active',
                        project_id: newLocation.project_id,
                        project: newLocation.project,
                        budget: 0,
                        spent_amount: 0,
                        expenses_count: 0
                    };
                    
                    addLocationMarker(locationData);
                    
                    // إغلاق النافذة وإعادة تعيين النموذج
                    document.getElementById('add-location-modal').classList.add('hidden');
                    document.getElementById('add-location-form').reset();
                    isAddingLocation = false;
                    map.getContainer().style.cursor = '';
                    
                    if (tempMarker) {
                        map.removeLayer(tempMarker);
                        tempMarker = null;
                    }
                    
                    alert('تم إضافة الموقع بنجاح');
                } else {
                    const errorData = await response.json();
                    alert('حدث خطأ في إضافة الموقع: ' + (errorData.message || 'خطأ غير معروف'));
                }
            } catch (error) {
                console.error('خطأ:', error);
                alert('حدث خطأ في الاتصال');
            }
        });


        // تهيئة الخريطة عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</x-app-layout>