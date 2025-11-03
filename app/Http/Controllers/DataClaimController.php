<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDataClaimRequest;
use App\Http\Requests\UpdateDataClaimRequest;
use App\Models\DataClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class DataClaimController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('data_claims')
                ->select('id', 'tanggal_claim', 'customer', 'part_no', 'problem', 'quantity', 'klasifikasi', 'kategori', 'file_evident')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('claim-customer.data-claim.index');
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
    public function store(StoreDataClaimRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle file upload jika ada
            $filename = null;
            if ($request->hasFile('file_evident')) {
                $file = $request->file('file_evident');
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('documents/data-claim'), $filename);
            }

            $claim = DB::transaction(function () use ($request, $filename) {
                return DataClaim::create([
                    'tanggal_claim' => $request->tanggal_claim,
                    'customer'      => $request->customer,
                    'part_no'       => $request->part_no,
                    'problem'       => $request->problem,
                    'quantity'      => $request->quantity,
                    'klasifikasi'   => $request->klasifikasi,
                    'kategori'      => $request->kategori,
                    'file_evident'  => $filename
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Data claim berhasil disimpan.',
                'data'    => $claim
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error'   => $e->getMessage()
            ], 500);
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
        try {
            $claim = DataClaim::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data'   => $claim
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
    public function update(UpdateDataClaimRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $claim = DataClaim::findOrFail($id);
            $filename = $claim->file_evident; // file lama

            // 🔹 Jika user upload file baru
            if ($request->hasFile('file_evident')) {
                $file = $request->file('file_evident');
                $newFilename = Str::random(40) . '.' . $file->getClientOriginalExtension();

                // Hapus file lama jika ada
                if ($filename && file_exists(public_path('documents/data-claim/' . $filename))) {
                    unlink(public_path('documents/data-claim/' . $filename));
                }

                // Upload file baru
                $file->move(public_path('documents/data-claim'), $newFilename);
                $filename = $newFilename;
            }

            DB::transaction(function () use ($claim, $request, $filename) {
                $claim->update([
                    'tanggal_claim' => $request->tanggal_claim,
                    'customer'      => $request->customer,
                    'part_no'       => $request->part_no,
                    'problem'       => $request->problem,
                    'quantity'      => $request->quantity,
                    'klasifikasi'   => $request->klasifikasi,
                    'kategori'      => $request->kategori,
                    'file_evident'  => $filename
                ]);
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Data claim berhasil diperbarui.',
                'data'    => $claim
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error'   => $e->getMessage()
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
        // mulai transaksi
        DB::beginTransaction();
        try {
            $claim = DataClaim::findOrFail($id);
            // Hapus file lama jika ada
            if ($claim && file_exists(public_path('documents/data-claim/' . $claim->file_evident))) {
                unlink(public_path('documents/data-claim/' . $claim->file_evident));
            }
            $claim->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data claim deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the data claim: ' . $e->getMessage()]);
        }
    }
}
