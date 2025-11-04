<?php

namespace App\Http\Controllers;

use App\Http\Requests\RevisiDocumentRequest;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Form;
use App\Models\FormHistorie;
use App\Models\Sop;
use App\Models\SopHistorie;
use App\Models\Wi;
use App\Models\WiHistorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ManagementRepresentativeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');  // pastikan ini nama kolom dan value-nya sesuai
        })->with([
            'histories' => fn($q) => $q->where('is_active', true),
            'wis.histories' => fn($q) => $q->where('is_active', true),
            'wis.forms.histories' => fn($q) => $q->where('is_active', true),
        ])->get();

        // Hitung jumlah data
        $sopCount = $sops->count(); // Jumlah SOP

        $wiCount = $sops->sum(function ($sop) {
            return $sop->wis->count();
        });

        $formCount = $sops->sum(function ($sop) {
            return $sop->wis->sum(function ($wi) {
                return $wi->forms->count();
            });
        });

        return view('document-quality-sop.management-representative.index', compact('sops', 'sopCount', 'wiCount', 'formCount'));
    }

    public function countSop()
    {
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');  // pastikan ini nama kolom dan value-nya sesuai
        })->with([
            'histories' => fn($q) => $q->where('is_active', true),
            'wis.histories' => fn($q) => $q->where('is_active', true),
            'wis.forms.histories' => fn($q) => $q->where('is_active', true),
        ])->get();
        // Hitung jumlah data
        $sopCount = $sops->count(); // Jumlah SOP

        return response()->json(['sopCount' => $sopCount]);
    }

    public function countWi()
    {
        // Hitung total WI dari semua SOP yang sesuai kategori
        $wis = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');
        })->with('wis')->get();

        $wiCount = $wis->sum(function ($sop) {
            return $sop->wis->count();
        });

        return response()->json(['wiCount' => $wiCount]);
    }

    public function countForm()
    {
        // Hitung total Form dari semua WI yang termasuk dalam SOP kategori yang dimaksud
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');
        })->with('wis.forms')->get();

        $formCount = $sops->sum(function ($sop) {
            return $sop->wis->sum(function ($wi) {
                return $wi->forms->count();
            });
        });

        return response()->json(['formCount' => $formCount]);
    }

    public function countAll()
    {
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');
        })->with(['wis.forms'])->get();

        $sopCount = $sops->count();
        $wiCount = $sops->sum(fn($sop) => $sop->wis->count());
        $formCount = $sops->sum(fn($sop) => $sop->wis->sum(fn($wi) => $wi->forms->count()));

        return response()->json([
            'sopCount' => $sopCount,
            'wiCount' => $wiCount,
            'formCount' => $formCount,
        ]);
    }


    public function contentListPartial()
    {
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');  // pastikan ini nama kolom dan value-nya sesuai
        })->with([
            'histories' => fn($q) => $q->where('is_active', true),
            'wis.histories' => fn($q) => $q->where('is_active', true),
            'wis.forms.histories' => fn($q) => $q->where('is_active', true),
        ])->get();

        return view('document-quality-sop.management-representative.partials.content-list', compact('sops'));
    }

    public function storeSOP(StoreDocumentRequest $request)
    {
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/sop/' . $filename; // Path lengkap

            $file->move(public_path('documents/representative/sop'), $filename); // Menggunakan $path yang sudah mengandung 'documents/sop/'

        } else {
            $path = null;
        }

        $document = Document::create([
            'category_document' => 'Management Representative', // Bisa dinamis kalau diperlukan
        ]);

        $sop = Sop::create([
            'document_id' => $document->id,
        ]);

        SopHistorie::create([
            'sop_id' => $sop->id,
            'title_document' => $request->title_document,
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => 0,
            'is_active' => true, // Revisi pertama aktif
        ]);

        // Return response
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen SOP Bagian Management Representative berhasil disimpan'
            ]);
        }
    }

    public function editSOP(Sop $sop)
    {
        $history = $sop->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $sop->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
        ]);
    }

    public function updateSOP(UpdateDocumentRequest $request, Sop $sop)
    {
        // Ambil revisi aktif
        $activeHistory = $sop->histories()->where('is_active', true)->first();

        if (!$activeHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }

        // Cek apakah ada file baru yang di-upload
        if ($request->hasFile('file_document')) {

            // Opsional: hapus file lama
            // Hapus file lama (jika ada)
            if ($activeHistory->file_document && file_exists(public_path('documents/representative/' . $activeHistory->file_document))) {
                unlink(public_path('documents/representative/' . $activeHistory->file_document));
            }

            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/sop/' . $filename; // Path lengkap

            $file->move(public_path('documents/representative/sop'), $filename);

            $activeHistory->file_document = $path;
        }

        // Update data lainnya
        $activeHistory->title_document = $request->title_document;
        $activeHistory->date_document = now()->toDateString();
        $activeHistory->time_document = now()->toTimeString();

        $activeHistory->save();

        // Return response
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Revisi aktif SOP berhasil diperbarui.'
            ]);
        }

        return back()->with('success', 'Revisi aktif SOP berhasil diperbarui.');
    }

    //destroy
    public function destroySOP(Sop $sop)
    {
        // Hapus semua WI
        foreach ($sop->wis as $wi) {
            // Hapus semua Form di WI ini
            foreach ($wi->forms as $form) {
                foreach ($form->histories as $formHistory) {
                    // Cek dan hapus file dari folder 'public/documents/form/'
                    if ($formHistory->file_document && file_exists(public_path('documents/representative/' . $formHistory->file_document))) {
                        unlink(public_path('documents/representative/' . $formHistory->file_document)); // Menggunakan unlink
                    }
                }
                $form->histories()->delete();
                $form->delete();
            }

            // Hapus semua histori WI
            foreach ($wi->histories as $wiHistory) {
                // Cek dan hapus file dari folder 'public/documents/wi/'
                if ($wiHistory->file_document && file_exists(public_path('documents/representative/' . $wiHistory->file_document))) {
                    unlink(public_path('documents/representative/' . $wiHistory->file_document)); // Menggunakan unlink
                }
            }
            $wi->histories()->delete();
            $wi->delete();
        }

        // Hapus histori SOP
        foreach ($sop->histories as $sopHistory) {
            // Cek dan hapus file dari folder 'public/documents/sop/'
            if ($sopHistory->file_document && file_exists(public_path('documents/representative/' . $sopHistory->file_document))) {
                unlink(public_path('documents/representative/' . $sopHistory->file_document)); // Menggunakan unlink
            }
        }
        $sop->histories()->delete();

        // Hapus SOP dan dokumen induknya
        $sop->delete();
        if ($sop->document) {
            $sop->document->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'SOP dan seluruh WI & Form terkait berhasil dihapus.'
        ]);
    }

    public function revisiSOP(Sop $sop)
    {
        $history = $sop->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $sop->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
            'date_document' => $history->date_document,
            'time_document' => $history->time_document
        ]);
    }

    public function reviseSOP(RevisiDocumentRequest $request, Sop $sop)
    {
        // Nonaktifkan revisi aktif saat ini
        $currentActive = $sop->histories()->where('is_active', true)->first();
        if (!$currentActive) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }
        $currentActive->update(['is_active' => false]);


        $previousTitle = $currentActive->title_document;

        // Hitung revisi terakhir
        $lastRevisionNumber = $sop->histories()->max('revision_number');

        // Simpan file baru
        $file = $request->file('file_document');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Simpan file dengan path lengkap
        $path = '/sop/' . $filename; // Path lengkap

        $file->move(public_path('documents/representative/sop'), $filename);

        // Tambah histori baru
        $newHistory = $sop->histories()->create([
            'title_document' => $previousTitle,
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => $lastRevisionNumber + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Revisi SOP berhasil ditambahkan.',
        ]);
    }

    public function storeWI(StoreDocumentRequest $request, $id)
    {
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/wi/' . $filename; // Path relatif untuk Wi

            // Simpan file ke public/documents/wi/
            $file->move(public_path('documents/representative/wi'), $filename);
        } else {
            $path = null;
        }

        $sop = Sop::find($id);

        $wi = Wi::create([
            'sop_id' => $sop->id,
        ]);

        WiHistorie::create([
            'wi_id' => $wi->id,
            'title_document' => $request->title_document,
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => 0,
            'is_active' => true, // Revisi pertama aktif
        ]);

        // Return response
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen WI Bagian Representative Management berhasil disimpan'
            ]);
        }
    }

    public function editWI(Wi $wi)
    {
        $history = $wi->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $wi->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
        ]);
    }

    public function updateWI(UpdateDocumentRequest $request, Wi $wi)
    {
        $activeHistory = $wi->histories()->where('is_active', true)->first();

        if (!$activeHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }

        // Handle file baru
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/wi/' . $filename; // Path relatif

            // Simpan file ke public/documents/wi/
            $file->move(public_path('documents/representative/wi'), $filename);

            // Hapus file lama (jika ada)
            if ($activeHistory->file_document && file_exists(public_path('documents/representative/' . $activeHistory->file_document))) {
                unlink(public_path('documents/representative/' . $activeHistory->file_document));
            }

            $activeHistory->file_document = $path;
        }

        // Update data lainnya
        $activeHistory->title_document = $request->title_document;
        $activeHistory->date_document = now()->toDateString();
        $activeHistory->time_document = now()->toTimeString();
        $activeHistory->save();

        return response()->json([
            'success' => true,
            'message' => 'Revisi aktif WI berhasil diperbarui.'
        ]);
    }

    public function destroyWI(Wi $wi)
    {
        // Hapus semua form yang terhubung ke WI ini
        foreach ($wi->forms as $form) {
            // Hapus file histori form
            foreach ($form->histories as $formHistory) {
                if ($formHistory->file_document && file_exists(public_path('documents/representative/' . $formHistory->file_document))) {
                    unlink(public_path('documents/representative/' . $formHistory->file_document)); // Menggunakan unlink
                }
            }

            // Hapus histori form & form
            $form->histories()->delete();
            $form->delete();
        }

        // Hapus file histori WI
        foreach ($wi->histories as $wiHistory) {
            if ($wiHistory->file_document && file_exists(public_path('documents/representative/' . $wiHistory->file_document))) {
                unlink(public_path('documents/representative/' . $wiHistory->file_document)); // Menggunakan unlink
            }
        }

        // Hapus histori WI & WI itu sendiri
        $wi->histories()->delete();
        $wi->delete();

        return response()->json([
            'success' => true,
            'message' => 'WI dan semua form terkait berhasil dihapus.'
        ]);
    }


    public function revisiWI(Wi $wi)
    {
        $history = $wi->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $wi->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
            'date_document' => $history->date_document,
            'time_document' => $history->time_document
        ]);
    }

    public function reviseWI(RevisiDocumentRequest $request, Wi $wi)
    {
        // Nonaktifkan revisi aktif saat ini
        $currentActive = $wi->histories()->where('is_active', true)->first();

        if (!$currentActive) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }

        // Nonaktifkan revisi lama
        $currentActive->update(['is_active' => false]);

        // Hitung nomor revisi terakhir
        $lastRevisionNumber = $wi->histories()->max('revision_number');

        // Simpan file baru
        $file = $request->file('file_document');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = '/wi/' . $filename; // Path relatif

        // Simpan file ke public/documents/wi/
        $file->move(public_path('documents/representative/wi'), $filename);

        // Simpan histori revisi baru
        $wi->histories()->create([
            'title_document' => $currentActive->title_document, // pakai judul sebelumnya
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => $lastRevisionNumber + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Revisi WI berhasil ditambahkan.',
        ]);
    }

    public function storeForm(StoreDocumentRequest $request, $id)
    {
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/form/' . $filename; // Path relatif

            // Simpan file ke public/documents/form/
            $file->move(public_path('documents/representative/form'), $filename);
        } else {
            $path = null;
        }

        $wi = Wi::find($id);

        $form = Form::create([
            'wi_id' => $wi->id,
        ]);

        FormHistorie::create([
            'form_id' => $form->id,
            'title_document' => $request->title_document,
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => 0,
            'is_active' => true, // Revisi pertama aktif
        ]);

        // Return response
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen Form Bagian QA QC berhasil disimpan'
            ]);
        }
    }

    public function editForm(Form $form)
    {
        $history = $form->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $form->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
        ]);
    }

    public function updateForm(UpdateDocumentRequest $request, Form $form)
    {
        $activeHistory = $form->histories()->where('is_active', true)->first();

        if (!$activeHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }

        // Jika upload file baru
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = '/form/' . $filename; // Path relatif

            // Simpan file ke public/documents/form/
            $file->move(public_path('documents/representative/form'), $filename);

            // Hapus file lama jika ada
            if ($activeHistory->file_document && file_exists(public_path('documents/representative/' . $activeHistory->file_document))) {
                unlink(public_path('documents/representative/' . $activeHistory->file_document));
            }

            $activeHistory->file_document = $path;
        }

        // Update data lainnya
        $activeHistory->title_document = $request->title_document;
        $activeHistory->date_document = now()->toDateString();
        $activeHistory->time_document = now()->toTimeString();

        $activeHistory->save();

        return response()->json([
            'success' => true,
            'message' => 'Revisi aktif Form berhasil diperbarui.'
        ]);
    }

    public function destroyForm(Form $form)
    {
        // Hapus semua file FormHistory
        foreach ($form->histories as $history) {
            if ($history->file_document && file_exists(public_path('documents/representative/' . $history->file_document))) {
                unlink(public_path('documents/representative/' . $history->file_document)); // Menggunakan unlink
            }
        }

        // Hapus semua histori form
        $form->histories()->delete();

        // Hapus form-nya
        $form->delete();

        return response()->json([
            'success' => true,
            'message' => 'Form dan riwayatnya berhasil dihapus.'
        ]);
    }

    public function revisiForm(Form $form)
    {
        $history = $form->histories()->where('is_active', true)->first();

        if (!$history) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json([
            'id' => $form->id,
            'title_document' => $history->title_document,
            'file_document' => $history->file_document,
            'date_document' => $history->date_document,
            'time_document' => $history->time_document
        ]);
    }

    public function reviseForm(RevisiDocumentRequest $request, Form $form)
    {
        // Ambil revisi aktif saat ini
        $currentActive = $form->histories()->where('is_active', true)->first();

        if (!$currentActive) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi aktif tidak ditemukan.'
            ], 404);
        }

        // Nonaktifkan revisi aktif lama
        $currentActive->update(['is_active' => false]);

        // Hitung nomor revisi terakhir
        $lastRevisionNumber = $form->histories()->max('revision_number');

        // Simpan file baru
        $file = $request->file('file_document');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = '/form/' . $filename; // Path relatif

        // Simpan file ke public/documents/form/
        $file->move(public_path('documents/representative/form'), $filename);

        // Simpan histori baru
        $form->histories()->create([
            'title_document' => $currentActive->title_document, // Tetap pakai judul sebelumnya
            'file_document' => $path,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => $lastRevisionNumber + 1,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Revisi Form berhasil ditambahkan.',
        ]);
    }

    public function showAllRevisions()
    {
        $sops = Sop::whereHas('document', function ($query) {
            $query->where('category_document', 'Management Representative');
        })
            ->with([
                'histories' => function ($q) {
                    $q->orderBy('revision_number');
                },
                'wis.histories' => function ($q) {
                    $q->orderBy('revision_number');
                },
                'wis.forms.histories' => function ($q) {
                    $q->orderBy('revision_number');
                },
                'document'
            ])
            ->get();

        $data = $sops->map(function ($sop) {
            return [
                'type' => 'sop',
                'sop_id' => $sop->id,
                'title' => optional($sop->histories->last())->title_document,
                'revisions' => $sop->histories->map(function ($rev) {
                    return [
                        'revision_number' => $rev->revision_number,
                        'title_document' => $rev->title_document,
                        'file_document' => $rev->file_document ? asset('documents/representative/' . ltrim($rev->file_document, '/')) : null,  // Perbaikan: menggunakan 'documents/' dan menghapus '/' dari awal file_path
                        'date_document' => $rev->date_document,
                        'time_document' => $rev->time_document,
                        'is_active' => $rev->is_active,
                    ];
                }),
                'wis' => $sop->wis->map(function ($wi) {
                    return [
                        'type' => 'wi',
                        'wi_id' => $wi->id,
                        'title' => optional($wi->histories->last())->title_document,
                        'revisions' => $wi->histories->map(function ($rev) {
                            return [
                                'revision_number' => $rev->revision_number,
                                'title_document' => $rev->title_document,
                                'file_document' => $rev->file_document ? asset('documents/representative/' . ltrim($rev->file_document, '/')) : null,  // Perbaikan
                                'date_document' => $rev->date_document,
                                'time_document' => $rev->time_document,
                                'is_active' => $rev->is_active,
                            ];
                        }),
                        'forms' => $wi->forms->map(function ($form) {
                            return [
                                'type' => 'form',
                                'form_id' => $form->id,
                                'title' => optional($form->histories->last())->title_document,
                                'revisions' => $form->histories->map(function ($rev) {
                                    return [
                                        'revision_number' => $rev->revision_number,
                                        'title_document' => $rev->title_document,
                                        'file_document' => $rev->file_document ? asset('documents/representative/' . ltrim($rev->file_document, '/')) : null,  // Perbaikan
                                        'date_document' => $rev->date_document,
                                        'time_document' => $rev->time_document,
                                        'is_active' => $rev->is_active,
                                    ];
                                }),
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
