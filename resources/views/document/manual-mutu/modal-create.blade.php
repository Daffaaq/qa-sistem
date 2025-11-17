<!-- Modal Create Manual Mutu -->
<div class="modal fade" id="modalCreateManualMutu" tabindex="-1" aria-labelledby="modalCreateManualMutuLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('manual-mutu.store') }}" method="POST" enctype="multipart/form-data"
                id="formCreateManualMutu">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateManualMutuLabel">Tambah Manual Mutu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title_document_modal" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="title_document_modal" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">Maksimal 255 karakter. Contoh: "Pedoman Mutu RS Tahun
                            2025".</small>
                    </div>

                    <div class="mb-3">
                        <label for="category_document_modal" class="form-label">Kategori Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="category_document" id="category_document_modal" class="form-control"
                            value="Manual Mutu" readonly>
                        <small class="form-text text-muted">Kategori ini ditentukan otomatis dan tidak bisa
                            diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_modal" class="form-label">Upload Dokumen (PDF) <span
                                class="text-danger">*</span></label>
                        <input type="file" name="file_document" id="file_document_modal" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Hanya format PDF. Ukuran maksimal 100MB.</small>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_modal" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan_modal" class="form-control" rows="3"
                            placeholder="Contoh: Revisi sesuai peraturan terbaru Kemenkes 2025"></textarea>
                        <small class="form-text text-muted">Opsional. Jelaskan perubahan atau catatan penting.</small>
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
        $('#formCreateManualMutu').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            // Reset error state
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            Swal.fire({
                title: 'Sedang mengunggah...',
                text: 'Mohon tunggu beberapa saat.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
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

                        form[0].reset();
                        $('#modalCreateManualMutu').modal('hide');
                        manualMutuTable.ajax.reload();

                        // 🔄 Update total dokumen
                        $.get("{{ route('manual-mutu.count') }}", function(res) {
                            $('#total-documents').text(res.totalDocuments);
                        });
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
                        const errors = xhr.responseJSON.errors;

                        // Tampilkan error per field
                        $.each(errors, function(field, messages) {
                            const inputField = form.find(`[name="${field}"]`);
                            inputField.addClass('is-invalid');
                            $(`#error-${field}`).text(messages.join(' '));
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
        // Reset error dan form saat modal Create SQAM ditutup
        $('#modalCreateManualMutu').on('hidden.bs.modal', function() {
            const form = $('#formCreateManualMutu');

            form[0].reset(); // Reset seluruh input
            form.find('.form-control').removeClass('is-invalid'); // Hapus kelas error
            form.find('.invalid-feedback').text(''); // Kosongkan pesan error
        });
    </script>
@endpush
