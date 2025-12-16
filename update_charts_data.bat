@echo off
echo إضافة بيانات المخططات البيانية فقط...
php artisan db:seed --class=ChartsDataSeeder
echo تم إضافة البيانات بنجاح!
pause