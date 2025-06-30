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
            ['name' => 'visit_create', 'display_name' => 'إنشاء زيارة'],
            ['name' => 'visit_view', 'display_name' => 'عرض زياراته فقط'],
            ['name' => 'visit_sumbit', 'display_name' => 'تسليم الزيارات'],
            ['name' => 'visit_view_assigned', 'display_name' => 'عرض الزيارات الموجهة له'],
            ['name' => 'visit_view_all', 'display_name' => 'عرض جميع الزيارات'],
            ['name' => 'visit_manage', 'display_name' => 'إدارة الزيارات'],
            ['name' => 'reports_view', 'display_name' => 'عرض التقارير'],
            ['name' => 'doctor_has_specialties', 'display_name' => 'لديه التخصصات'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
            ]);
        }
    }
}
