@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Judul di kiri -->
                    <h4 class="fw-semibold mb-0">Manual Mutu</h4>

                    <!-- Breadcrumb di kanan -->
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            @if (request()->routeIs('dashboard'))
                                <span class="active text-primary fw-semibold">Dashboard</span>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item">
                            @if (request()->routeIs('manual-mutu.index'))
                                <span class="active text-primary fw-semibold">Manual Mutu</span>
                            @else
                                <a href="{{ route('manual-mutu.index') }}" class="text-muted">Manual Mutu</a>
                            @endif
                        </li>
                    </ol>

                </div>
            </div>
        </div>

        <section class="datatables">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-primary text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-documents">{{ $totalDocuments }}</h5>
                                    <small>Dokumen Manual Mutu</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateManualMutu">
                                    <i class="ti ti-plus"></i> Tambah
                                </button>
                            @endif
                            <button type="button" class="btn btn-secondary" id="btnShowAllRevisions">
                                <i class="ti ti-history"></i> Lihat Semua Revisi
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive m-t-40">
                        <table id="manual-mutu-table" class="table border display table-bordered table-striped no-wrap"
                            style="width: 100%">
                            <thead>
                                <!-- start row -->
                                <tr>
                                    <th>No</th>
                                    <th>Judul Dokumen</th>
                                    <th>Revisi Ke</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                                <!-- end row -->
                            </thead>
                            <tbody>

                                <!-- end row -->
                            </tbody>
                        </table>
                    </div>
                </div>
                </di>
        </section>

    </div>

    @include('document.manual-mutu.modal-preview')
    @include('document.manual-mutu.modal-create')
    @include('document.manual-mutu.modal-edit')
    @include('document.manual-mutu.modal-revisi')
    @include('document.manual-mutu.modal-show-revisi')
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

        .card .fs-8 {
            font-size: 2.5rem !important;
        }
    </style>
@endpush


@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/manual-mutu') }}"; // Using asset() for correct path generation
    </script>
    <script>
        var manualMutuTable;
        window.USER_ROLE = "{{ Auth::user()->role }}";
        $(document).ready(function() {
            manualMutuTable = $('#manual-mutu-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('manual-mutu.list') }}",
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
                        render: function(data, type, row) {
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
                            let btnEdit = '';
                            let btnDelete = '';
                            let btnRevisi = '';

                            if (window.USER_ROLE === 'superadmin') {
                                btnEdit = `<button class="btn btn-sm btn-warning me-1 btn-edit-manual-mutu" data-id="${id}">
        <i class="ti ti-pencil"></i> Edit
    </button>`;

                                btnRevisi = `<button class="btn btn-sm btn-primary me-1 btn-revisi-manual-mutu" data-id="${id}">
        <i class="ti ti-refresh"></i> Revisi
    </button>`;

                                btnDelete = `<button class="btn btn-sm btn-danger btn-delete" data-id="${id}">
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
                        let url = "{{ route('manual-mutu.destroy', ':id') }}";
                        url = url.replace(':id', id);
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message
                                    });

                                    manualMutuTable.ajax.reload();

                                    // Panggil endpoint count untuk update total dokumen
                                    $.get("{{ route('manual-mutu.count') }}",
                                        function(
                                            data) {
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
                            error: function(xhr) {
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
