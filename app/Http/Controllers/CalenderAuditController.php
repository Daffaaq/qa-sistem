<?php

namespace App\Http\Controllers;

use App\Models\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalenderAuditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = $this->getCalendarEvents();
        $upcomingEvents = $this->getUpcomingEvents();

        return view('customer-audit.calender.index', compact('events', 'upcomingEvents'));
    }

    private function getCalendarEvents()
    {
        $customerAudits = CustomerAudit::all();
        $events = [];
        $existingColors = [];
        $today = \Carbon\Carbon::today();

        foreach ($customerAudits as $audit) {
            $color = $audit->warna_event ?? $this->generateSafePastelColor($audit->id, $existingColors);

            $startDate = \Carbon\Carbon::parse($audit->tanggal_mulai_event);
            $endDate = $audit->tanggal_selesai_event
                ? \Carbon\Carbon::parse($audit->tanggal_selesai_event)->addDay()
                : null;

            $hasEnded = $endDate ? $endDate->lt($today) : $startDate->lt($today);

            // Event utama
            $events[] = [
                'id' => $audit->id,
                'title' => $audit->nama_event,
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate ? $endDate->format('Y-m-d') : null,
                'description' => $audit->deskripsi_event ?? 'Tidak ada deskripsi',
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => '#000',
                'logo_customer' => $audit->logo_customer ? asset('documents/customer-audit/logo/' . $audit->logo_customer) : null, // tambah ini
                'extendedProps' => [
                    'pastEvent' => $hasEnded,
                ],
            ];

            // Tambahkan background highlight untuk semua event
            $events[] = [
                'id' => $audit->id . '_bg', // unik untuk background supaya tidak bentrok
                'title' => '',
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate ? $endDate->format('Y-m-d') : null,
                'display' => 'background',
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        }

        return collect($events);
    }




    private function getUpcomingEvents()
    {
        $audits = CustomerAudit::whereDate('tanggal_mulai_event', '>=', now())
            ->orderBy('tanggal_mulai_event', 'asc')
            ->get();
        $existingColors = [];
        return $audits->map(function ($event) use (&$existingColors) { // <- pakai use
            $color = $event->warna_event ?? $this->generateSafePastelColor($event->id, $existingColors);


            Log::info("Upcoming Events ID {$event->id} | Color: {$color}");

            return [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'tanggal_mulai' => \Carbon\Carbon::parse($event->tanggal_mulai_event)->translatedFormat('d F Y'),
                'tanggal_selesai' => $event->tanggal_selesai_event
                    ? \Carbon\Carbon::parse($event->tanggal_selesai_event)->translatedFormat('d F Y')
                    : '',
                'deskripsi' => $event->deskripsi_event ?? '-',
                'backgroundColor' => $color, // Pastikan backgroundColor sama
            ];
        });
    }

    /**
     * Fungsi untuk generate warna pastel acak (lembut)
     */
    private function generateSafePastelColor($id, &$existingColors = [])
    {
        do {
            // Hue: berdasarkan ID + random kecil
            $hue = ($id * 137 + rand(0, 50)) % 360;

            // Saturation: 60–85%, Lightness: 55–70% (pastel cerah)
            $saturation = 60 + rand(0, 25);
            $lightness = 55 + rand(0, 15);

            $color = [$hue, $saturation, $lightness];
        }
        // Ulang jika terlalu mirip warna yang sudah ada
        while ($this->isTooSimilar($color, $existingColors) || $this->isGrayish($color));

        // Simpan warna baru agar tidak dipakai lagi
        $existingColors[] = $color;

        return "hsl({$color[0]}, {$color[1]}%, {$color[2]}%)";
    }

    // Cek jarak warna dengan existing colors
    private function isTooSimilar($color, $existingColors, $minDistance = 30)
    {
        foreach ($existingColors as $existing) {
            $dh = abs($color[0] - $existing[0]);
            $ds = abs($color[1] - $existing[1]);
            $dl = abs($color[2] - $existing[2]);

            if ($dh < $minDistance && $ds < 10 && $dl < 10) {
                return true;
            }
        }
        return false;
    }

    // Cek apakah warna terlalu netral / abu / putih / hitam
    private function isGrayish($color)
    {
        // Saturation terlalu rendah atau lightness terlalu rendah/tinggi
        if ($color[1] < 50 || $color[2] < 50 || $color[2] > 80) {
            return true;
        }

        return false;
    }


    public function refresh()
    {
        $upcomingEvents = $this->getUpcomingEvents(); // sesuai logika kamu sebelumnya
        $events = $this->getCalendarEvents(); // sesuai logika calendar kamu

        return response()->json([
            'upcomingHtml' => view('customer-audit.partials.upcoming-events', compact('upcomingEvents'))->render(),
            'events' => $events,
        ]);
    }


    public function getEventDetail1($id)
    {
        $event = CustomerAudit::findOrFail($id);

        return response()->json([
            'id' => $event->id,
            'nama_event' => $event->nama_event,
            'tanggal_mulai' => $event->tanggal_mulai_event ? \Carbon\Carbon::parse($event->tanggal_mulai_event)->translatedFormat('d F Y') : null,
            'tanggal_selesai' => $event->tanggal_selesai_event ? \Carbon\Carbon::parse($event->tanggal_selesai_event)->translatedFormat('d F Y') : null,
            'deskripsi' => $event->deskripsi_event ?? '-',
            'file' => $event->file_path ? asset('storage/' . $event->file_path) : null, // file attachment jika ada
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
