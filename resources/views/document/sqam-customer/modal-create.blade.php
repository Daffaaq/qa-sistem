<!-- Modal Create SQAM Customer -->
<div class="modal fade" id="modalCreateSQAMCustomer" tabindex="-1" aria-labelledby="modalCreateSQAMCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('sqam-customer.store') }}" method="POST" enctype="multipart/form-data"
                id="formCreateSQAMCustomer">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateSQAMCustomerLabel">Tambah SQAM Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="title_document_modal" class="form-label">Judul Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_modal" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Maksimal 255 karakter. Contoh: "SQAM Customer XYZ Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="category_document_modal" class="form-label">Kategori Dokumen
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="category_document" id="category_document_modal" class="form-control"
                            value="SQAM Customer" readonly>
                        <small class="form-text text-muted">
                            Kategori ini ditentukan otomatis dan tidak bisa diubah.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_modal" class="form-label">Upload Dokumen (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_modal" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">
                            Hanya format PDF. Ukuran maksimal 100MB.
                        </small>
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
        $('#formCreateSQAMCustomer').on('submit', function(e) {
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

                        $('#formCreateSQAMCustomer')[0].reset();
                        $('#modalCreateSQAMCustomer').modal('hide');
                        sqamCustomerTable.ajax.reload();
                        // 🔄 Update total dokumen
                        $.get("{{ route('sqam-customer.count') }}", function(res) {
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
                        let errors = xhr.responseJSON.errors;

                        // Reset error lama
                        $('#formCreateSQAMCustomer').find('.form-control').removeClass('is-invalid');
                        $('#formCreateSQAMCustomer').find('.invalid-feedback').text('');

                        // Tampilkan error baru
                        $.each(errors, function(field, messages) {
                            let input = $(`#formCreateSQAMCustomer [name="${field}"]`);
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
        // Reset error dan form saat modal Create SQAM ditutup
        $('#modalCreateSQAMCustomer').on('hidden.bs.modal', function() {
            const form = $('#formCreateSQAMCustomer');

            form[0].reset(); // Reset seluruh input
            form.find('.form-control').removeClass('is-invalid'); // Hapus kelas error
            form.find('.invalid-feedback').text(''); // Kosongkan pesan error
        });
    </script>
@endpush
