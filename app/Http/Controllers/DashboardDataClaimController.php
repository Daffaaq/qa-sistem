<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class DashboardDataClaimController extends Controller
{

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $year = date('Y'); // Tahun sekarang otomatis, misal 2025

            // Cek apakah tabel data_dummies ada
            $tableExists = Schema::hasTable('data_dummies');

            if ($tableExists) {
                // Query asli pakai data_dummies
                $query = "
            SELECT 
                b.bulan,
                b.tahun,
                b.bulan AS bulan_no,
                COALESCE(k.total_kirim, 0) AS total_kirim,
                COALESCE(c.Qty_NG_Official, 0) AS ng_official,
                COALESCE(c.Qty_NG_Non_Official, 0) AS ng_non_official,
                CASE 
                    WHEN k.total_kirim > 0 THEN ROUND((c.Qty_NG_Official * 1000000.0) / k.total_kirim, 2)
                    ELSE 0
                END AS ppm_official,
                CASE 
                    WHEN k.total_kirim > 0 THEN ROUND((c.Qty_NG_Non_Official * 1000000.0) / k.total_kirim, 2)
                    ELSE 0
                END AS ppm_non_official,
                CASE 
                    WHEN k.total_kirim > 0 THEN ROUND(((c.Qty_NG_Official + c.Qty_NG_Non_Official) * 1000000.0) / k.total_kirim, 2)
                    ELSE 0
                END AS total_ppm
            FROM 
                (SELECT 1 AS bulan, {$year} AS tahun UNION ALL
                SELECT 2, {$year} UNION ALL
                SELECT 3, {$year} UNION ALL
                SELECT 4, {$year} UNION ALL
                SELECT 5, {$year} UNION ALL
                SELECT 6, {$year} UNION ALL
                SELECT 7, {$year} UNION ALL
                SELECT 8, {$year} UNION ALL
                SELECT 9, {$year} UNION ALL
                SELECT 10, {$year} UNION ALL
                SELECT 11, {$year} UNION ALL
                SELECT 12, {$year}) b
            LEFT JOIN 
                (SELECT 
                    MONTH(tanggal_claim) AS bulan, 
                    SUM(CASE WHEN kategori = 'Official' THEN quantity ELSE 0 END) AS Qty_NG_Official, 
                    SUM(CASE WHEN kategori = 'Non Official' THEN quantity ELSE 0 END) AS Qty_NG_Non_Official
                FROM data_claims
                WHERE YEAR(tanggal_claim) = {$year}
                GROUP BY MONTH(tanggal_claim)) c 
            ON b.bulan = c.bulan
            LEFT JOIN 
                (SELECT bulan, SUM(total_kirim) AS total_kirim
                FROM data_dummies
                WHERE tahun = {$year}
                GROUP BY bulan) k 
                ON b.bulan = k.bulan
            ORDER BY b.bulan;
            ";

                $data = DB::select(DB::raw($query));
            } else {
                // Jika tabel data_dummies tidak ada, set semua nilai jadi 0
                $data = [];
                for ($i = 1; $i <= 12; $i++) {
                    $data[] = (object)[
                        'bulan' => $i,
                        'tahun' => $year,
                        'bulan_no' => $i,
                        'total_kirim' => 0,
                        'ng_official' => 0,
                        'ng_non_official' => 0,
                        'ppm_official' => 0,
                        'ppm_non_official' => 0,
                        'total_ppm' => 0,
                    ];
                }
            }

            // Array untuk konversi bulan angka ke nama bulan
            $bulanNames = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];

            $bulanData = [];
            foreach ($bulanNames as $bulanNo => $bulanName) {
                $bulanData[$bulanNo] = [
                    'bulan' => $bulanName,
                    'ng_official' => 0,
                    'ng_non_official' => 0,
                    'total_kirim' => 0,
                    'ppm_official' => 0,
                    'ppm_non_official' => 0,
                    'total_ppm' => 0,
                ];
            }

            foreach ($data as $item) {
                $ngOfficial = $item->ng_official;
                $ngNonOfficial = $item->ng_non_official;
                $totalKirim = $item->total_kirim;

                $ppmOfficial = ($totalKirim > 0) ? round(($ngOfficial * 1000000.0) / $totalKirim, 2) : 0;
                $ppmNonOfficial = ($totalKirim > 0) ? round(($ngNonOfficial * 1000000.0) / $totalKirim, 2) : 0;
                $totalPpm = ($totalKirim > 0) ? round((($ngOfficial + $ngNonOfficial) * 1000000.0) / $totalKirim, 2) : 0;

                $bulanData[$item->bulan] = [
                    'bulan' => $bulanNames[$item->bulan],
                    'bulan_no' => $item->bulan,
                    'ng_official' => $ngOfficial,
                    'ng_non_official' => $ngNonOfficial,
                    'total_kirim' => number_format($totalKirim / 1000, 0, ',', '.'),
                    'ppm_official' => $ppmOfficial,
                    'ppm_non_official' => $ppmNonOfficial,
                    'total_ppm' => $totalPpm,
                ];
            }

            return response()->json([
                'draw' => (int) $request->draw,
                'recordsTotal' => count($bulanNames),
                'recordsFiltered' => count($bulanData),
                'data' => array_values($bulanData)
            ]);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Cek apakah tabel ada
        if (Schema::hasTable('data_dummies')) {
            $data = DB::table('data_dummies')
                ->select(DB::raw('bulan, SUM(total_kirim) as total_kirim'))
                ->groupBy('bulan')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalKirimSemua = DB::table('data_dummies')->sum('total_kirim');
        } else {
            $data = collect(); // collection kosong
            $totalKirimSemua = 0;
        }

        // Tambahkan ini
        $totalKirimSemuaFormatted = number_format($totalKirimSemua, 0, ',', '.');
        $dataTargetPPM = 3;
        $dataCurrentPPM = 0;
        $year = date('Y'); // ambil tahun sekarang otomatis

        $totalQuantity = DB::table('data_claims')
            ->whereYear('tanggal_claim', $year)
            ->sum('quantity');
        // dd($data);

        // Ambil jumlah kategori Official dan Non Official per bulan
        $dataDataClaims = DB::table('data_claims')
            ->select(DB::raw('MONTH(tanggal_claim) AS bulan, kategori, SUM(quantity) AS jumlah'))
            ->whereYear('tanggal_claim', $year) // Filter berdasarkan tahun
            ->groupBy(DB::raw('MONTH(tanggal_claim), kategori')) // Kelompokkan berdasarkan bulan dan kategori
            ->get();

        // Menyusun data untuk Official dan Non Official per bulan
        $bulanNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        $dataKategori = [];
        foreach ($bulanNames as $bulanNo => $bulanName) {
            $dataKategori[$bulanNo] = [
                'bulan' => $bulanName,
                'official' => 0,
                'non_official' => 0,
                'target_ppm' => 3  // Menambahkan target PPM 3 untuk setiap bulan
            ];
        }

        // Isi data kategori berdasarkan query yang sudah dijalankan
        foreach ($dataDataClaims as $item) {
            if ($item->kategori == 'Official') {
                $dataKategori[$item->bulan]['official'] = $item->jumlah;
            } else if ($item->kategori == 'Non Official') {
                $dataKategori[$item->bulan]['non_official'] = $item->jumlah;
            }
        }

        // Menyiapkan data untuk chart
        $ngOfficialData = array_column($dataKategori, 'official');
        $ngNonOfficialData = array_column($dataKategori, 'non_official');
        $bulanLabels = array_values($bulanNames);
        $targetPPMData = array_column($dataKategori, 'target_ppm'); // Menambahkan target PPM untuk setiap bulan

        // Data untuk chart, menambahkan target PPM dalam data chart

        // dd($ngOfficialData, $ngNonOfficialData, $bulanLabels);
        //ppm official
        $tableExists = Schema::hasTable('data_dummies');

        if ($tableExists) {
            // Query asli pakai data_dummies
            $ppmQuery = "
        SELECT 
            b.bulan,
            COALESCE(k.total_kirim, 0) AS total_kirim,
            COALESCE(c.Qty_NG_Official, 0) AS ng_official,
            CASE 
                WHEN k.total_kirim > 0 THEN ROUND((c.Qty_NG_Official * 1000000.0) / k.total_kirim, 2)
                ELSE 0
            END AS ppm_official
        FROM 
            (SELECT 1 AS bulan UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL
             SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) b
        LEFT JOIN 
            (SELECT 
                MONTH(tanggal_claim) AS bulan, 
                SUM(CASE WHEN kategori = 'Official' THEN quantity ELSE 0 END) AS Qty_NG_Official
             FROM data_claims
             WHERE YEAR(tanggal_claim) = {$year}
             GROUP BY MONTH(tanggal_claim)) c 
        ON b.bulan = c.bulan
        LEFT JOIN 
            (SELECT bulan, SUM(total_kirim) AS total_kirim
             FROM data_dummies
             WHERE tahun = {$year}
             GROUP BY bulan) k 
        ON b.bulan = k.bulan
        ORDER BY b.bulan;
    ";

            $ppmData = DB::select(DB::raw($ppmQuery));
        } else {
            // Jika tabel data_dummies tidak ada, semua nilai 0
            $ppmData = [];
            for ($i = 1; $i <= 12; $i++) {
                $ppmData[] = (object)[
                    'bulan' => $i,
                    'total_kirim' => 0,
                    'ng_official' => 0,
                    'ppm_official' => 0
                ];
            }
        }

        // Menyusun array PPM Official per bulan
        $ppmOfficialData = [];
        foreach ($ppmData as $item) {
            $ppmOfficialData[$item->bulan] = $item->ppm_official;
        }
        return view('claim-customer.dashboard.index', compact(
            'data',
            'dataTargetPPM',
            'dataCurrentPPM',
            'year',
            'totalQuantity',
            'totalKirimSemuaFormatted',
            'dataKategori',
            'targetPPMData',
            'ngOfficialData',
            'ngNonOfficialData',
            'ppmOfficialData',
            'bulanLabels'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
