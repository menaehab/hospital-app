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
            // ['name' => 'appointment_sumbit', 'display_name' => 'تسليم الزيارات'], // submit appointments (used by receptionist)
            ['name' => 'appointment_view_add_by_himself', 'display_name' => 'عرض زيارت التي سجلها فقط'], // show appointments that created by himself (used by receptionist)
            ['name' => 'appointment_view', 'display_name' => 'عرض زيارته فقط'], // show appointments that created by himself (used by doctor)
            ['name' => 'visit_manage', 'display_name' => 'إدارة الزيارات'], // manage appointments (used by admin)
            ['name' => 'reports_view', 'display_name' => 'عرض التقارير'], // show reports (used by admin)
            ['name' => 'doctor_has_specialties', 'display_name' => 'دكتور لديه تخصص'], // show doctors that has specialties (used by doctor)
            ['name' => 'manage_users', 'display_name' => 'إدارة المستخدمين'], // manage users (used by admin)
            ['name' => 'manage_roles', 'display_name' => 'إدارة الصلاحيات'], // manage roles (used by admin)
            ['name' => 'manage_specialties', 'display_name' => 'إدارة التخصصات'], // manage specialties (used by admin)
            ['name' => 'manage_clienics', 'display_name' => 'إدارة العيادات'], // manage clienics (used by admin)
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
            ]);
        }
    }
}