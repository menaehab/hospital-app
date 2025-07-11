<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\VisitType;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinic = Clinic::create([
            'name' => 'clinic',
        ]);
        $admin = User::where('id',1)->update([
            'clinic_id' => 1,
        ]);
        $visitType = VisitType::create([
            'service_type' => 'visitType',
            'price' => 100,
            'doctor_fee_type' => 'fixed',
            'doctor_fee_value' => 100,
            'doctor_id' => 1,
        ]);

        $patient = Patient::create([
            'name' => 'patient',
            'age' => 10,
            'phone' => '123456789',
            'address' => 'patient',
        ]);
    }
}
