<!-- Modal Create Form -->
<div class="modal fade" id="modalCreateForm" tabindex="-1" aria-labelledby="modalCreateFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formCreateForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateFormLabel">Tambah Dokumen Form PPIC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="wi_id" id="form_wi_id">

                    <div class="mb-3">
                        <label for="title_document_form" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="title_document_form" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">Contoh: "Form Pemeriksaan Visual Produk Akhir".</small>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_document_form" class="form-label">Deskripsi Dokumen
                        </label>
                        <textarea name="keterangan" id="keterangan_document_form" class="form-control" rows="3"></textarea>
                        <small class="form-text text-muted">
                            Jelaskan tujuan atau isi dokumen ini.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_form" class="form-label">Upload Dokumen (PDF) <span
                                class="text-danger">*</span></label>
                        <input type="file" name="file_document" id="file_document_form" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Format PDF. Ukuran maksimal 100MB.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let selectedWiId = null;

        $(document).on('click', '.btn-add-form', function() {
            selectedWiId = $(this).data('wi-id');
            $('#form_wi_id').val(selectedWiId);
            $('#modalCreateForm').modal('show');
        });

        $('#formCreateForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = `{{ route('ppic.store-form', ['wi' => '__wiId__']) }}`.replace('__wiId__',
                selectedWiId);

            Swal.fire({
                title: 'Sedang menyimpan...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#modalCreateForm').modal('hide');
                        $('#formCreateForm')[0].reset();

                        // Reload daftar SOP (atau bagian yang sesuai)
                        $.ajax({
                            url: "{{ route('ppic.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle(); // Re-init toggle kalau perlu
                                $.get("{{ route('ppic.count-form') }}", function(res) {
                                    $('#total-documents-form-rep').text(res.formCount);
                                });
                            },
                            error: function() {
                                Swal.fire('Gagal', 'Gagal memuat ulang daftar SOP.',
                                    'error');
                            }
                        });
                    } else {
                        Swal.fire('Gagal', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $('#formCreateForm .form-control').removeClass('is-invalid');
                        $('#formCreateForm .invalid-feedback').text('');

                        $.each(errors, function(field, message) {
                            $(`#formCreateForm [name="${field}"]`).addClass('is-invalid');
                            $(`#error-${field}`).text(message[0]);
                        });
                    } else {
                        Swal.fire('Error', 'Tidak dapat menghubungi server.', 'error');
                    }
                }
            });
        });

        $('#modalCreateForm').on('hidden.bs.modal', function() {
            const form = $('#formCreateForm');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });
    </script>
@endpush
