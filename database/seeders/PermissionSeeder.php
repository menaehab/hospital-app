<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'appointment_submit', 'display_name' => 'تسليم الزيارات'], // submit appointments (used by accountant)
            ['name' => 'add_appointments', 'display_name' => 'إضافة زيارات'], // add appointments (used by receptionist)
            ['name' => 'view_appointments', 'display_name' => 'عرض زيارته فقط'], // show appointments that created by himself (used by doctor)
            ['name' => 'manage_appointments', 'display_name' => 'إدارة المواعيد'], // manage appointments (used by admin)
            ['name' => 'manage_visit_types', 'display_name' => 'إدارة أنواع الزيارات'], // manage visit types (used by admin)
            ['name' => 'view_reports', 'display_name' => 'عرض التقارير'], // show reports (used by admin)
            ['name' => 'has_specialties', 'display_name' => 'لديه تخصص'], // show doctors that has specialties (used by doctor)
            ['name' => 'manage_users', 'display_name' => 'إدارة المستخدمين'], // manage users (used by admin)
            ['name' => 'manage_roles', 'display_name' => 'إدارة الصلاحيات'], // manage roles (used by admin)
            ['name' => 'manage_specialties', 'display_name' => 'إدارة التخصصات'], // manage specialties (used by admin)
            ['name' => 'manage_clinics', 'display_name' => 'إدارة العيادات'], // manage clienics (used by admin)
            ['name' => 'manage_patients', 'display_name' => 'إدارة المرضى'], // manage patients (used by admin)
            ['name' => 'view_patients', 'display_name' => 'عرض المرضى'], // view patients (used by doctor)
            ['name' => 'manage_food', 'display_name' => 'إدارة الغذاء'], // manage food (used by admin)
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
            ],[
                'display_name' => $permission['display_name'],
            ]);
        }
    }
}