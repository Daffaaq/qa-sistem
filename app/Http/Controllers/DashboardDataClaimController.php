<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\Facades\DataTables;

class DashboardDataClaimController extends Controller
{

    // public function list(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $year = date('Y');
    //         // $year = 2024; // Tahun sekarang otomatis, misal 2025

    //         // Cek apakah tabel data_dummies ada
    //         $tableExists = Schema::hasTable('data_dummies');

    //         if ($tableExists) {
    //             // Query asli pakai data_dummies
    //             $query = "
    //         SELECT 
    //             b.bulan,
    //             b.tahun,
    //             b.bulan AS bulan_no,
    //             COALESCE(k.total_kirim, 0) AS total_kirim,
    //             COALESCE(c.Qty_NG_Official, 0) AS ng_official,
    //             COALESCE(c.Qty_NG_Non_Official, 0) AS ng_non_official,
    //             CASE 
    //                 WHEN k.total_kirim > 0 THEN ROUND((c.Qty_NG_Official * 1000000.0) / k.total_kirim, 2)
    //                 ELSE 0
    //             END AS ppm_official,
    //             CASE 
    //                 WHEN k.total_kirim > 0 THEN ROUND((c.Qty_NG_Non_Official * 1000000.0) / k.total_kirim, 2)
    //                 ELSE 0
    //             END AS ppm_non_official,
    //             CASE 
    //                 WHEN k.total_kirim > 0 THEN ROUND(((c.Qty_NG_Official + c.Qty_NG_Non_Official) * 1000000.0) / k.total_kirim, 2)
    //                 ELSE 0
    //             END AS total_ppm
    //         FROM 
    //             (SELECT 1 AS bulan, {$year} AS tahun UNION ALL
    //             SELECT 2, {$year} UNION ALL
    //             SELECT 3, {$year} UNION ALL
    //             SELECT 4, {$year} UNION ALL
    //             SELECT 5, {$year} UNION ALL
    //             SELECT 6, {$year} UNION ALL
    //             SELECT 7, {$year} UNION ALL
    //             SELECT 8, {$year} UNION ALL
    //             SELECT 9, {$year} UNION ALL
    //             SELECT 10, {$year} UNION ALL
    //             SELECT 11, {$year} UNION ALL
    //             SELECT 12, {$year}) b
    //         LEFT JOIN 
    //             (SELECT 
    //                 MONTH(tanggal_claim) AS bulan, 
    //                 SUM(CASE WHEN kategori = 'Official' THEN quantity ELSE 0 END) AS Qty_NG_Official, 
    //                 SUM(CASE WHEN kategori = 'Non Official' THEN quantity ELSE 0 END) AS Qty_NG_Non_Official
    //             FROM data_claims
    //             WHERE YEAR(tanggal_claim) = {$year}
    //             GROUP BY MONTH(tanggal_claim)) c 
    //         ON b.bulan = c.bulan
    //         LEFT JOIN 
    //             (SELECT bulan, SUM(total_kirim) AS total_kirim
    //             FROM data_dummies
    //             WHERE tahun = {$year}
    //             GROUP BY bulan) k 
    //             ON b.bulan = k.bulan
    //         ORDER BY b.bulan;
    //         ";

    //             $data = DB::select(DB::raw($query));
    //         } else {
    //             // Ambil data dari data_claims meskipun data_dummies tidak ada
    //             $data = [];
    //             for ($i = 1; $i <= 12; $i++) {
    //                 $claims = DB::table('data_claims')
    //                     ->selectRaw('
    //     COALESCE(SUM(CASE WHEN LOWER(kategori) = \'official\' THEN quantity ELSE 0 END), 0) AS ng_official, 
    //     COALESCE(SUM(CASE WHEN LOWER(kategori) = \'non official\' THEN quantity ELSE 0 END), 0) AS ng_non_official
    // ')
    //                     ->whereYear('tanggal_claim', $year)
    //                     ->whereMonth('tanggal_claim', $i)
    //                     ->first();




    //                 // Menambahkan data berdasarkan hasil query
    //                 $ngOfficial = $claims ? $claims->ng_official : 0;
    //                 $ngNonOfficial = $claims ? $claims->ng_non_official : 0;

    //                 $data[] = (object)[
    //                     'bulan' => $i,
    //                     'tahun' => $year,
    //                     'bulan_no' => $i,
    //                     'total_kirim' => 0, // Karena data_dummies tidak ada, set total_kirim 0
    //                     'ng_official' => $ngOfficial,
    //                     'ng_non_official' => $ngNonOfficial,
    //                     'ppm_official' => 0,
    //                     'ppm_non_official' => 0,
    //                     'total_ppm' => 0,
    //                 ];
    //             }
    //         }

    //         // Array untuk konversi bulan angka ke nama bulan
    //         $bulanNames = [
    //             1 => 'Januari',
    //             2 => 'Februari',
    //             3 => 'Maret',
    //             4 => 'April',
    //             5 => 'Mei',
    //             6 => 'Juni',
    //             7 => 'Juli',
    //             8 => 'Agustus',
    //             9 => 'September',
    //             10 => 'Oktober',
    //             11 => 'November',
    //             12 => 'Desember'
    //         ];

    //         $bulanData = [];
    //         foreach ($bulanNames as $bulanNo => $bulanName) {
    //             $bulanData[$bulanNo] = [
    //                 'bulan' => $bulanName,
    //                 'ng_official' => 0,
    //                 'ng_non_official' => 0,
    //                 'total_kirim' => 0,
    //                 'ppm_official' => 0,
    //                 'ppm_non_official' => 0,
    //                 'total_ppm' => 0,
    //             ];
    //         }

    //         foreach ($data as $item) {
    //             $ngOfficial = $item->ng_official;
    //             $ngNonOfficial = $item->ng_non_official;
    //             $totalKirim = $item->total_kirim;

    //             $ppmOfficial = ($totalKirim > 0) ? round(($ngOfficial * 1000000.0) / $totalKirim, 2) : 0;
    //             $ppmNonOfficial = ($totalKirim > 0) ? round(($ngNonOfficial * 1000000.0) / $totalKirim, 2) : 0;
    //             $totalPpm = ($totalKirim > 0) ? round((($ngOfficial + $ngNonOfficial) * 1000000.0) / $totalKirim, 2) : 0;

    //             $bulanData[$item->bulan] = [
    //                 'bulan' => $bulanNames[$item->bulan],
    //                 'bulan_no' => $item->bulan,
    //                 'ng_official' => $ngOfficial,
    //                 'ng_non_official' => $ngNonOfficial,
    //                 'total_kirim' => number_format($totalKirim / 1000, 0, ',', '.'),
    //                 'ppm_official' => $ppmOfficial,
    //                 'ppm_non_official' => $ppmNonOfficial,
    //                 'total_ppm' => $totalPpm,
    //             ];
    //         }

    //         return response()->json([
    //             'draw' => (int) $request->draw,
    //             'recordsTotal' => count($bulanNames),
    //             'recordsFiltered' => count($bulanData),
    //             'data' => array_values($bulanData)
    //         ]);
    //     }
    // }

    public function list(Request $request)
    {
        $year = $request->filled('tahun') ? (int) $request->input('tahun') : (int) date('Y');
        $customer = $request->input('customer');

        $tableExists = Schema::hasTable('data_dummies');

        // === QUERY UTAMA — SELALU PAKAI FILTER CUSTOMER & TAHUN ===
        $query = "
        SELECT
            b.bulan,
            {$year} AS tahun,
            b.bulan AS bulan_no,
            COALESCE(SUM(d.total_kirim), 0) AS total_kirim,
            COALESCE(SUM(d.ngofficial), 0) AS ng_official,
            COALESCE(SUM(d.ngnonofficial), 0) AS ng_non_official,
            CASE WHEN SUM(d.total_kirim) > 0 
                 THEN ROUND((SUM(d.ngofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                 ELSE 0 
            END AS ppm_official,
            CASE WHEN SUM(d.total_kirim) > 0 
                 THEN ROUND((SUM(d.ngnonofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                 ELSE 0 
            END AS ppm_non_official,
            CASE WHEN SUM(d.total_kirim) > 0 
                 THEN ROUND(((SUM(d.ngofficial) + SUM(d.ngnonofficial)) * 1000000.0) / SUM(d.total_kirim), 2) 
                 ELSE 0 
            END AS total_ppm
        FROM (
            SELECT 1 AS bulan UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
            SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL
            SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
            SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
        ) b
        LEFT JOIN data_dummies d 
            ON b.bulan = d.bulan 
            AND d.tahun = ?
    ";

        $bindings = [$year];
        if (!empty($customer)) {
            $query .= " AND d.customer = ?";
            $bindings[] = $customer;
        }

        $query .= " GROUP BY b.bulan ORDER BY b.bulan";

        // === EKSEKUSI QUERY ===
        $data = $tableExists ? DB::select(DB::raw($query), $bindings) : [];

        // === FALLBACK: Jika tidak ada data_dummies atau tidak ada hasil ===
        if (empty($data)) {
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

        // === FORMAT UNTUK DATATABLES ===
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

        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = [
                'bulan' => $bulanNames[$item->bulan],
                'bulan_no' => $item->bulan,
                'tahun' => $item->tahun,
                'total_kirim' => number_format($item->total_kirim, 0, ',', '.'),
                'ng_official' => (int) $item->ng_official,           // 16
                'ng_non_official' => (int) $item->ng_non_official,
                'ppm_official' => (float) $item->ppm_official,
                'ppm_non_official' => (float) $item->ppm_non_official,
                'total_ppm' => (float) $item->total_ppm,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => 12,
            'recordsFiltered' => 12,
            'data' => $formattedData,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $year = date('Y'); // TAHUN INI SAJA

        $tableExists = Schema::hasTable('data_dummies');

        // === CUSTOMER & TAHUN UNTUK DROPDOWN (TANPA FILTER) ===
        $dataCustomer = $tableExists
            ? DB::table('data_dummies')->select('customer')->distinct()->get()
            : collect();

        $tahun = $tableExists
            ? DB::table('data_dummies')->select('tahun')->distinct()->orderBy('tahun', 'desc')->get()
            : collect();

        if ($tableExists) {
            // === TOTAL KIRIM PER BULAN (SEMUA CUSTOMER, TAHUN INI) ===
            $data = DB::table('data_dummies')
                ->selectRaw('bulan, SUM(total_kirim) as total_kirim')
                ->where('tahun', $year)
                ->groupBy('bulan')
                ->orderBy('bulan', 'asc')
                ->get();

            $totalKirimSemua = DB::table('data_dummies')
                ->where('tahun', $year)
                ->sum('total_kirim');

            // === NG OFFICIAL & NON OFFICIAL (SEMUA CUSTOMER) ===
            $ngData = DB::table('data_dummies')
                ->selectRaw('bulan, SUM(ngofficial) AS ng_official, SUM(ngnonofficial) AS ng_non_official')
                ->where('tahun', $year)
                ->groupBy('bulan')
                ->get()
                ->keyBy('bulan');

            $totalQuantity = DB::table('data_dummies')
                ->where('tahun', $year)
                ->sum(DB::raw('ngofficial + ngnonofficial'));

            // === PPM QUERY — SAMA PERSIS DENGAN list() TANPA FILTER ===
            $ppmQuery = "
            SELECT
                b.bulan,
                COALESCE(SUM(d.total_kirim), 0) AS total_kirim,
                COALESCE(SUM(d.ngofficial), 0) AS ng_official,
                COALESCE(SUM(d.ngnonofficial), 0) AS ng_non_official,
                CASE WHEN SUM(d.total_kirim) > 0 
                     THEN ROUND((SUM(d.ngofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                     ELSE 0 
                END AS ppm_official,
                CASE WHEN SUM(d.total_kirim) > 0 
                     THEN ROUND((SUM(d.ngnonofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                     ELSE 0 
                END AS ppm_non_official
            FROM (
                SELECT 1 AS bulan UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
                SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL
                SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
                SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
            ) b
            LEFT JOIN data_dummies d ON b.bulan = d.bulan AND d.tahun = ?
            GROUP BY b.bulan
            ORDER BY b.bulan
        ";
            $ppmData = DB::select(DB::raw($ppmQuery), [$year]);
        } else {
            $data = collect();
            $totalKirimSemua = 0;
            $totalQuantity = 0;
            $ngData = collect();
            $ppmData = array_fill(0, 12, (object)[
                'bulan' => 0,
                'total_kirim' => 0,
                'ng_official' => 0,
                'ng_non_official' => 0,
                'ppm_official' => 0,
                'ppm_non_official' => 0
            ]);
        }

        $totalKirimSemuaFormatted = number_format($totalKirimSemua, 0, ',', '.');
        $dataTargetPPM = 3;
        $dataCurrentPPM = 0;

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

        // === DATA KATEGORI ===
        $dataKategori = [];
        foreach ($bulanNames as $no => $nama) {
            $ngOfficial = $ngData->get($no)->ng_official ?? 0;
            $ngNonOfficial = $ngData->get($no)->ng_non_official ?? 0;

            $dataKategori[$no] = [
                'bulan' => $nama,
                'official' => $ngOfficial,
                'non_official' => $ngNonOfficial,
                'target_ppm' => 3
            ];
        }

        // === CHART DATA ===
        $ngOfficialData = array_column($dataKategori, 'official');
        $ngNonOfficialData = array_column($dataKategori, 'non_official');
        $bulanLabels = array_values($bulanNames);
        $targetPPMData = array_fill(0, 12, 3);

        $ppmOfficialData = array_fill(0, 12, 0.0);  // mulai dari 0, float
        $ppmNonOfficialData = array_fill(0, 12, 0.0);

        foreach ($ppmData as $item) {
            $bulanIndex = $item->bulan - 1; // Januari = 0
            $ppmOfficialData[$bulanIndex] = (float) $item->ppm_official;
            $ppmNonOfficialData[$bulanIndex] = (float) $item->ppm_non_official;
        }
        return view('claim-customer.dashboard.index', compact(
            'data',
            'dataTargetPPM',
            'year',
            'totalQuantity',
            'totalKirimSemuaFormatted',
            'dataKategori',
            'targetPPMData',
            'dataCurrentPPM',
            'ngOfficialData',
            'ngNonOfficialData',
            'ppmOfficialData',
            'ppmNonOfficialData',
            'bulanLabels',
            'dataCustomer',
            'tahun'
        ));
    }

    public function updateChartData(Request $request)
    {
        $year = $request->filled('tahun') ? (int) $request->input('tahun') : (int) date('Y');
        $customer = $request->input('customer');

        $tableExists = Schema::hasTable('data_dummies');

        if (!$tableExists) {
            return response()->json([
                'totalDelivery' => '0',
                'totalNG' => '0',
                'year' => $year,
                'bulanLabels' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                'ngOfficialData' => array_fill(0, 12, 0),
                'ngNonOfficialData' => array_fill(0, 12, 0),
                'ppmOfficialData' => array_fill(0, 12, 0.0),
                'ppmNonOfficialData' => array_fill(0, 12, 0.0),
                'targetPPMData' => array_fill(0, 12, 3),
            ]);
        }

        // === QUERY DENGAN FILTER ===
        $query = DB::table('data_dummies')->where('tahun', $year);
        if ($customer) {
            $query->where('customer', $customer);
            
        }

        // === TOTAL DELIVERY (dari data_dummies) ===
        $totalDelivery = (clone $query)->sum('total_kirim');
        // dd($customer, $query, $totalDelivery);
        // === TOTAL NG (dari data_dummies) ===
        $totalNG = (clone $query)->sum(DB::raw('ngofficial + ngnonofficial'));

        // === NG PER BULAN ===
        $ngData = (clone $query)
            ->selectRaw('bulan, SUM(ngofficial) AS ng_official, SUM(ngnonofficial) AS ng_non_official')
            ->groupBy('bulan')
            ->get()
            ->keyBy('bulan');

        // === PPM QUERY — FULL DUMMY + FILTER ===
        $ppmQuery = "
        SELECT
            b.bulan,
            COALESCE(SUM(d.total_kirim), 0) AS total_kirim,
            COALESCE(SUM(d.ngofficial), 0) AS ng_official,
            COALESCE(SUM(d.ngnonofficial), 0) AS ng_non_official,
            CASE WHEN SUM(d.total_kirim) > 0 
                 THEN ROUND((SUM(d.ngofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                 ELSE 0 
            END AS ppm_official,
            CASE WHEN SUM(d.total_kirim) > 0 
                 THEN ROUND((SUM(d.ngnonofficial) * 1000000.0) / SUM(d.total_kirim), 2) 
                 ELSE 0 
            END AS ppm_non_official
        FROM (
            SELECT 1 AS bulan UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL
            SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL
            SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL
            SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
        ) b
        LEFT JOIN data_dummies d ON b.bulan = d.bulan AND d.tahun = ?
    ";

        $bindings = [$year];
        if ($customer) {
            $ppmQuery .= " AND d.customer = ?";
            $bindings[] = $customer;
        }
        $ppmQuery .= " GROUP BY b.bulan ORDER BY b.bulan";
        $ppmData = DB::select(DB::raw($ppmQuery), $bindings);

        // === DATA KATEGORI ===
        $bulanNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $dataKategori = array_fill(0, 12, ['official' => 0, 'non_official' => 0, 'target_ppm' => 3]);

        foreach ($ngData as $bulan => $row) {
            $idx = $bulan - 1;
            $dataKategori[$idx]['official'] = (int) ($row->ng_official ?? 0);
            $dataKategori[$idx]['non_official'] = (int) ($row->ng_non_official ?? 0);
        }

        // === PPM ARRAY ===
        $ppmOfficial = array_fill(0, 12, 0.0);
        $ppmNonOfficial = array_fill(0, 12, 0.0);
        foreach ($ppmData as $item) {
            $idx = $item->bulan - 1;
            $ppmOfficial[$idx] = (float) $item->ppm_official;
            $ppmNonOfficial[$idx] = (float) $item->ppm_non_official;
        }

        // === RETURN ===
        return response()->json([
            'totalDelivery' => number_format($totalDelivery, 0, ',', '.'),
            'totalNG' => number_format($totalNG, 0, ',', '.'),
            'year' => $year,
            'bulanLabels' => $bulanNames,
            'ngOfficialData' => array_column($dataKategori, 'official'),
            'ngNonOfficialData' => array_column($dataKategori, 'non_official'),
            'ppmOfficialData' => $ppmOfficial,
            'ppmNonOfficialData' => $ppmNonOfficial,
            'targetPPMData' => array_fill(0, 12, 3),
        ]);
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
