@extends('layouts.main')

@section('content')
    <div class="container-fluid">

        <!-- Header -->
        <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-3">
            <div class="card-body px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="fw-semibold mb-0">Form Data Audit - {{ $dataCustomerAudit->nama_event ?? 'Event' }}</h4>
                    <ol class="breadcrumb border border-info px-3 py-2 rounded">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer-audit.index') }}" class="text-muted">Customer
                                Audit</a></li>
                        <li class="breadcrumb-item"><span class="active text-primary fw-semibold">Form Data Audit</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Info Event Collapsible -->
        <div class="mb-3">
            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#infoEvent"
                aria-expanded="false" aria-controls="infoEvent">
                <i class="ti ti-info-circle"></i> Info Event
            </button>
            <div class="collapse mt-2" id="infoEvent">
                <div class="card card-body bg-light">
                    <p><strong>Nama Event:</strong> {{ $dataCustomerAudit->nama_event }}</p>
                    <p><strong>Deskripsi:</strong> {!! $dataCustomerAudit->deskripsi_event !!}</p>
                    <p><strong>Tanggal Mulai:</strong> {{ $dataCustomerAudit->tanggal_mulai_event->format('d-m-Y') }}</p>
                    <p><strong>Tanggal Selesai:</strong> {{ $dataCustomerAudit->tanggal_selesai_event->format('d-m-Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Dinamis -->
        <form action="{{ route('customer-audit.store-audit-form', $dataCustomerAudit->id) }}" method="POST" id="data-audit"
            enctype="multipart/form-data">
            @csrf
            <div id="dynamic-forms-container">

                @php
                    $temuans = old('temuan', ['']); // default 1 row
                    $due_dates = old('due_date', ['']);
                    $pics = old('pic', ['']);
                    $keterangans = old('keterangan', ['']);
                @endphp

                @foreach ($temuans as $i => $temuan)
                    <div class="card mb-3 dynamic-form-row">
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Temuan -->
                                <div class="col-md-12">
                                    <label>Temuan</label>
                                    <textarea name="temuan[]" class="form-control" rows="3">{{ $temuan }}</textarea>
                                    @if ($errors->has('temuan.' . $i))
                                        <span class="text-danger">{{ $errors->first('temuan.' . $i) }}</span>
                                    @endif
                                </div>

                                <!-- Due Date, PIC, File Evident -->
                                <div class="col-md-4">
                                    <label>Due Date</label>
                                    <input type="date" name="due_date[]" class="form-control"
                                        value="{{ $due_dates[$i] ?? '' }}">
                                    @if ($errors->has('due_date.' . $i))
                                        <span class="text-danger">{{ $errors->first('due_date.' . $i) }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label>PIC</label>
                                    <input type="text" name="pic[]" class="form-control"
                                        value="{{ $pics[$i] ?? '' }}">
                                    @if ($errors->has('pic.' . $i))
                                        <span class="text-danger">{{ $errors->first('pic.' . $i) }}</span>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label>File Evident</label>
                                    <input type="file" name="file_evident[]" class="form-control">
                                    @if ($errors->has('file_evident.' . $i))
                                        <span class="text-danger">{{ $errors->first('file_evident.' . $i) }}</span>
                                    @endif
                                </div>

                                <!-- Keterangan -->
                                <div class="col-md-12">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan[]" class="form-control" rows="3">{{ $keterangans[$i] ?? '' }}</textarea>
                                    @if ($errors->has('keterangan.' . $i))
                                        <span class="text-danger">{{ $errors->first('keterangan.' . $i) }}</span>
                                    @endif
                                </div>

                                <!-- Tombol Hapus dan Tambah -->
                                <div class="col-md-12 mt-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-row"
                                        style="margin-right: 3px">
                                        <i class="ti ti-trash"></i> Hapus Form
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm add-row">
                                        <i class="ti ti-plus"></i> Tambah Form
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Simpan Data Audit</button>
                <a href="{{ route('customer-audit.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const container = document.getElementById('dynamic-forms-container');
            const form = document.getElementById('data-audit');

            container.addEventListener('click', function(e) {

                // --- HAPUS FORM ---
                if (e.target.closest('.remove-row')) {
                    const rows = container.querySelectorAll('.dynamic-form-row');

                    if (rows.length > 1) {
                        e.target.closest('.dynamic-form-row').remove();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Anda tidak bisa menghapus form ini',
                        });
                    }
                }

                // --- TAMBAH FORM ---
                if (e.target.closest('.add-row')) {

                    const original = container.querySelector('.dynamic-form-row');
                    const newForm = original.cloneNode(true);

                    // Reset semua input & textarea
                    newForm.querySelectorAll('input, textarea').forEach(el => {
                        if (el.type === 'file') {
                            el.value = '';
                        } else {
                            el.value = '';
                        }
                    });

                    // Hapus pesan error di row baru
                    newForm.querySelectorAll('.text-danger').forEach(el => el.remove());

                    container.appendChild(newForm);
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault(); // cegah submit langsung

                Swal.fire({
                    title: 'Yakin ingin menyimpan?',
                    text: "Pastikan semua data sudah benar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // submit jika user setuju
                    }
                });
            });
        });
    </script>
@endpush
