-- بيانات تجريبية لنظام إدارة مصروفات الإنتاج الفني

-- إدراج المشاريع
INSERT INTO projects (name, description, total_budget, spent_amount, start_date, end_date, status, created_at, updated_at) VALUES
('مسلسل الأحلام الذهبية - رمضان 2025', 'مسلسل درامي اجتماعي من 30 حلقة يتناول قضايا المجتمع المعاصر', 15000000, 8500000, '2024-09-15', '2026-02-15', 'active', NOW(), NOW()),
('برنامج صباح الخير يا عرب', 'برنامج صباحي يومي يقدم الأخبار والترفيه', 5000000, 3200000, '2024-06-15', '2025-06-15', 'active', NOW(), NOW()),
('فيلم الرحلة الأخيرة', 'فيلم سينمائي درامي يحكي قصة عائلة في زمن الحرب', 8000000, 7800000, '2024-04-15', '2024-11-15', 'completed', NOW(), NOW()),
('مسلسل حكايات الأطفال', 'مسلسل تعليمي للأطفال من 20 حلقة', 3000000, 1200000, '2025-01-15', '2025-04-15', 'planned', NOW(), NOW());

-- إدراج المواقع
INSERT INTO locations (project_id, name, address, latitude, longitude, budget_allocated, created_at, updated_at) VALUES
(1, 'استوديو الرياض الرئيسي', 'حي الملز، الرياض، المملكة العربية السعودية', 24.7136, 46.6753, 2000000, NOW(), NOW()),
(1, 'موقع تصوير خارجي - جدة', 'كورنيش جدة، جدة، المملكة العربية السعودية', 21.4858, 39.1925, 1500000, NOW(), NOW()),
(2, 'استوديو البرامج الصباحية', 'حي العليا، الرياض، المملكة العربية السعودية', 24.6877, 46.7219, 800000, NOW(), NOW()),
(3, 'موقع تصوير الصحراء', 'صحراء الربع الخالي، المملكة العربية السعودية', 23.7000, 46.7500, 1200000, NOW(), NOW());

-- إدراج الأشخاص
INSERT INTO people (name, role, phone, email, national_id, created_at, updated_at) VALUES
('أحمد محمد العلي', 'مخرج', '0501234567', 'ahmed.ali@example.com', '1234567890', NOW(), NOW()),
('فاطمة سعد الغامدي', 'ممثلة رئيسية', '0509876543', 'fatima.ghamdi@example.com', '0987654321', NOW(), NOW()),
('محمد عبدالله النجار', 'مدير التصوير', '0555555555', 'mohammed.najjar@example.com', '5555555555', NOW(), NOW()),
('نورا خالد الشمري', 'مساعد مخرج', '0544444444', 'nora.shamri@example.com', '4444444444', NOW(), NOW());

-- إدراج العهد
INSERT INTO custodies (project_id, requested_by, amount, purpose, status, approved_by, approved_at, created_at, updated_at) VALUES
(1, 1, 500000, 'مصروفات تصوير الأسبوع الأول', 'active', 1, NOW() - INTERVAL '5 days', NOW() - INTERVAL '7 days', NOW()),
(2, 1, 200000, 'مصروفات الإنتاج الشهرية', 'settled', 1, NOW() - INTERVAL '15 days', NOW() - INTERVAL '20 days', NOW()),
(1, 1, 300000, 'مصروفات المعدات والديكور', 'requested', NULL, NULL, NOW() - INTERVAL '1 day', NOW());

-- إدراج المصروفات
INSERT INTO expenses (project_id, custody_id, category_id, item_id, amount, description, expense_date, location_id, person_id, status, created_at, updated_at) VALUES
(1, 1, 1, 1, 150000, 'إيجار كاميرات احترافية لمدة أسبوع', NOW() - INTERVAL '5 days', 1, 1, 'approved', NOW() - INTERVAL '5 days', NOW()),
(1, 1, 2, 5, 25000, 'مواد تجميل وأزياء للممثلين', NOW() - INTERVAL '4 days', 1, 2, 'approved', NOW() - INTERVAL '4 days', NOW()),
(1, 1, 3, 9, 80000, 'خدمات الإضاءة والصوت', NOW() - INTERVAL '3 days', 1, 3, 'approved', NOW() - INTERVAL '3 days', NOW()),
(1, 1, 4, 13, 45000, 'نقل الفريق والمعدات للموقع', NOW() - INTERVAL '2 days', 2, 4, 'approved', NOW() - INTERVAL '2 days', NOW()),
(2, 2, 1, 2, 75000, 'صيانة معدات الاستوديو', NOW() - INTERVAL '10 days', 3, 1, 'approved', NOW() - INTERVAL '10 days', NOW()),
(2, 2, 5, 17, 15000, 'ضيافة الضيوف والفريق', NOW() - INTERVAL '8 days', 3, 2, 'approved', NOW() - INTERVAL '8 days', NOW()),
(1, NULL, 1, 3, 120000, 'شراء معدات إضاءة جديدة', NOW() - INTERVAL '1 day', 1, 3, 'pending', NOW() - INTERVAL '1 day', NOW()),
(2, NULL, 2, 6, 35000, 'مواد ديكور للاستوديو', NOW(), 3, 4, 'pending', NOW(), NOW());

-- إدراج الإشعارات
INSERT INTO notifications (user_id, title, message, type, level, is_read, created_at, updated_at) VALUES
(1, 'تجاوز في الميزانية', 'مشروع "فيلم الرحلة الأخيرة" تجاوز 95% من الميزانية المخصصة', 'budget_alert', 'critical', false, NOW() - INTERVAL '2 hours', NOW()),
(1, 'عهدة جديدة تحتاج موافقة', 'طلب عهدة بمبلغ 300,000 ر.س لمشروع "مسلسل الأحلام الذهبية"', 'custody_approval', 'warning', false, NOW() - INTERVAL '5 hours', NOW()),
(1, 'مصروف يحتاج موافقة', 'مصروف بمبلغ 120,000 ر.س لشراء معدات إضاءة', 'expense_approval', 'info', false, NOW() - INTERVAL '8 hours', NOW()),
(1, 'تم اعتماد المصروف', 'تم اعتماد مصروف خدمات الإضاءة والصوت بمبلغ 80,000 ر.س', 'expense_approved', 'success', true, NOW() - INTERVAL '1 day', NOW());

-- تحديث المبالغ المصروفة في المشاريع
UPDATE projects SET spent_amount = (
    SELECT COALESCE(SUM(amount), 0) 
    FROM expenses 
    WHERE expenses.project_id = projects.id AND expenses.status = 'approved'
) WHERE id IN (1, 2, 3, 4);