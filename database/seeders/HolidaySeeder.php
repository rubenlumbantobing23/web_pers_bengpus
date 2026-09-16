<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Holiday;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'Tahun Baru 2026 Masehi', 'type' => 'national_holiday'],
            ['date' => '2026-01-16', 'name' => 'Isra Mikraj Nabi Muhammad SAW', 'type' => 'national_holiday'],
            ['date' => '2026-02-17', 'name' => 'Tahun Baru Imlek 2577 Kongzili', 'type' => 'national_holiday'],
            ['date' => '2026-03-19', 'name' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'type' => 'national_holiday'],
            ['date' => '2026-03-20', 'name' => 'Hari Raya Idul Fitri 1447 Hijriah (Hari 1)', 'type' => 'national_holiday'],
            ['date' => '2026-03-21', 'name' => 'Hari Raya Idul Fitri 1447 Hijriah (Hari 2)', 'type' => 'national_holiday'],
            ['date' => '2026-03-23', 'name' => 'Cuti Bersama Idul Fitri 1447 Hijriah', 'type' => 'collective_leave'],
            ['date' => '2026-04-03', 'name' => 'Wafat Yesus Kristus', 'type' => 'national_holiday'],
            ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional', 'type' => 'national_holiday'],
            ['date' => '2026-05-14', 'name' => 'Kenaikan Yesus Kristus', 'type' => 'national_holiday'],
            ['date' => '2026-05-27', 'name' => 'Hari Raya Idul Adha 1447 Hijriah', 'type' => 'national_holiday'],
            ['date' => '2026-05-31', 'name' => 'Hari Raya Waisak 2570 BE', 'type' => 'national_holiday'],
            ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila', 'type' => 'national_holiday'],
            ['date' => '2026-06-16', 'name' => 'Tahun Baru Islam 1448 Hijriah', 'type' => 'national_holiday'],
            ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan Republik Indonesia', 'type' => 'national_holiday'],
            ['date' => '2026-08-25', 'name' => 'Maulid Nabi Muhammad SAW', 'type' => 'national_holiday'],
            ['date' => '2026-12-25', 'name' => 'Hari Raya Natal', 'type' => 'national_holiday'],
        ];

        foreach ($holidays as $h) {
            Holiday::updateOrCreate(
                ['date' => $h['date']],
                [
                    'name' => $h['name'],
                    'type' => $h['type'],
                    'year' => (int) substr($h['date'], 0, 4),
                    'description' => 'Hari libur resmi pemerintah Indonesia',
                ]
            );
        }
    }
}
