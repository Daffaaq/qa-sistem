<!-- Modal Create SOP Management Representative -->
<div class="modal fade" id="modalCreateSOPRep" tabindex="-1" aria-labelledby="modalCreateSOPRepLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('human-capital.store-sop') }}" method="POST" enctype="multipart/form-data"
                id="formCreateSOPRep">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateSOPRepLabel">Tambah Dokumen SOP Human Capital
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title_document_sop_rep" class="form-label">Judul Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_sop_rep" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Maksimal 255 karakter. Contoh: "SOP Human Capital PT IPG Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="category_document_sop_rep" class="form-label">Kategori Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="category_document" id="category_document_sop_rep"
                            class="form-control" value="Human Capital" readonly>
                        <small class="form-text text-muted">
                            Kategori ini ditentukan otomatis dan tidak bisa diubah.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_sop_rep" class="form-label">Upload Dokumen (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_sop_rep" accept="application/pdf"
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
        $('#formCreateSOPRep').on('submit', function(e) {
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

                        $('#formCreateSOPRep')[0].reset();
                        $('#modalCreateSOPRep').modal('hide');
                        $.get("{{ route('human-capital.count-sop') }}", function(res) {
                            $('#total-documents-sop-rep').text(res.sopCount);
                        });
                        $.ajax({
                            url: "{{ route('human-capital.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle(); // <<< WAJIB dipanggil
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal memuat ulang daftar SOP.'
                                });
                            }
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
                        let errors = xhr.responseJSON.errors;

                        // Reset error lama
                        $('#formCreateSOPRep').find('.form-control').removeClass('is-invalid');
                        $('#formCreateSOPRep').find('.invalid-feedback').text('');

                        // Tampilkan error baru
                        $.each(errors, function(field, messages) {
                            let input = $(`#formCreateSOPRep [name="${field}"]`);
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

        $('#modalCreateSOPRep').on('hidden.bs.modal', function() {
            const form = $('#formCreateSOPRep');

            form[0].reset(); // Reset seluruh input
            form.find('.form-control').removeClass('is-invalid'); // Hapus kelas error
            form.find('.invalid-feedback').text(''); // Kosongkan pesan error
        });
    </script>
@endpush
