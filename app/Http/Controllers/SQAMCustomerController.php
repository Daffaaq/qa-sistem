<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentHistorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SQAMCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('documents as d')
                ->leftJoin('document_histories as h', function ($join) {
                    $join->on('d.id', '=', 'h.document_id')
                        ->where('h.is_active', true);
                })
                ->select(
                    'd.id',
                    'h.title_document',
                    'h.date_document',
                    DB::raw("CONVERT(VARCHAR(8), h.time_document, 108) as time_document"),
                    'h.file_document',
                    'h.revision_number'
                )
                ->where('d.category_document', 'SQAM Customer')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return response()->json(['message' => 'Method not allowed'], 405);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $totalDocuments = DB::table('documents')
            ->where('category_document', 'SQAM Customer')
            ->count();
        return view('document.sqam-customer.index', compact('totalDocuments'));
    }


    public function count()
    {
        $totalDocuments = DB::table('documents')
            ->where('category_document', 'SQAM Customer')
            ->count();
        return response()->json(['totalDocuments' => $totalDocuments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDocumentRequest $request)
    {
        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');
            // Menggunakan string acak untuk nama file
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();  // 40 karakter string acak

            $file->move(public_path('documents/sqam-customer'), $filename);
        } else {
            $filename = null;
        }

        $document = Document::create([
            'category_document' => 'SQAM Customer',
        ]);

        DocumentHistorie::create([
            'document_id' => $document->id,
            'title_document' => $request->title_document,
            'file_document' => $filename,
            'date_document' => now()->toDateString(),
            'time_document' => now()->toTimeString(),
            'revision_number' => 0,
            'is_active' => true,
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen SQAM Customer berhasil disimpan'
            ]);
        }
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
        $document = Document::with(['histories' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        $history = $document->histories->first();

        return response()->json([
            'id' => $document->id,
            'title_document' => $history->title_document ?? '',
            'file_document' => $history->file_document ?? '',
            'category_document' => $document->category_document,
        ]);
    }

    public function update(UpdateDocumentRequest $request, $id)
    {
        // dd($request->method(), $request->_method);
        $document = Document::findOrFail($id);

        $history = DocumentHistorie::where('document_id', $document->id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($request->hasFile('file_document')) {
            // Hapus file lama
            if ($history->file_document && file_exists(public_path('documents/sqam-customer/' . $history->file_document))) {
                unlink(public_path('documents/sqam-customer/' . $history->file_document));
            }

            $file = $request->file('file_document');
            // Menggunakan string acak untuk nama file
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();  // 40 karakter string acak

            $file->move(public_path('documents/sqam-customer'), $filename);
            // Simpan path file yang baru
        } else {
            $filename = $history->file_document;  // Gunakan file lama jika tidak ada file baru
        }

        $history->update([
            'title_document' => $request->title_document,
            'file_document' => $filename,
            'date_document' => now()->format('Y-m-d'),
            'time_document' => now()->format('H:i:s'),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Dokumen SQAM Customer berhasil diperbarui']);
        }
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $revisions = DocumentHistorie::where('document_id', $id)->get();

        // Hapus file dari setiap revisi (jika ada)
        foreach ($revisions as $revision) {
            if ($revision->file_document && file_exists(public_path('documents/sqam-customer/' . $revision->file_document))) {
                unlink(public_path('documents/sqam-customer/' . $revision->file_document));
            }
        }

        DocumentHistorie::where('document_id', $id)->delete();
        $document->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dokumen dan semua revisi SQAM Customer berhasil dihapus',
            ]);
        }
    }

    public function revisi($id)
    {
        $document = Document::with(['histories' => function ($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        $history = $document->histories->first();

        return response()->json([
            'id' => $document->id,
            'category_document' => $document->category_document,
            'title_document' => $history->title_document ?? '',
            'file_document' => $history->file_document ?? '',
            'date_document' => $history->date_document ?? '',
            'time_document' => $history->time_document ?? '',
        ]);
    }

    public function storeRevisi(StoreDocumentRequest $request, $id)
    {
        $document = Document::findOrFail($id);

        DocumentHistorie::where('document_id', $id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        if ($request->hasFile('file_document')) {
            $file = $request->file('file_document');

            // Menggunakan string acak untuk nama file
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();  // 40 karakter string acak

            $file->move(public_path('documents/sqam-customer'), $filename);
        } else {
            $filename = null;
        }

        $latestRevision = DocumentHistorie::where('document_id', $id)->max('revision_number');
        $nextRevision = $latestRevision ? $latestRevision + 1 : 1;

        $prevTitle = DocumentHistorie::where('document_id', $id)
            ->orderByDesc('revision_number')
            ->value('title_document');

        DocumentHistorie::create([
            'document_id' => $document->id,
            'title_document' => $prevTitle,
            'file_document' => $filename,
            'date_document' => now()->format('Y-m-d'),
            'time_document' => now()->format('H:i:s'),
            'revision_number' => $nextRevision,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Revisi dokumen SQAM Customer berhasil ditambahkan']);
    }

    public function allRevisions()
    {
        $histories = DocumentHistorie::with('document')
            ->whereHas('document', function ($query) {
                $query->where('category_document', 'SQAM Customer');
            })
            ->orderBy('document_id')
            ->orderByDesc('revision_number')
            ->get();

        $documents = [];

        foreach ($histories as $history) {
            $docId = $history->document_id;

            if (!isset($documents[$docId])) {
                $documents[$docId] = [
                    'document_title' => null,
                    'revisions' => [],
                ];
            }

            $documents[$docId]['revisions'][] = [
                'revision_number' => $history->revision_number,
                'title_document' => $history->title_document,
                'file_document' => $history->file_document,
                'date_document' => $history->date_document,
                'time_document' => $history->time_document,
                'is_active' => $history->is_active,
            ];

            if ($history->is_active) {
                $documents[$docId]['document_title'] = $history->title_document;
            }
        }

        foreach ($documents as $docId => &$doc) {
            if (!$doc['document_title'] && count($doc['revisions']) > 0) {
                $doc['document_title'] = $doc['revisions'][0]['title_document'];
            }
        }

        return response()->json([
            'documents' => $documents,
        ]);
    }
}
