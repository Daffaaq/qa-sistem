<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DataClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create(); // Menggunakan Faker untuk menghasilkan data dummy

        $tahun = 2025;  // Tentukan tahun yang diinginkan

        // Loop untuk setiap bulan dari Januari (bulan 1) hingga Oktober (bulan 10)
        for ($bulan = 1; $bulan <= 10; $bulan++) {
            // Menentukan jumlah hari dalam bulan tersebut
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

            // Loop untuk memasukkan data setiap hari dalam bulan tersebut
            for ($day = 1; $day <= $jumlahHari; $day++) {
                // Mengatur tanggal sesuai dengan bulan dan hari
                $tanggal_claim = date('Y-m-d', strtotime("$tahun-$bulan-$day"));

                // Menentukan kategori secara acak
                $kategori = $faker->randomElement(['Official', 'Non Official']);

                // Menentukan klasifikasi secara acak
                $klasifikasi = $faker->randomElement(['Function', 'Appearance', 'Dimension', 'Other']);

                // Memasukkan data ke dalam tabel data_claims
                DB::table('data_claims')->insert([
                    'tanggal_claim' => $tanggal_claim,
                    'customer' => $faker->company,
                    'part_no' => $faker->bothify('???-#####'),
                    'problem' => $faker->sentence,
                    'quantity' => $faker->numberBetween(1, 100),
                    'klasifikasi' => $klasifikasi,
                    'kategori' => $kategori,
                    'file_evident' => null,  // Set file_evident menjadi null
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
