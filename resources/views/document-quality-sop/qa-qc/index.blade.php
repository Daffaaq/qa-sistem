@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">QC SOP</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="active text-primary fw-semibold">QC SOP</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Hierarchy Content -->
        <section>
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    {{-- Card Statistik (Compact) --}}
                    <div class="row mb-4 align-items-center">
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-danger text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-documents-sop">{{ $sopCount }}</h5>
                                    <small>Dokumen SOP</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-warning text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-documents-wi">{{ $wiCount }}</h5>
                                    <small>Dokumen WI</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div
                                class="bg-success text-white rounded d-flex justify-content-between align-items-center p-3 shadow-sm">
                                <div>
                                    <h5 class="mb-0" id="total-documents-form">{{ $formCount }}</h5>
                                    <small>Dokumen FORM</small>
                                </div>
                                <div class="ms-3">
                                    <i class="ti ti-file-text fs-9"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 d-flex justify-content-end">
                            <button class="btn btn-primary" id="btnShowAllRevisions">
                                Lihat Semua Revisi
                            </button>
                        </div>
                        {{-- <!-- Kolom tambahan untuk tombol -->
                        <div class="col-lg-6 col-md-12 d-flex justify-content-end">
                            <button class="btn btn-primary" id="btnShowAllRevisions">
                                Lihat Semua Revisi
                            </button>
                        </div> --}}
                    </div>


                    <div class="row mb-3 d-flex justify-content-end">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Cari SOP/WI/Form..." aria-label="Search" aria-describedby="search-icon">
                                <span class="input-group-text" id="search-icon">
                                    <i class="ti ti-search"></i>
                                </span>
                            </div>
                            <div id="noResultsMessage" class="text-center text-muted mt-2" style="display: none;">
                                Tidak ada hasil yang cocok.
                            </div>
                        </div>
                    </div>


                    <div class="tree-modern">
                        <div id="sop-list-container">
                            <div class="text-center text-muted">Memuat data SOP...</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('document-quality-sop.qa-qc.modal-preview')
    @include('document-quality-sop.qa-qc.modal-create-sop')
    @include('document-quality-sop.qa-qc.modal-edit-sop')
    @include('document-quality-sop.qa-qc.modal-revisi-sop')
    @include('document-quality-sop.qa-qc.modal-create-wi')
    @include('document-quality-sop.qa-qc.modal-edit-wi')
    @include('document-quality-sop.qa-qc.modal-revisi-wi')
    @include('document-quality-sop.qa-qc.modal-create-form')
    @include('document-quality-sop.qa-qc.modal-edit-form')
    @include('document-quality-sop.qa-qc.modal-revisi-form')
    @include('document-quality-sop.qa-qc.modal-show-revisi')
@endsection

@push('styles')
    <style>
        /* Tree Modern Styling */
        .tree-modern ul {
            list-style: none;
            padding-left: 1rem;
            margin-left: 0;
            border-left: 2px solid #c8d1da;
            transition: max-height 0.3s ease;
            overflow: hidden;
        }

        .tree-modern li {
            margin: 0.75rem 0;
            position: relative;
        }

        .tree-modern li::before {
            content: '';
            position: absolute;
            top: 12px;
            left: -14px;
            width: 10px;
            height: 2px;
            background: #c8d1da;
            border-radius: 2px;
        }

        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border: 1px solid #d1d9e6;
            padding: 6px 10px !important;
            border-radius: 8px;
            font-size: 13px !important;
            cursor: pointer;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
            transition: background-color 0.2s ease, box-shadow 0.2s ease;
        }

        .doc-item:hover {
            background-color: #f5f9ff;
            box-shadow: 0 3px 8px rgb(0 0 0 / 0.12);
        }

        .doc-item.sop {
            border-left: 5px solid var(--bs-danger);
        }

        .doc-item.wi {
            border-left: 5px solid #f0ad4e;
        }

        .doc-item.form {
            border-left: 5px solid #5cb85c;
        }

        .doc-title {
            color: #2c3e50;
            user-select: none;
        }

        .doc-title:hover {
            text-decoration: underline;
        }

        .badge {
            font-size: 0.65rem !important;
            padding: 3px 7px !important;
            border-radius: 12px;
            font-weight: 600;
            user-select: none;
        }

        .action-buttons a {
            font-size: 0.65rem !important;
            padding: 3px 7px !important;
            border-radius: 6px;
            text-decoration: none;
            color: #fff;
            user-select: none;
            transition: background-color 0.2s ease;
        }

        .action-buttons a.btn-primary {
            background-color: #3a8ee6;
        }

        .action-buttons a.btn-primary:hover {
            background-color: #2f7ad7;
        }

        .action-buttons a.btn-info {
            background-color: #5bc0de;
        }

        .action-buttons a.btn-info:hover {
            background-color: #31b0d5;
        }

        .action-buttons a.btn-danger {
            background-color: #d9534f;
        }

        .action-buttons a.btn-danger:hover {
            background-color: #c9302c;
        }

        /* Toggle Icon Rotation */
        .toggle-icon {
            width: 12px !important;
            height: 12px !important;
            transition: transform 0.3s ease;
            flex-shrink: 0;
        }

        .toggle-node[aria-expanded="true"] .toggle-icon {
            transform: rotate(90deg);
        }

        /* Collapse Node */
        .collapse-node {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
            padding-left: 1rem;
            margin-top: 0.5rem;
            border-left: 2px solid #dbe5f4;
            user-select: none;
        }

        .collapse-node.open {
            max-height: 2000px;
            /* big enough to fit content */
        }

        /* Keyboard focus for accessibility */
        .toggle-node:focus {
            outline: 2px solid #3a8ee6;
            outline-offset: 3px;
        }

        .pin-point {
            margin-left: 6px;
        }

        .pin-point .badge {
            font-size: 0.6rem;
            background-color: #dc3545;
            /* Bootstrap red */
            color: white;
        }

        .doc-title {
            text-decoration: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function loadSopList() {
            $.ajax({
                url: "{{ route('qa-qc.content-list-partial') }}",
                method: "GET",
                success: function(html) {
                    $('#sop-list-container').html(html);

                    // Re-init toggle functionality
                    initTreeToggle();
                },
                error: function() {
                    $('#sop-list-container').html(
                        '<div class="text-danger text-center">Gagal memuat data SOP.</div>');
                }
            });
        }

        function filterDocuments() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            // Hapus semua pin-point sebelumnya
            document.querySelectorAll('.pin-point').forEach(pin => pin.remove());

            let anyVisible = false;

            document.querySelectorAll('#sop-list-container li').forEach(li => {
                const docItems = li.querySelectorAll('.doc-item');
                let isMatchFound = false;

                docItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const isMatch = text.includes(searchTerm);

                    if (isMatch && searchTerm.trim() !== '') {
                        isMatchFound = true;

                        // Tambahkan pin-point jika belum ada
                        const title = item.querySelector('.doc-title');
                        if (title && !title.querySelector('.pin-point')) {
                            const pin = document.createElement('span');
                            pin.classList.add('pin-point');
                            pin.innerHTML = '📍'; // icon pin-point
                            title.appendChild(pin);
                        }
                    }
                });

                // Tampilkan/hilangkan berdasarkan apakah ada match di anak-anaknya
                li.style.display = isMatchFound || searchTerm.trim() === '' ? '' : 'none';

                // Buka semua node yang berisi hasil
                if (isMatchFound) {
                    anyVisible = true;
                    li.querySelectorAll('.collapse-node').forEach(el => el.classList.add('open'));
                    li.querySelectorAll('.toggle-node').forEach(el => el.setAttribute('aria-expanded', 'true'));
                } else {
                    li.querySelectorAll('.collapse-node').forEach(el => el.classList.remove('open'));
                    li.querySelectorAll('.toggle-node').forEach(el => el.setAttribute('aria-expanded', 'false'));
                }
            });

            // Show or hide "no results" message
            const noResultsMessage = document.getElementById('noResultsMessage');
            const sopListContainer = document.getElementById('sop-list-container');

            if (!anyVisible && searchTerm.trim() !== '') {
                noResultsMessage.style.display = 'block';
                sopListContainer.style.display = 'none';
            } else {
                noResultsMessage.style.display = 'none';
                sopListContainer.style.display = '';
            }
        }




        function initTreeToggle() {
            document.querySelectorAll('.toggle-node').forEach(node => {
                node.addEventListener('click', (e) => {
                    if (e.target.closest('a')) return;

                    const parent = node.parentElement;
                    const childList = parent.querySelector('.collapse-node');

                    if (!childList) return;

                    const isOpen = node.getAttribute('aria-expanded') === 'true';
                    node.setAttribute('aria-expanded', String(!isOpen));

                    if (!isOpen) {
                        childList.classList.add('open');
                    } else {
                        childList.classList.remove('open');
                    }
                });

                node.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        node.click();
                    }
                });
            });
        }


        // Inisialisasi awal
        document.addEventListener('DOMContentLoaded', () => {
            // initTreeToggle();
            loadSopList();
            document.getElementById('searchInput').addEventListener('input', () => {
                filterDocuments();
            });
        });
    </script>
    <script>
        function showDeleteConfirm(title, url, callback) {
            Swal.fire({
                title: `Yakin ingin menghapus ${title}?`,
                text: "Data yang dihapus tidak bisa dikembalikan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire("Terhapus!", response.message, "success");
                            loadSopList(); // reload partial
                            if (typeof callback === "function") callback();
                        },
                        error: function(xhr) {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                        }
                    });
                }
            });
        }

        function refreshAllCounts() {
            $.get("{{ route('qa-qc.count-all') }}", function(res) {
                $('#total-documents-sop').text(res.sopCount);
                $('#total-documents-wi').text(res.wiCount);
                $('#total-documents-form').text(res.formCount);
            });
        }

        $(document).on('click', '.btn-delete-sop', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            // Generate the URL using Laravel's route() helper
            const url = `{{ route('qa-qc.destroy-sop', ['sop' => ':sopId']) }}`.replace(':sopId', id);

            showDeleteConfirm('SOP ini', url, function() {
                refreshAllCounts();
            });
        });


        $(document).on('click', '.btn-delete-wi', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = `{{ route('qa-qc.destroy-wi', ['wi' => ':wiId']) }}`.replace(':wiId', id);

            showDeleteConfirm('WI ini', url, function() {
                refreshAllCounts();
            });
        });

        $(document).on('click', '.btn-delete-form', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = `{{ route('qa-qc.destroy-form', ['form' => ':formId']) }}`.replace(':formId', id);

            showDeleteConfirm('Form ini', url, function() {
                refreshAllCounts();
            });
        });
    </script>
@endpush
