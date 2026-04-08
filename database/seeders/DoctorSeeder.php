<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $doctors = [
            [
                'name_en' => 'Dr. Sarah Adel',
                'name_ar' => '?. ???? ????',
                'specialization_en' => 'Dentistry',
                'specialization_ar' => '?? ???????',
                'bio_en' => 'General dentist with preventive care focus.',
                'bio_ar' => '????? ????? ???? ???? ??? ??????? ????????.',
                'image' => '/assets/img/health/staff-1.webp',
            ],
            [
                'name_en' => 'Dr. Omar Hassan',
                'name_ar' => '?. ??? ???',
                'specialization_en' => 'Cardiology',
                'specialization_ar' => '????? ?????',
                'bio_en' => 'Cardiology specialist with outpatient clinic experience.',
                'bio_ar' => '?????? ??? ???? ???? ?? ???????? ????????.',
                'image' => '/assets/img/health/staff-3.webp',
            ],
            [
                'name_en' => 'Dr. Lina Kamal',
                'name_ar' => '?. ???? ????',
                'specialization_en' => 'Dermatology',
                'specialization_ar' => '??????? ???????',
                'bio_en' => 'Dermatologist focused on common skin conditions.',
                'bio_ar' => '????? ????? ?????? ?? ??????? ??????? ???????.',
                'image' => '/assets/img/health/staff-5.webp',
            ],
        ];

        foreach ($doctors as $doctor) {
            Doctor::query()->updateOrCreate(
                ['name_en' => $doctor['name_en']],
                $doctor,
            );
        }
    }
}
