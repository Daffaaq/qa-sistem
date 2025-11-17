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

        $tahun2025 = 2025;  // Tentukan tahun 2025
        $tahun2024 = 2024;  // Tentukan tahun 2024

        // Daftar customer yang sudah ditentukan
        $customers = [
            'PT. YAMAHA INDONESIA MOTOR MFG',
            'PT MITSUBISHI KRAMA YUDHA TIGA BERLIANMOTORS',
            'PT ASAHI DENSO INDONESIA',
        ];

        // Fungsi untuk menambahkan data untuk satu tahun
        $this->generateDataForYear($tahun2025, $customers, $faker);
        $this->generateDataForYear($tahun2024, $customers, $faker);
    }

    // Fungsi untuk generate data untuk setiap tahun
    private function generateDataForYear($tahun, $customers, $faker)
    {
        // Loop untuk setiap bulan dari Januari (bulan 1) hingga Desember (bulan 12)
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // Tentukan jumlah hari dalam bulan tersebut
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

            // Loop untuk memasukkan data setiap hari dalam bulan tersebut
            for ($day = 1; $day <= $jumlahHari; $day++) {
                // Mengatur tanggal sesuai dengan bulan dan hari
                $tanggal_claim = date('Y-m-d', strtotime("$tahun-$bulan-$day"));

                // Menentukan kategori secara acak
                $kategori = $faker->randomElement(['Official', 'Non Official']);

                // Menentukan klasifikasi secara acak
                $klasifikasi = $faker->randomElement(['Function', 'Appearance', 'Dimension', 'Other']);

                // Memilih customer secara acak dari daftar yang sudah ditentukan
                $customer = $faker->randomElement($customers);

                // Memasukkan data ke dalam tabel data_claims
                DB::table('data_claims')->insert([
                    'tanggal_claim' => $tanggal_claim,
                    'customer' => $customer,
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
