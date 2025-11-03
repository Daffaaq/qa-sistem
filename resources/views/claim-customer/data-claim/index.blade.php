@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">Data Claim</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">Data Claim</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="datatables mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-primary text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-data-claim"></h5>
                                    <small>Total Data Claim</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateClaim">
                                    <i class="ti ti-plus"></i> Tambah Data Claim
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="data-claim-table" class="table table-bordered table-striped nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Claim</th>
                                    <th>Customer</th>
                                    <th>Part No</th>
                                    <th>Problem</th>
                                    <th>Quantity</th>
                                    <th>Klasifikasi</th>
                                    <th>Kategori</th>
                                    <th>File Evident</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>

    {{-- Include modals nanti --}}
    @include('claim-customer.data-claim.modal-create')
    @include('claim-customer.data-claim.modal-edit')
    @include('claim-customer.data-claim.modal-preview')
@endsection

@push('styles')
    <style>
        .breadcrumb .active {
            color: #0d6efd !important;
            font-weight: 600;
        }

        .breadcrumb a {
            color: #6c757d;
        }

        .breadcrumb a:hover {
            color: #0a58ca;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/data-claim') }}"; // Using asset() for correct path generation
    </script>
    <script>
        let dataClaimTable;
        window.USER_ROLE = "{{ Auth::user()->role }}";
        $(document).ready(function() {
            dataClaimTable = $('#data-claim-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('data-claim.list') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_claim',
                        name: 'tanggal_claim'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'part_no',
                        name: 'part_no'
                    },
                    {
                        data: 'problem',
                        name: 'problem'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'klasifikasi',
                        name: 'klasifikasi'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'file_evident',
                        name: 'file_evident',
                        render: function(data) {
                            if (data) {
                                const fileUrl = `${window.BASE_URL}/${data}`;
                                return `<button class="btn btn-sm btn-info btn-preview-pdf" data-file="${fileUrl}">
                    <i class="ti ti-file"></i> Lihat
                </button>`;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            let buttons = '';
                            if (window.USER_ROLE === 'superadmin') {
                                buttons += `<button class="btn btn-sm btn-warning btn-edit" data-id="${id}">
                                        <i class="ti ti-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="${id}">
                                        <i class="ti ti-trash"></i> Hapus
                                    </button>`;
                            }
                            return buttons;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Hapus data
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('data-claim.destroy', ':id') }}".replace(':id',
                                id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.',
                                    'success');
                                dataClaimTable.ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
@endpush
