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
        $data = [
            ['bulan' => 2, 'total_kirim' => 68000000, 'tahun' => 2025],
            ['bulan' => 6, 'total_kirim' => 97000000, 'tahun' => 2025],
            ['bulan' => 10, 'total_kirim' => 63180000, 'tahun' => 2025],
            ['bulan' => 4, 'total_kirim' => 590000000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 19200000, 'tahun' => 2025],
            ['bulan' => 1, 'total_kirim' => 3360000, 'tahun' => 2025],
            ['bulan' => 8, 'total_kirim' => 19200000, 'tahun' => 2025],
            ['bulan' => 12, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 11, 'total_kirim' => 22320000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 720000, 'tahun' => 2025],
            ['bulan' => 7, 'total_kirim' => 18600000, 'tahun' => 2025],
            ['bulan' => 9, 'total_kirim' => 1800000, 'tahun' => 2025],
            ['bulan' => 1, 'total_kirim' => 4800000, 'tahun' => 2025],
            ['bulan' => 6, 'total_kirim' => 1800000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 8, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 4, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 2, 'total_kirim' => 330000, 'tahun' => 2025],
            ['bulan' => 7, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 10, 'total_kirim' => 2400000, 'tahun' => 2025],
            ['bulan' => 9, 'total_kirim' => 1080000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 1680000, 'tahun' => 2025],
            ['bulan' => 4, 'total_kirim' => 13440000, 'tahun' => 2025],
            ['bulan' => 11, 'total_kirim' => 1370000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 200000, 'tahun' => 2025],
            ['bulan' => 12, 'total_kirim' => 100000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 20000, 'tahun' => 2025],
            ['bulan' => 7, 'total_kirim' => 300000, 'tahun' => 2025],
            ['bulan' => 8, 'total_kirim' => 20000, 'tahun' => 2025],
            ['bulan' => 10, 'total_kirim' => 20000, 'tahun' => 2025],
            ['bulan' => 9, 'total_kirim' => 3676090000, 'tahun' => 2025],
            ['bulan' => 1, 'total_kirim' => 4000000, 'tahun' => 2025],
            ['bulan' => 11, 'total_kirim' => 20000000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 36400000, 'tahun' => 2025],
            ['bulan' => 6, 'total_kirim' => 400000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 129400000, 'tahun' => 2025],
            ['bulan' => 2, 'total_kirim' => 10000, 'tahun' => 2025],
            ['bulan' => 7, 'total_kirim' => 86020000, 'tahun' => 2025],
            ['bulan' => 12, 'total_kirim' => 200000, 'tahun' => 2025],
            ['bulan' => 10, 'total_kirim' => 180000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 80000, 'tahun' => 2025],
            ['bulan' => 8, 'total_kirim' => 79500000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 46400000, 'tahun' => 2025],
            ['bulan' => 9, 'total_kirim' => 4800000, 'tahun' => 2025],
            ['bulan' => 4, 'total_kirim' => 25200000, 'tahun' => 2025],
            ['bulan' => 1, 'total_kirim' => 153600000, 'tahun' => 2025],
            ['bulan' => 2, 'total_kirim' => 26400000, 'tahun' => 2025],
            ['bulan' => 3, 'total_kirim' => 3600000, 'tahun' => 2025],
            ['bulan' => 6, 'total_kirim' => 3600000, 'tahun' => 2025],
            ['bulan' => 5, 'total_kirim' => 54600000, 'tahun' => 2025],
            ['bulan' => 12, 'total_kirim' => 1800000, 'tahun' => 2025]
        ];

        foreach ($data as $datum) {
            DataDummy::create($datum);
        }
    }
}
