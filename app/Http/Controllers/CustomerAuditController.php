<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerAuditRequest;
use App\Models\CustomerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class CustomerAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dataAuditList($id)
    {
        $dataAudit = DB::table('data_audits')
            ->where('customer_audits_id', $id)
            ->select('id', 'temuan', 'due_date', 'status', 'pic', 'file_evident', 'keterangan');

        return DataTables::of($dataAudit)
            ->addIndexColumn()
            ->make(true);
    }


    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('customer_audits')
                ->select(
                    'id',
                    'nama_event',
                    'tanggal_mulai_event',
                    'tanggal_selesai_event',
                    'file_evident',
                    DB::raw('(SELECT COUNT(*) FROM data_audits WHERE data_audits.customer_audits_id = customer_audits.id) as has_audit')
                )
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
        return view('customer-audit.data-customer-audit.index');
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
    public function store(StoreCustomerAuditRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle file upload jika ada
            $filename = null;
            if ($request->hasFile('file_evident')) {
                $file = $request->file('file_evident');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('documents/customer-audit'), $filename);
            }

            $customerAudit = DB::transaction(function () use ($request, $filename) {
                return CustomerAudit::create([
                    'nama_event' => $request->nama_event,
                    'tanggal_mulai_event' => $request->tanggal_mulai_event,
                    'tanggal_selesai_event' => $request->tanggal_selesai_event ?? null,
                    'deskripsi_event' => $request->deskripsi_event,
                    'file_evident' => $filename
                ]);
            });

            return response()->json(['status' => 'success', 'message' => 'Data customer audit berhasil disimpan.', 'data' => $customerAudit], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data customer audit: ' . $e->getMessage()], 500);
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
        try {
            $customerAudit = CustomerAudit::findOrFail($id);

            // Ensure the date is formatted as yyyy-mm-dd
            $customerAudit->tanggal_mulai_event = \Carbon\Carbon::parse($customerAudit->tanggal_mulai_event)->format('Y-m-d');
            $customerAudit->tanggal_selesai_event = $customerAudit->tanggal_selesai_event ? \Carbon\Carbon::parse($customerAudit->tanggal_selesai_event)->format('Y-m-d') : null;

            return response()->json([
                'id' => $customerAudit->id,
                'nama_event' => $customerAudit->nama_event,
                'tanggal_mulai_event' => $customerAudit->tanggal_mulai_event,
                'tanggal_selesai_event' => $customerAudit->tanggal_selesai_event,
                'deskripsi_event' => $customerAudit->deskripsi_event,
                'file_evident' => $customerAudit->file_evident
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data customer audit tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $customerAudit = CustomerAudit::findOrFail($id);

            // Ensure the date is formatted as yyyy-mm-dd
            $customerAudit->tanggal_mulai_event = \Carbon\Carbon::parse($customerAudit->tanggal_mulai_event)->format('Y-m-d');
            $customerAudit->tanggal_selesai_event = $customerAudit->tanggal_selesai_event ? \Carbon\Carbon::parse($customerAudit->tanggal_selesai_event)->format('Y-m-d') : null;
            return response()->json([
                'id' => $customerAudit->id,
                'nama_event' => $customerAudit->nama_event,
                'tanggal_mulai_event' => $customerAudit->tanggal_mulai_event,
                'tanggal_selesai_event' => $customerAudit->tanggal_selesai_event,
                'deskripsi_event' => $customerAudit->deskripsi_event,
                'file_evident' => $customerAudit->file_evident
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data claim tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCustomerAuditRequest $request, $id)
    {
        try {
            // Validate the incoming request
            $data = $request->validated();

            // Find the customer audit record by ID
            $customerAudit = CustomerAudit::findOrFail($id);

            // Handle file upload if a new file is uploaded
            $filename = $customerAudit->file_evident;  // Keep the existing file name by default
            if ($request->hasFile('file_evident')) {
                // Delete the old file if it exists
                if ($customerAudit->file_evident) {
                    $oldFilePath = public_path('documents/customer-audit/' . $customerAudit->file_evident);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                // Handle the new file upload
                $file = $request->file('file_evident');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('documents/customer-audit'), $filename);
            }

            // Update the customer audit record
            $customerAudit->update([
                'nama_event' => $request->nama_event,
                'tanggal_mulai_event' => $request->tanggal_mulai_event,
                'tanggal_selesai_event' => $request->tanggal_selesai_event ?? null,
                'deskripsi_event' => $request->deskripsi_event,
                'file_evident' => $filename
            ]);

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Data customer audit berhasil diperbarui.',
                'data' => $customerAudit
            ]);
        } catch (\Exception $e) {
            // Handle any errors and return failure response
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data customer audit: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $customerAudit = CustomerAudit::findOrFail($id);

            // Hapus file customer audit
            if (!empty($customerAudit->file_evident)) {
                $filePath = public_path('documents/customer-audit/' . $customerAudit->file_evident);
                if (file_exists($filePath) && is_file($filePath)) {
                    unlink($filePath);
                }
            }

            // Hapus file-data audit terkait
            foreach ($customerAudit->dataAudit as $dataAudit) {
                if (!empty($dataAudit->file_evident)) {
                    $filePath = public_path('documents/data-audit/' . $dataAudit->file_evident);
                    if (file_exists($filePath) && is_file($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Hapus record parent, data audit otomatis ikut terhapus karena cascade
            $customerAudit->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data customer audit dan file terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ]);
        }
    }
}
