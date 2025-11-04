<!-- Modal Create Data Claim -->
<div class="modal fade" id="modalCreateClaim" tabindex="-1" aria-labelledby="modalCreateClaimLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('data-claim.store') }}" method="POST" enctype="multipart/form-data"
                id="formCreateClaim">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateClaimLabel">Tambah Data Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tanggal_claim" class="form-label">Tanggal Claim <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tanggal_claim" id="tanggal_claim" class="form-control">
                            <div class="invalid-feedback" id="error-tanggal_claim"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="customer" class="form-label">Customer <span class="text-danger">*</span></label>
                            <input type="text" name="customer" id="customer" class="form-control">
                            <div class="invalid-feedback" id="error-customer"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="part_no" class="form-label">Part No <span class="text-danger">*</span></label>
                            <input type="text" name="part_no" id="part_no" class="form-control">
                            <div class="invalid-feedback" id="error-part_no"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="problem" class="form-label">Problem <span class="text-danger">*</span></label>
                            <input type="text" name="problem" id="problem" class="form-control">
                            <div class="invalid-feedback" id="error-problem"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1">
                            <div class="invalid-feedback" id="error-quantity"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="klasifikasi" class="form-label">Klasifikasi <span
                                    class="text-danger">*</span></label>
                            <select name="klasifikasi" id="klasifikasi" class="form-select">
                                <option value="">-- Pilih Klasifikasi --</option>
                                <option value="Function">Function</option>
                                <option value="Appearance">Appearance</option>
                                <option value="Dimension">Dimension</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="invalid-feedback" id="error-klasifikasi"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Non Official">Non Official</option>
                                <option value="Official">Official</option>
                            </select>
                            <div class="invalid-feedback" id="error-kategori"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="file_evident" class="form-label">Upload File Evident (jpg, png, pdf)</label>
                            <input type="file" name="file_evident" id="file_evident" class="form-control"
                                accept="application/pdf">
                            <div class="invalid-feedback" id="error-file_evident"></div>
                            <small class="form-text text-muted">Opsional, maksimal 2MB.</small>
                        </div>
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
        $('#formCreateClaim').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            // Reset error state
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            Swal.fire({
                title: 'Sedang menyimpan...',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        });
                        form[0].reset();
                        $('#modalCreateClaim').modal('hide');
                        dataClaimTable.ajax.reload();
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

        // Reset form saat modal ditutup
        $('#modalCreateClaim').on('hidden.bs.modal', function() {
            const form = $('#formCreateClaim');
            form[0].reset();
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });
    </script>
@endpush
