<!-- Modal Create SOP QA QC -->
<div class="modal fade" id="modalCreateSOP" tabindex="-1" aria-labelledby="modalCreateSOPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('qa-qc.store-sop') }}" method="POST" enctype="multipart/form-data" id="formCreateSOP">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateSOPLabel">Tambah Dokumen SOP QC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title_document_sop" class="form-label">Judul Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_sop" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Maksimal 255 karakter. Contoh: "SOP QC PT IPG Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="category_document_sop" class="form-label">Kategori Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="category_document" id="category_document_sop" class="form-control"
                            value="QA QC" readonly>
                        <small class="form-text text-muted">
                            Kategori ini ditentukan otomatis dan tidak bisa diubah.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_document_sop" class="form-label">Deskripsi Dokumen
                        </label>
                        <textarea name="keterangan" id="keterangan_document_sop" class="form-control" rows="3"></textarea>
                        <small class="form-text text-muted">
                            Jelaskan tujuan atau isi dokumen ini.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_document_sop" class="form-label">Upload Dokumen (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_sop" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Hanya format PDF. Ukuran maksimal 100MB.</small>
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
        $('#formCreateSOP').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            Swal.fire({
                title: 'Sedang mengunggah...',
                text: 'Mohon tunggu beberapa saat.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        });

                        $('#formCreateSOP')[0].reset();
                        $('#modalCreateSOP').modal('hide');
                        $.get("{{ route('qa-qc.count-sop') }}", function(res) {
                            $('#total-documents-sop').text(res.sopCount);
                        });
                        $.ajax({
                            url: "{{ route('qa-qc.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-list-container').html(html);
                                initTreeToggle(); // <<< ini WAJIB ditambahkan
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal memuat ulang daftar SOP.'
                                });
                            }
                        });
                        // Tambahkan reload tabel / update UI jika perlu
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan.'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        // Reset error lama
                        $('#formCreateSOP').find('.form-control').removeClass('is-invalid');
                        $('#formCreateSOP').find('.invalid-feedback').text('');

                        // Tampilkan error baru
                        $.each(errors, function(field, messages) {
                            let input = $(`#formCreateSOP [name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#error-${field}`).text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Tidak dapat menghubungi server.'
                        });
                    }
                }
            });
        });

        $('#modalCreateSOP').on('hidden.bs.modal', function() {
            const form = $('#formCreateSOP');

            form[0].reset(); // Reset seluruh input
            form.find('.form-control').removeClass('is-invalid'); // Hapus kelas error
            form.find('.invalid-feedback').text(''); // Kosongkan pesan error
        });
    </script>
@endpush
