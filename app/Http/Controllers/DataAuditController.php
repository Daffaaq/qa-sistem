<?php

namespace App\Http\Controllers;

use App\Models\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DataAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dataAuditForm($id)
    {
        $dataCustomerAudit = CustomerAudit::findOrFail($id);

        return view('customer-audit.data-audit.data-audit-form', compact('dataCustomerAudit'));
    }

    public function storeDataAudit(Request $request, $id)
    {
        $dataCustomerAudit = CustomerAudit::findOrFail($id);

        $request->validate([
            'temuan' => 'required|array|min:1',
            'temuan.*' => 'required|string|max:5000',
            'due_date' => 'required|array|min:1',
            'due_date.*' => 'required|date',
            'pic' => 'required|array|min:1',
            'pic.*' => 'required|string|max:255',
            'file_evident' => 'array',
            'file_evident.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'keterangan' => 'array',
            'keterangan.*' => 'nullable|string|max:5000',
        ], [
            'temuan.required' => 'Temuan harus diisi minimal satu.',
            'temuan.*.required' => 'Kolom Temuan tidak boleh kosong.',
            'due_date.*.required' => 'Kolom Due Date tidak boleh kosong.',
            'pic.*.required' => 'Kolom PIC tidak boleh kosong.',
            'file_evident.*.file' => 'File Evident harus berupa file yang valid.',
            'keterangan.*.max' => 'Keterangan maksimal 5000 karakter.',
        ]);


        foreach ($request->temuan as $i => $temuan) {

            $filename = null;
            $status = 'Open'; // default

            if ($request->hasFile("file_evident.$i")) {
                $file = $request->file("file_evident.$i");

                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('documents/data-audit'), $filename);

                $status = 'Closed'; // ubah status jadi Closed kalau ada file
            }

            DB::table('data_audits')->insert([
                'temuan' => $temuan,
                'due_date' => $request->due_date[$i],
                'status' => $status, // ← pakai variabel status
                'pic' => $request->pic[$i],
                'file_evident' => $filename,
                'customer_audits_id' => $dataCustomerAudit->id,
                'keterangan' => $request->keterangan[$i] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        return redirect()->route('customer-audit.index')
            ->with('success', 'Data audit berhasil disimpan.');
    }

    public function editDataAudit($id)
    {
        $dataAudit = DB::table('data_audits')
            ->where('id', $id)
            ->first();
        if (!$dataAudit) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data audit tidak ditemukan.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data'   => $dataAudit
        ]);
    }

    public function updateDataAudit(Request $request, $id)
    {
        $request->validate([
            'temuan' => 'required|string|max:5000',
            'due_date' => 'required|date',
            'pic' => 'required|string|max:255',
            'file_evident' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'keterangan' => 'nullable|string|max:5000',
        ]);

        $dataAudit = DB::table('data_audits')
            ->where('id', $id)
            ->first();

        if (!$dataAudit) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data audit tidak ditemukan.',
            ], 404);
        }

        // Handle file upload jika ada
        $filename = $dataAudit->file_evident;
        $status = $dataAudit->status; // default ambil dari database

        if ($request->hasFile('file_evident')) {
            $file = $request->file('file_evident');
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/data-audit'), $filename);

            // Jika ada file, status otomatis Closed
            $status = 'Closed';
        } else {
            // Jika tidak ada file, status otomatis Open
            if (!$filename) {
                $status = 'Open';
            }
        }

        DB::table('data_audits')
            ->where('id', $id)
            ->update([
                'temuan' => $request->temuan,
                'due_date' => $request->due_date,
                'pic' => $request->pic,
                'file_evident' => $filename,
                'keterangan' => $request->keterangan,
                'status' => $status,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data audit berhasil diperbarui.',
        ]);
    }


    public function destroyDataAudit($id)
    {
        $dataAudit = DB::table('data_audits')
            ->where('id', $id)
            ->first();
        if (!$dataAudit) {
            return response()->json([
                'success'  => false,
                'message' => 'Data audit tidak ditemukan.',
            ], 404);
        }

        if ($dataAudit->file_evident) {
            $filePath = public_path('documents/data-audit/' . $dataAudit->file_evident);
            if (file_exists($filePath)) {
                @unlink($filePath); // @ untuk mencegah error jika file tidak bisa dihapus
            }
        }

        DB::table('data_audits')
            ->where('id', $id)
            ->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data audit berhasil dihapus.',
        ]);
    }
}
