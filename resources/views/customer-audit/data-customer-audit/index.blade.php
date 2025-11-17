@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">Data Customer Audit</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">Data Customer Audit</span>
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
                                    <h5 class="mb-0" id="total-data-customer-audit"></h5>
                                    <small>Total Data Customer Audit</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            @if (Auth::user() && Auth::user()->role === 'superadmin')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalCreateCustomerAudit">
                                    <i class="ti ti-plus"></i> Tambah Data Customer Audit
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="data-customer-audit-table" class="table table-bordered table-striped nowrap"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Event</th>
                                    <th>Tanggal Mulai Event</th>
                                    <th>Tanggal Selesai Event</th>
                                    <th>File Evident</th>
                                    <th>Data Audit</th>
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

    {{-- Include modals later --}}
    @include('customer-audit.data-customer-audit.modal-create')
    @include('customer-audit.data-customer-audit.modal-edit')
    @include('customer-audit.data-customer-audit.modal-show')
    @include('customer-audit.data-customer-audit.modal-preview')
    @include('customer-audit.data-audit.data-audit-edit')
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

        #data-customer-audit-table th,
        #data-customer-audit-table td {
            text-align: center;
            /* Menengah teks di seluruh kolom */
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/customer-audit') }}"; // Using asset() for correct path generation
        window.DATA_AUDIT_BASE_URL = "{{ asset('documents/data-audit') }}";
    </script>
    <script>
        let dataCustomerAuditTable;
        window.USER_ROLE = "{{ Auth::user()->role }}";
        window.ROUTES = {
            dataAuditList: "{{ route('customer-audit.data-audit-list', ':id') }}"
        };
        $(document).ready(function() {
            dataCustomerAuditTable = $('#data-customer-audit-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('customer-audit.list') }}",
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
                        data: 'nama_event',
                        name: 'nama_event'
                    },
                    {
                        data: 'tanggal_mulai_event',
                        name: 'tanggal_mulai_event'
                    },
                    {
                        data: 'tanggal_selesai_event',
                        name: 'tanggal_selesai_event',
                        render: function(data) {
                            return data ? data : '-';
                        }
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
                        name: 'data_audit',
                        render: function(id, type, row) {
                            const url = "{{ route('customer-audit.data-audit-form', ':id') }}"
                                .replace(':id', id);

                            let addBtn = `
        <a href="${url}" class="btn btn-sm btn-primary">
            <i class="ti ti-plus"></i> Add Data Audit
        </a>
    `;

                            // Tombol expand hanya muncul kalau ada data audit
                            let expandBtn = '';
                            if (row.has_audit) {
                                expandBtn = `
            <button class="btn btn-sm btn-success btn-expand-data-audit" data-id="${id}">
                <i class="ti ti-database"></i> Data Audit
            </button>
        `;
                            }

                            return addBtn + expandBtn;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        render: function(id, type, row) {
                            let buttons = '';

                            // Tombol Show tersedia untuk semua pengguna
                            buttons += `
            <button class="btn btn-sm btn-info btn-show-customer-audit" data-id="${id}">
                <i class="ti ti-eye"></i> Show
            </button>
        `;

                            // Tombol Edit dan Delete hanya untuk superadmin
                            if (window.USER_ROLE === 'superadmin') {
                                buttons += `
                <button class="btn btn-sm btn-warning btn-edit-customer-audit" data-id="${id}">
                    <i class="ti ti-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="${id}">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            `;
                            }

                            return buttons;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#data-customer-audit-table tbody').on('click', '.btn-expand-data-audit', function() {
                const tr = $(this).closest('tr');
                const row = dataCustomerAuditTable.row(tr);
                const id = $(this).data('id');

                if (row.child.isShown()) {
                    // Sembunyikan child row
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    const url = window.ROUTES.dataAuditList.replace(':id', id);

                    $.get(url, function(data) {
                        // Container untuk subtable
                        let html = `<table class="table table-bordered table-striped mt-3" id="subtable-${id}" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Temuan</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>PIC</th>
                                    <th>Keterangan</th>
                                    <th>File Evident</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>`;

                        row.child(html).show();
                        tr.addClass('shown');

                        // Inisialisasi DataTable untuk child row
                        $(`#subtable-${id}`).DataTable({
                            data: data.data, // <-- perhatikan ini
                            columns: [{
                                    data: 'temuan'
                                },
                                {
                                    data: 'due_date',
                                    defaultContent: '-'
                                },
                                {
                                    data: 'status',
                                    render: function(status, type, row) {
                                        const isClosed = row.file_evident ? true :
                                            status.toLowerCase() === 'closed';
                                        const badgeClass = isClosed ? 'bg-success' :
                                            'bg-warning';
                                        return `<span class="badge ${badgeClass}">${isClosed ? 'Closed' : 'Open'}</span>`;
                                    }
                                },
                                {
                                    data: 'pic'
                                },
                                {
                                    data: 'keterangan',
                                    defaultContent: '-'
                                },
                                {
                                    data: 'file_evident',
                                    render: function(file) {
                                        if (file) {
                                            const fileUrl =
                                                `${window.DATA_AUDIT_BASE_URL}/${file}`;
                                            return `<button class="btn btn-sm btn-info btn-preview-pdf" data-file="${fileUrl}">
                <i class="ti ti-file"></i> Lihat
            </button>`;
                                        }
                                        return '-';
                                    }
                                },
                                {
                                    data: 'id',
                                    orderable: false,
                                    searchable: false,
                                    render: function(rowData) {
                                        return `
                                        <button class="btn btn-sm btn-warning btn-edit-data-audit" data-id="${rowData}">
                                            <i class="ti ti-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete-data-audit" data-id="${rowData}">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                        `;
                                    }
                                }
                            ],
                            paging: false,
                            searching: false,
                            info: false,
                            ordering: false
                        });

                    });
                }
            });

            // Delete child DataAudit
            $(document).on('click', '.btn-delete-data-audit', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus data audit?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('customer-audit.destroy-audit-data', ':id') }}"
                                .replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', response.message, 'success');

                                // Reload parent table (opsional) atau reload child table saja
                                // Jika ingin reload child table:
                                // Temukan parent row dan reload DataTable-nya
                                $('#subtable-' + id).DataTable().ajax.reload(null,
                                    false); // false = pertahankan paging
                                dataCustomerAuditTable.ajax.reload(null,
                                    false); // reload parent table
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });


            // Delete data
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
                            url: "{{ route('customer-audit.destroy', ':id') }}".replace(
                                ':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', 'Data berhasil dihapus.',
                                    'success');
                                dataCustomerAuditTable.ajax.reload();
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
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
@endpush
