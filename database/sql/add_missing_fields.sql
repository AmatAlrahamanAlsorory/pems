-- إضافة الحقول المفقودة لجميع الجداول

-- جدول المستخدمين
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS permissions JSON;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT true;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP;
ALTER TABLE users ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP;

-- جدول المواقع
ALTER TABLE locations ADD COLUMN IF NOT EXISTS city VARCHAR(255);
ALTER TABLE locations ADD COLUMN IF NOT EXISTS address TEXT;
ALTER TABLE locations ADD COLUMN IF NOT EXISTS latitude DECIMAL(10,8);
ALTER TABLE locations ADD COLUMN IF NOT EXISTS longitude DECIMAL(11,8);
ALTER TABLE locations ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active';
ALTER TABLE locations ADD COLUMN IF NOT EXISTS project_id BIGINT;

-- جدول العهد
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS custody_number VARCHAR(255);
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS user_id BIGINT;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS purpose TEXT;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS issued_date DATE;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS due_date DATE;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS returned_amount DECIMAL(15,2) DEFAULT 0;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS remaining_amount DECIMAL(15,2) DEFAULT 0;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS location VARCHAR(255);
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS notes TEXT;
ALTER TABLE custodies ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'pending';

-- جدول الإشعارات
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS title VARCHAR(255);
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS message TEXT;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS type VARCHAR(50) DEFAULT 'info';
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS is_read BOOLEAN DEFAULT false;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS data JSON;

-- إنشاء جدول سجل التدقيق
CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT,
    action VARCHAR(255) NOT NULL,
    model_type VARCHAR(255),
    model_id BIGINT,
    old_values JSON,
    new_values JSON,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- إنشاء الفهارس
CREATE INDEX IF NOT EXISTS idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_model ON audit_logs(model_type, model_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_action ON audit_logs(action);