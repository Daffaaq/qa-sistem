<?php

namespace App\Http\Controllers;

use App\Models\MenuGroup;
use App\Models\Sop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $documentManualMutu = DB::table('documents')->where('category_document', 'Manual Mutu')->count();
        $documentSQAMCustomer = DB::table('documents')->where('category_document', 'SQAM Customer')->count();
        $documentSQAMSupplier = DB::table('documents')->where('category_document', 'SQAM Supplier')->count();
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'QA QC');
        })->with(['wis.forms'])->get();

        $sopCount = $sops->count();
        $wiCount = $sops->sum(fn($sop) => $sop->wis->count());
        $formCount = $sops->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $qaqc = $sopCount + $wiCount + $formCount;

        $sopRep = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');
        })->with(['wis.forms'])->get();

        $sopCountRep = $sopRep->count();
        $wiCountRep = $sopRep->sum(fn($sop) => $sop->wis->count());
        $formCountRep = $sopRep->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $manRep = $sopCountRep + $wiCountRep + $formCountRep;

        $sopPPIC = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'PPIC');
        })->with(['wis.forms'])->get();

        $sopCountPPIC = $sopPPIC->count();
        $wiCountPPIC = $sopPPIC->sum(fn($sop) => $sop->wis->count());
        $formCountPPIC = $sopPPIC->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $ppic = $sopCountPPIC + $wiCountPPIC + $formCountPPIC;

        $sopMaintanance = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Maintanance');
        })->with(['wis.forms'])->get();

        $sopCountMaintanance = $sopMaintanance->count();
        $wiCountMaintanance = $sopMaintanance->sum(fn($sop) => $sop->wis->count());
        $formCountMaintanance = $sopMaintanance->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $maintanance = $sopCountMaintanance + $wiCountMaintanance + $formCountMaintanance;

        $sopHumanCapital = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Human Capital');
        })->with(['wis.forms'])->get();

        $sopCountHumanCapital = $sopHumanCapital->count();
        $wiCountHumanCapital = $sopHumanCapital->sum(fn($sop) => $sop->wis->count());
        $formCountHumanCapital = $sopHumanCapital->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $humanCapital = $sopCountHumanCapital + $wiCountHumanCapital + $formCountHumanCapital;

        $sopEngineering = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Engineering');
        })->with(['wis.forms'])->get();
        
        $sopCountEngineering = $sopEngineering->count();
        $wiCountEngineering = $sopEngineering->sum(fn($sop) => $sop->wis->count());
        $formCountEngineering = $sopEngineering->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $engineering = $sopCountEngineering + $wiCountEngineering + $formCountEngineering;

        $sopIRGA = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'IRGA');
        })->with(['wis.forms'])->get();

        $sopCountIRGA = $sopIRGA->count();
        $wiCountIRGA = $sopIRGA->sum(fn($sop) => $sop->wis->count());
        $formCountIRGA = $sopIRGA->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $irga = $sopCountIRGA + $wiCountIRGA + $formCountIRGA;

        $sopSHE = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'SHE');
        })->with(['wis.forms'])->get();

        $sopCountSHE = $sopSHE->count();
        $wiCountSHE = $sopSHE->sum(fn($sop) => $sop->wis->count());
        $formCountSHE = $sopSHE->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        $she = $sopCountSHE + $wiCountSHE + $formCountSHE;

        // Collect data and sort it
        $documentCounts = [
            ['name' => 'Manual Mutu', 'count' => $documentManualMutu],
            ['name' => 'SQAM Customer', 'count' => $documentSQAMCustomer],
            ['name' => 'SQAM Supplier', 'count' => $documentSQAMSupplier],
            ['name' => 'QA QC', 'count' => $qaqc],
        ];

        // Sort in descending order by count
        usort($documentCounts, fn($a, $b) => $b['count'] - $a['count']);

        // Take the top 3
        $top3Documents = array_slice($documentCounts, 0, 3);
        // dd($top3Documents);

        // Total dokumen di-upload bulan ini dari document_histories dan documents yang aktif
        // $totalDocumentsThisMonth = DB::table('document_histories')
        //     ->join('documents', 'document_histories.document_id', '=', 'documents.id')
        //     ->whereMonth('document_histories.created_at', now()->month) // Menyaring berdasarkan bulan sekarang
        //     ->whereYear('document_histories.created_at', now()->year)  // Menyaring berdasarkan tahun sekarang
        //     ->where('document_histories.is_active', 1)  // Menyaring berdasarkan dokumen yang aktif
        //     ->count();


        // Hitung SOP yang di-upload bulan ini dan yang aktif
        $totalSopsThisMonth = DB::table('sop_histories')
            ->join('sops', 'sop_histories.sop_id', '=', 'sops.id')
            ->whereMonth('sop_histories.created_at', now()->month)
            ->whereYear('sop_histories.created_at', now()->year)
            ->where('sop_histories.is_active', 1)  // Menyaring berdasarkan SOP yang aktif
            ->count();


        // Hitung WI yang di-upload bulan ini dan yang aktif
        $totalWisThisMonth = DB::table('wi_histories')
            ->join('wis', 'wi_histories.wi_id', '=', 'wis.id')
            ->whereMonth('wi_histories.created_at', now()->month)
            ->whereYear('wi_histories.created_at', now()->year)
            ->where('wi_histories.is_active', 1)  // Menyaring berdasarkan WI yang aktif
            ->count();


        // Hitung Form yang di-upload bulan ini dan yang aktif (meskipun data kosong)
        $totalFormsThisMonth = DB::table('form_histories')
            ->join('forms', 'form_histories.form_id', '=', 'forms.id')
            ->whereMonth('form_histories.created_at', now()->month)
            ->whereYear('form_histories.created_at', now()->year)
            ->where('form_histories.is_active', 1)  // Menyaring berdasarkan Form yang aktif
            ->count();

        // Hitung total dokumen tahun ini
        $totalDocumentsThisYear = DB::table('document_histories')
            ->join('documents', 'document_histories.document_id', '=', 'documents.id')
            ->whereMonth('document_histories.created_at', now()->month) // Menyaring berdasarkan bulan yang sama tahun ini
            ->whereYear('document_histories.created_at', now()->year) // Menyaring berdasarkan tahun ini
            ->where('document_histories.is_active', 1)  // Menyaring berdasarkan dokumen yang aktif
            ->count();

        // Hitung total dokumen tahun lalu
        $totalDocumentsLastYear = DB::table('document_histories')
            ->join('documents', 'document_histories.document_id', '=', 'documents.id')
            ->whereMonth('document_histories.created_at', now()->month) // Menyaring berdasarkan bulan yang sama tahun lalu
            ->whereYear('document_histories.created_at', now()->subYear()->year) // Menyaring berdasarkan tahun lalu
            ->where('document_histories.is_active', 1)  // Menyaring berdasarkan dokumen yang aktif
            ->count();

        // Hitung SOP yang di-upload tahun ini dan yang aktif
        $totalSopsThisYear = DB::table('sop_histories')
            ->join('sops', 'sop_histories.sop_id', '=', 'sops.id')
            ->whereMonth('sop_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun ini
            ->whereYear('sop_histories.created_at', now()->year)  // Menyaring berdasarkan tahun ini
            ->where('sop_histories.is_active', 1)  // Menyaring berdasarkan SOP yang aktif
            ->count();

        // Hitung SOP yang di Upload tahun lalu dan yang aktif
        $totalSopsLastYear = DB::table('sop_histories')
            ->join('sops', 'sop_histories.sop_id', '=', 'sops.id')
            ->whereMonth('sop_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun lalu
            ->whereYear('sop_histories.created_at', now()->subYear()->year)  // Menyaring berdasarkan tahun lalu
            ->where('sop_histories.is_active', 1)  // Menyaring berdasarkan SOP yang aktif
            ->count();

        // Hitung WI yang di upload tahun ini dan yang aktif
        $totalWisThisYear = DB::table('wi_histories')
            ->join('wis', 'wi_histories.wi_id', '=', 'wis.id')
            ->whereMonth('wi_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun ini
            ->whereYear('wi_histories.created_at', now()->year)  // Menyaring berdasarkan tahun ini
            ->where('wi_histories.is_active', 1)  // Menyaring berdasarkan WI yang aktif
            ->count();

        // Hitung WI yang di upload tahun lalu dan yang aktif
        $totalWisLastYear = DB::table('wi_histories')
            ->join('wis', 'wi_histories.wi_id', '=', 'wis.id')
            ->whereMonth('wi_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun lalu
            ->whereYear('wi_histories.created_at', now()->subYear()->year)  // Menyaring berdasarkan tahun lalu
            ->where('wi_histories.is_active', 1)  // Menyaring berdasarkan WI yang aktif
            ->count();

        // Hitung Form yang di-upload tahun ini dan yang aktif
        $totalFormsThisYear = DB::table('form_histories')
            ->join('forms', 'form_histories.form_id', '=', 'forms.id')
            ->whereMonth('form_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun ini
            ->whereYear('form_histories.created_at', now()->year)  // Menyaring berdasarkan tahun ini
            ->where('form_histories.is_active', 1)  // Menyaring berdasarkan Form yang aktif
            ->count();

        // Hitung Form yang di-upload tahun lalu dan yang aktif
        $totalFormsLastYear = DB::table('form_histories')
            ->join('forms', 'form_histories.form_id', '=', 'forms.id')
            ->whereMonth('form_histories.created_at', now()->month)  // Menyaring berdasarkan bulan yang sama tahun lalu
            ->whereYear('form_histories.created_at', now()->subYear()->year)  // Menyaring berdasarkan tahun lalu
            ->where('form_histories.is_active', 1)  // Menyaring berdasarkan Form yang aktif
            ->count();

        // Hitung total dokumen QA QC (SOP, WI, dan Form) yang di-upload tahun ini
        $totalQaqcThisYear = $totalSopsThisYear + $totalWisThisYear + $totalFormsThisYear;

        // Hitung total dokumen QA QC (SOP, WI, dan Form) yang di-upload tahun lalu
        $totalQaqcLastYear = $totalSopsLastYear + $totalWisLastYear + $totalFormsLastYear;

        //Hitung total dokumen tahun ini dari kategori-kategori yang ada
        $totalDocumentsThisYear = $totalDocumentsThisYear + $totalQaqcThisYear;

        // Hitung total dokumen tahun lalu dari kategori-kategori yang ada
        $totalDocumentsLastYear = $totalDocumentsLastYear + $totalQaqcLastYear;

        // Hitung persentase perubahan dibanding tahun lalu
        $percentageChangethisYear = $totalQaqcThisYear > 0 && $totalDocumentsLastYear > 0
            ? (($totalQaqcThisYear - $totalDocumentsLastYear) / $totalDocumentsLastYear) * 100
            : 0;
        $currentYear = now()->year;  // Tahun ini
        $lastYear = now()->subYear()->year;  // Tahun lalu

        $documentsPerMonth = DB::table('document_histories')
            ->select(DB::raw("CONVERT(VARCHAR(7), created_at, 120) as bulan"), DB::raw('COUNT(*) as total_upload'))
            ->whereYear('created_at', now()->year)
            ->where('is_active', 1)
            ->groupBy(DB::raw("CONVERT(VARCHAR(7), created_at, 120)"));

        $sopsPerMonth = DB::table('sop_histories')
            ->select(DB::raw("CONVERT(VARCHAR(7), created_at, 120) as bulan"), DB::raw('COUNT(*) as total_upload'))
            ->whereYear('created_at', now()->year)
            ->where('is_active', 1)
            ->groupBy(DB::raw("CONVERT(VARCHAR(7), created_at, 120)"));

        $wisPerMonth = DB::table('wi_histories')
            ->select(DB::raw("CONVERT(VARCHAR(7), created_at, 120) as bulan"), DB::raw('COUNT(*) as total_upload'))
            ->whereYear('created_at', now()->year)
            ->where('is_active', 1)
            ->groupBy(DB::raw("CONVERT(VARCHAR(7), created_at, 120)"));

        $formsPerMonth = DB::table('form_histories')
            ->select(DB::raw("CONVERT(VARCHAR(7), created_at, 120) as bulan"), DB::raw('COUNT(*) as total_upload'))
            ->whereYear('created_at', now()->year)
            ->where('is_active', 1)
            ->groupBy(DB::raw("CONVERT(VARCHAR(7), created_at, 120)"));

        // Ambil tahun sekarang atau tentukan tahun
        $tahun = now()->year;

        // Buat array bulan dari Januari sampai Desember
        $bulanArr = [];
        for ($i = 1; $i <= 12; $i++) {
            $bulanArr[] = sprintf('%d-%02d', $tahun, $i);
        }

        // Hasil query seperti yang kamu buat
        $allUploads = $documentsPerMonth
            ->unionAll($sopsPerMonth)
            ->unionAll($wisPerMonth)
            ->unionAll($formsPerMonth)
            ->get();

        // Group dan sum per bulan
        $dataGrouped = $allUploads->groupBy('bulan')->map(function ($monthData) {
            return $monthData->sum('total_upload');
        });

        $namaBulan = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'Mei',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Agu',
            '09' => 'Sep',
            '10' => 'Okt',
            '11' => 'Nov',
            '12' => 'Des'
        ];

        $result = collect($bulanArr)->map(function ($bln) use ($dataGrouped, $namaBulan) {
            // Pecah string '2025-01' jadi ['2025', '01']
            [$tahun, $bulan] = explode('-', $bln);
            $bulanText = $namaBulan[$bulan] . '-' . $tahun;

            return [
                'bulan' => $bulanText,
                'total_upload' => $dataGrouped[$bln] ?? 0
            ];
        });

        // Hitung SOP yang di-upload bulan lalu dan yang aktif
        $totalSopsLastMonth = DB::table('sop_histories')
            ->join('sops', 'sop_histories.sop_id', '=', 'sops.id')
            ->whereMonth('sop_histories.created_at', now()->subMonth()->month)  // Bulan lalu
            ->whereYear('sop_histories.created_at', now()->year)  // Tahun ini
            ->where('sop_histories.is_active', 1)  // Menyaring berdasarkan SOP yang aktif
            ->count();

        // Hitung WI yang di-upload bulan lalu dan yang aktif
        $totalWisLastMonth = DB::table('wi_histories')
            ->join('wis', 'wi_histories.wi_id', '=', 'wis.id')
            ->whereMonth('wi_histories.created_at', now()->subMonth()->month)  // Bulan lalu
            ->whereYear('wi_histories.created_at', now()->year)  // Tahun ini
            ->where('wi_histories.is_active', 1)  // Menyaring berdasarkan WI yang aktif
            ->count();

        // Hitung Form yang di-upload bulan lalu dan yang aktif
        $totalFormsLastMonth = DB::table('form_histories')
            ->join('forms', 'form_histories.form_id', '=', 'forms.id')
            ->whereMonth('form_histories.created_at', now()->subMonth()->month)  // Bulan lalu
            ->whereYear('form_histories.created_at', now()->year)  // Tahun ini
            ->where('form_histories.is_active', 1)  // Menyaring berdasarkan Form yang aktif
            ->count();

        // Hitung total QA QC (SOP, WI, Form) bulan lalu
        $totalQaqcLastMonth = $totalSopsLastMonth + $totalWisLastMonth + $totalFormsLastMonth;

        // Hitung total dokumen QA QC (SOP, WI, dan Form) yang di-upload bulan ini
        $totalQaqcThisMonth = $totalSopsThisMonth + $totalWisThisMonth + $totalFormsThisMonth;

        // Hitung total dokumen bulan ini dari kategori-kategori yang ada
        $totalDocumentsThisMonth = DB::table('documents')
            ->whereIn('category_document', ['Manual Mutu', 'SQAM Customer', 'SQAM Supplier'])
            ->whereMonth('created_at', now()->month)  // Bulan ini
            ->whereYear('created_at', now()->year)  // Tahun ini
            ->count() + $totalQaqcThisMonth; // Menambahkan QA QC (SOP, WI, dan Form)

        // Hitung total dokumen bulan lalu
        $totalDocumentsLastMonth = DB::table('documents')
            ->whereIn('category_document', ['Manual Mutu', 'SQAM Customer', 'SQAM Supplier'])
            ->whereMonth('created_at', now()->subMonth()->month) // Bulan lalu
            ->whereYear('created_at', now()->year)  // Tahun ini
            ->count() + $totalQaqcLastMonth; // Menambahkan QA QC bulan lalu

        // Hitung persentase perubahan dibanding bulan lalu
        $percentageChangeThisMonth = $totalDocumentsThisMonth > 0 && $totalDocumentsLastMonth > 0
            ? (($totalDocumentsThisMonth - $totalDocumentsLastMonth) / $totalDocumentsLastMonth) * 100
            : 0;
        // dd($percentageChangeThisMonth);
        $year = request('year', now()->year);
        $month = request('month', now()->month);
        $weeklyUploads = collect();

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $currentStart = $startOfMonth->copy();
        $currentEnd = $currentStart->copy()->endOfWeek(Carbon::SUNDAY);

        while ($currentStart->lte($endOfMonth)) {
            if ($currentEnd->gt($endOfMonth)) {
                $currentEnd = $endOfMonth->copy();
            }

            $startOfWeekFormatted = $currentStart->toDateString();
            $endOfWeekFormatted = $currentEnd->toDateString();

            $count =
                DB::table('document_histories')
                ->join('documents', 'document_histories.document_id', '=', 'documents.id')
                ->whereRaw('CONVERT(date, document_histories.created_at) BETWEEN ? AND ?', [$startOfWeekFormatted, $endOfWeekFormatted])
                ->where('document_histories.is_active', 1)
                ->whereIn('documents.category_document', ['Manual Mutu', 'SQAM Customer', 'SQAM Supplier'])
                ->count()
                +
                DB::table('sop_histories')
                ->join('sops', 'sop_histories.sop_id', '=', 'sops.id')
                ->whereRaw('CONVERT(date, sop_histories.created_at) BETWEEN ? AND ?', [$startOfWeekFormatted, $endOfWeekFormatted])
                ->where('sop_histories.is_active', 1)
                ->count()
                +
                DB::table('wi_histories')
                ->join('wis', 'wi_histories.wi_id', '=', 'wis.id')
                ->whereRaw('CONVERT(date, wi_histories.created_at) BETWEEN ? AND ?', [$startOfWeekFormatted, $endOfWeekFormatted])
                ->where('wi_histories.is_active', 1)
                ->count()
                +
                DB::table('form_histories')
                ->join('forms', 'form_histories.form_id', '=', 'forms.id')
                ->whereRaw('CONVERT(date, form_histories.created_at) BETWEEN ? AND ?', [$startOfWeekFormatted, $endOfWeekFormatted])
                ->where('form_histories.is_active', 1)
                ->count();

            $weeklyUploads->push($count);

            // Format label dengan rentang tanggal, misal "Week 1 (01 Oct - 05 Oct)"
            $weekNumber = $weeklyUploads->count();
            $label = "Week $weekNumber (" . $currentStart->format('d M') . " - " . $currentEnd->format('d M') . ")";
            $weeklyLabels[] = $label;

            $currentStart = $currentEnd->copy()->addDay(1);
            $currentEnd = $currentStart->copy()->endOfWeek(Carbon::SUNDAY);
        }

        // Dapatkan tanggal hari ini, tapi sesuaikan tahun dan bulan yang diminta
        $today = now();
        if ($year != $today->year || $month != $today->month) {
            // Jika bukan bulan/tahun sekarang, set today ke akhir bulan supaya semua minggu tampil
            $today = $endOfMonth;
        }

        // Cari minggu ke berapa hari ini di bulan yang dipilih (menggunakan minggu yang dimulai Senin dan diakhiri Minggu)
        $weekOfMonthToday = $today->diffInWeeks($startOfMonth->startOfWeek(Carbon::MONDAY)) + 1;

        // Batasi data sesuai minggu sekarang
        $weeklyUploads = $weeklyUploads->slice(0, $weekOfMonthToday)->values();
        $weeklyLabels = array_slice($weeklyLabels, 0, $weekOfMonthToday);
        // Buat label untuk minggu yang tampil
        $labels = [];
        for ($i = 1; $i <= $weekOfMonthToday; $i++) {
            $labels[] = "Week $i";
        }

        // dd($weeklyUploads);
        // dd($labels, $weeklyLabels);
        $documentHistories = DB::table('document_histories')
            ->whereRaw('CONVERT(date, created_at) BETWEEN ? AND ?', ['2025-10-20', '2025-10-26']) // Rentang tanggal yang dihitung
            ->get();

        // Hitung persentase perubahan dibandingkan tahun lalu
        $percentageChangeyear = $totalDocumentsThisMonth > 0 && $totalDocumentsLastYear > 0
            ? (($totalDocumentsThisMonth - $totalDocumentsLastYear) / $totalDocumentsLastYear) * 100
            : 0;

        $categories = DB::table('documents')->select('category_document')->distinct()->pluck('category_document');
        $selectedCategory = request('category');


        // Query untuk documents
        $documents = DB::table('documents')
            ->join('document_histories', 'documents.id', '=', 'document_histories.document_id')
            ->where('document_histories.is_active', 1)
            ->select(
                DB::raw("'document' as type"),
                'document_histories.title_document as title',
                'document_histories.file_document as file',
                'document_histories.date_document',
                'document_histories.time_document',
                'documents.category_document as category',
                'document_histories.created_at',
                // Tambahkan kolom folder berdasarkan kategori
                DB::raw("CASE 
            WHEN documents.category_document = 'Manual Mutu' THEN 'manual-mutu'
            WHEN documents.category_document = 'SQAM Supplier' THEN 'sqam-supplier'
            WHEN documents.category_document = 'SQAM Customer' THEN 'sqam-customer'
            ELSE ''
        END as folder")
            );

        // Query untuk SOPs
        $sops = DB::table('sops')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('sop_histories', 'sops.id', '=', 'sop_histories.sop_id')
            ->where('sop_histories.is_active', 1)
            ->select(
                DB::raw("'sop' as type"),
                'sop_histories.title_document as title',
                'sop_histories.file_document as file',
                'sop_histories.date_document',
                'sop_histories.time_document',
                'documents.category_document as category',
                'sop_histories.created_at',
                DB::raw("'' as folder") // SOP tidak perlu folder
            );

        // Query untuk WIs
        $wis = DB::table('wis')
            ->join('sops', 'wis.sop_id', '=', 'sops.id')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('wi_histories', 'wis.id', '=', 'wi_histories.wi_id')
            ->where('wi_histories.is_active', 1)
            ->select(
                DB::raw("'wi' as type"),
                'wi_histories.title_document as title',
                'wi_histories.file_document as file',
                'wi_histories.date_document',
                'wi_histories.time_document',
                'documents.category_document as category',
                'wi_histories.created_at',
                DB::raw("'' as folder") // WI tidak perlu folder
            );

        // Query untuk Forms
        $forms = DB::table('forms')
            ->join('wis', 'forms.wi_id', '=', 'wis.id')
            ->join('sops', 'wis.sop_id', '=', 'sops.id')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('form_histories', 'forms.id', '=', 'form_histories.form_id')
            ->where('form_histories.is_active', 1)
            ->select(
                DB::raw("'form' as type"),
                'form_histories.title_document as title',
                'form_histories.file_document as file',
                'form_histories.date_document',
                'form_histories.time_document',
                'documents.category_document as category',
                'form_histories.created_at',
                DB::raw("'' as folder") // Form tidak perlu folder
            );

        // Gabungkan semua query
        $allFiles = $documents->unionAll($sops)->unionAll($wis)->unionAll($forms);

        // Bungkus di subquery untuk order dan limit
        $allFiles = DB::query()->fromSub($allFiles, 'all_files')
            ->orderByDesc('created_at')
            ->limit(5);

        // Filter kategori jika ada
        if ($selectedCategory) {
            $allFiles = $allFiles->where('category', $selectedCategory);
        }

        // Ambil hasil
        $allFiles = $allFiles->get();


        // dd($allFiles);
        // dd($weeklyUploads);
        return view(
            'dashboard.index',
            compact(
                'documentManualMutu',
                'documentSQAMCustomer',
                'documentSQAMSupplier',
                'qaqc',
                'manRep',
                'ppic',
                'maintanance',
                'humanCapital',
                'engineering',
                'irga',
                'she',
                'categories',
                'selectedCategory',
                'allFiles',
                'top3Documents',
                'totalDocumentsThisMonth',
                'percentageChangeyear',
                'weeklyUploads',
                'percentageChangeThisMonth',
                'labels',
                'weeklyLabels',
                'totalDocumentsThisYear',
                'totalDocumentsLastYear',
                'percentageChangethisYear',
                'currentYear',
                'lastYear',
                'result'
            )
        );
    }

    public function filterDocuments(Request $request)
    {
        $selectedCategory = $request->category;

        // Query documents
        $documents = DB::table('documents')
            ->join('document_histories', 'documents.id', '=', 'document_histories.document_id')
            ->where('document_histories.is_active', 1)
            ->select(
                DB::raw("'document' as type"),
                'document_histories.title_document as title',
                'document_histories.file_document as file',
                'document_histories.date_document',
                'document_histories.time_document',
                'documents.category_document as category',
                'document_histories.created_at'
            );

        // Query SOPs
        $sops = DB::table('sops')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('sop_histories', 'sops.id', '=', 'sop_histories.sop_id')
            ->where('sop_histories.is_active', 1)
            ->select(
                DB::raw("'sop' as type"),
                'sop_histories.title_document as title',
                'sop_histories.file_document as file',
                'sop_histories.date_document',
                'sop_histories.time_document',
                'documents.category_document as category',
                'sop_histories.created_at'
            );

        // Query WIs
        $wis = DB::table('wis')
            ->join('sops', 'wis.sop_id', '=', 'sops.id')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('wi_histories', 'wis.id', '=', 'wi_histories.wi_id')
            ->where('wi_histories.is_active', 1)
            ->select(
                DB::raw("'wi' as type"),
                'wi_histories.title_document as title',
                'wi_histories.file_document as file',
                'wi_histories.date_document',
                'wi_histories.time_document as time_document',
                'documents.category_document as category',
                'wi_histories.created_at'
            );

        // Query Forms
        $forms = DB::table('forms')
            ->join('wis', 'forms.wi_id', '=', 'wis.id')
            ->join('sops', 'wis.sop_id', '=', 'sops.id')
            ->join('documents', 'sops.document_id', '=', 'documents.id')
            ->join('form_histories', 'forms.id', '=', 'form_histories.form_id')
            ->where('form_histories.is_active', 1)
            ->select(
                DB::raw("'form' as type"),
                'form_histories.title_document as title',
                'form_histories.file_document as file',
                'form_histories.date_document',
                'form_histories.time_document as time_document',
                'documents.category_document as category',
                'form_histories.created_at'
            );

        // Gabungkan semua data (tanpa filter kategori!)
        $allFiles = $documents
            ->unionAll($sops)
            ->unionAll($wis)
            ->unionAll($forms);

        // Ambil 5 data terbaru (tanpa filter kategori)
        $limitFive = DB::query()
            ->fromSub($allFiles, 'all_files')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Filter kategori di PHP/Collection
        if ($selectedCategory) {
            $limitFive = collect($limitFive)->where('category', $selectedCategory)->values();
        }

        return response()->json($limitFive);
    }
}
