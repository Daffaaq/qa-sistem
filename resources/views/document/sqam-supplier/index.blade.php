@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Judul -->
                    <h4 class="fw-semibold mb-0">SQAM Supplier</h4>

                    <!-- Breadcrumb -->
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">SQAM Supplier</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="datatables">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        {{-- Card Statistik --}}
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-primary text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-documents">{{ $totalDocuments }}</h5>
                                    <small>Dokumen SQAM
                                        Supplier</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateSQAMSupplier">
                                    <i class="ti ti-plus"></i> Tambah
                                </button>
                            @endif
                            <button type="button" class="btn btn-secondary" id="btnShowAllRevisions">
                                <i class="ti ti-history"></i> Lihat Semua Revisi
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table id="sqam-supplier-table" class="table border display table-bordered table-striped no-wrap"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Dokumen</th>
                                    <th>Revisi Ke</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>File</th>
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

    {{-- Include modal (buat nanti) --}}
    @include('document.sqam-supplier.modal-preview')
    @include('document.sqam-supplier.modal-create')
    @include('document.sqam-supplier.modal-edit')
    @include('document.sqam-supplier.modal-revisi')
    @include('document.sqam-supplier.modal-show-revisi')
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
        window.BASE_URL = "{{ asset('documents/sqam-supplier') }}"; // Using asset() for correct path generation
    </script>
    <script>
        var sqamSupplierTable;
        window.USER_ROLE = "{{ Auth::user()->role }}";
        $(document).ready(function() {
            sqamSupplierTable = $('#sqam-supplier-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sqam-supplier.list') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title_document',
                        name: 'title_document'
                    },
                    {
                        data: 'revision_number',
                        name: 'revision_number',
                        render: function(data) {
                            return data ? `Revisi ${data}` : '-';
                        }
                    },
                    {
                        data: 'date_document',
                        name: 'date_document'
                    },
                    {
                        data: 'time_document',
                        name: 'time_document'
                    },
                    {
                        data: 'file_document',
                        name: 'file_document',
                        render: function(data) {
                            if (data) {
                                const fileUrl = `${window.BASE_URL}/${data}`;
                                return `
                <button class="btn btn-sm btn-info btn-preview-pdf" data-file="${fileUrl}">
                    <i class="ti ti-file"></i> Lihat
                </button>
            `;
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            let btnEdit = '';
                            let btnDelete = '';
                            let btnRevisi = '';
                            if (window.USER_ROLE === 'superadmin') {
                                btnEdit = `
        <button class="btn btn-sm btn-warning me-1 btn-edit-sqam-supplier" data-id="${id}">
            <i class="ti ti-pencil"></i> Edit
        </button>`;

                                btnRevisi = `
        <button class="btn btn-sm btn-primary me-1 btn-revisi-sqam-supplier" data-id="${id}">
            <i class="ti ti-refresh"></i> Revisi
        </button>`;

                                btnDelete = `
        <button class="btn btn-sm btn-danger btn-delete" data-id="${id}">
            <i class="ti ti-trash"></i> Hapus
        </button>`;
                            }
                            return btnEdit + btnRevisi + btnDelete;
                        },

                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Tombol hapus
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus dokumen ini?',
                    text: "Semua data revisi dan file terkait akan ikut terhapus dan tidak bisa dikembalikan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Using the route helper to get the correct URL
                        $.ajax({
                            url: "{{ route('sqam-supplier.destroy', ':id') }}".replace(
                                ':id', id), // Use named route here
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.close();
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message
                                    });
                                    sqamSupplierTable.ajax.reload();
                                    // Panggil endpoint count untuk update total dokumen
                                    $.get("{{ route('sqam-supplier.count') }}",
                                        function(data) {
                                            $('#total-documents').text(data
                                                .totalDocuments);
                                        });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: response.message ||
                                            'Terjadi kesalahan.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.close();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat menghubungi server.'
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
