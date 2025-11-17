<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DataDummy;

class DataDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Daftar customer
        $customers = [
            [
                'kodecustomer' => 'CL00056',
                'customer' => 'PT. YAMAHA INDONESIA MOTOR MFG',
                'tsidiid' => 'TSI123',
                'tipartname' => 'Part A',
                'tipartnumber' => 'PA123'
            ],
            [
                'kodecustomer' => 'CL00063',
                'customer' => 'PT MITSUBISHI KRAMA YUDHA TIGA BERLIANMOTORS',
                'tsidiid' => 'TSI124',
                'tipartname' => 'Part B',
                'tipartnumber' => 'PA124'
            ],
            [
                'kodecustomer' => 'CL00064',
                'customer' => 'PT ASAHI DENSO INDONESIA',
                'tsidiid' => 'TSI129',
                'tipartname' => 'Part C',
                'tipartnumber' => 'PA129'
            ]
        ];

        // Fungsi untuk generate total_kirim acak per customer per bulan
        $generateTotalKirim = function ($baseMin, $baseMax, $bulan, $tahun) {
            // Variasi berdasarkan bulan dan tahun (bisa disesuaikan)
            $multiplier = ($tahun == 2025) ? 1.05 : 1.00; // 2025 naik 5%
            $seasonal = [1 => 1.1, 2 => 1.2, 4 => 1.5, 12 => 0.8]; // Contoh musiman
            $factor = $seasonal[$bulan] ?? 1.0;

            $base = rand($baseMin, $baseMax);
            return (int) ($base * $factor * $multiplier);
        };

        // Rentang base per customer (bisa disesuaikan)
        $baseRanges = [
            'CL00056' => [5000000, 80000000],  // Yamaha: sedang - tinggi
            'CL00063' => [60000000, 120000000], // Mitsubishi: tinggi
            'CL00064' => [1000000, 30000000],   // Asahi: rendah - sedang
        ];

        // Proses untuk tahun 2024 dan 2025
        foreach ([2024, 2025] as $tahun) {
            foreach ($customers as $customer) {
                $kode = $customer['kodecustomer'];
                [$min, $max] = $baseRanges[$kode] ?? [1000000, 50000000]; // default

                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $total_kirim = $generateTotalKirim($min, $max, $bulan, $tahun);

                    $datum = [
                        'bulan' => $bulan,
                        'ngnonofficial' => rand(1, 50),
                        'ngofficial' => rand(1, 50),
                        'total_kirim' => $total_kirim,
                        'tahun' => $tahun,
                        'kodecustomer' => $kode,
                        'customer' => $customer['customer'],
                        'tsidiid' => $customer['tsidiid'],
                        'tipartname' => $customer['tipartname'],
                        'tipartnumber' => $customer['tipartnumber']
                    ];

                    DataDummy::create($datum);
                }
            }
        }
    }
}
