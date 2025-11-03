@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="card bg-light-info shadow-none position-relative overflow-hidden">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">User Management</h4>

                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            @if (request()->routeIs('dashboard'))
                                <span class="active text-primary fw-semibold">Dashboard</span>
                            @else
                                <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item">
                            @if (request()->routeIs('users.index'))
                                <span class="active text-primary fw-semibold">User Management</span>
                            @else
                                <a href="{{ route('users.index') }}" class="text-muted">User Management</a>
                            @endif
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <section class="datatables">
            <div class="card">
                <div class="card-body">
                     <div class="mb-3 d-flex justify-content-end align-items-center flex-wrap gap-2">
                        <!-- Move the "Add User" button to the right -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalCreateUser">
                            <i class="ti ti-plus"></i> Add User
                        </button>
                    </div>

                    <div class="table-responsive m-t-40">
                        <table id="user-table" class="table border display table-bordered table-striped no-wrap"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded here via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('user.modal-create')
    @include('user.modal-edit')
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
        var userTable;
        $(document).ready(function() {
            userTable = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('users.list') }}",
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
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                                <button class="btn btn-sm btn-warning me-1 btn-edit-user" data-id="${id}">
                                    <i class="ti ti-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-user" data-id="${id}">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            `;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Delete User
            $(document).on('click', '.btn-delete-user', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure you want to delete this user?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        let url = "{{ route('users.destroy', ':id') }}".replace(':id', id);

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
                                        title: 'Deleted!',
                                        text: response.message
                                    });

                                    userTable.ajax.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed!',
                                        text: response.message ||
                                            'There was an error.'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Unable to reach server.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
