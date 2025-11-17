<!-- Modal Create WI -->
<div class="modal fade" id="modalCreateWI" tabindex="-1" aria-labelledby="modalCreateWILabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formCreateWI">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateWILabel">Tambah Dokumen WI PPIC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="sop_id" id="wi_sop_id">

                    <div class="mb-3">
                        <label for="title_document_wi" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="title_document_wi" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">Contoh: "WI Pemeriksaan Visual Produk Akhir".</small>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_document_wi" class="form-label">Deskripsi Dokumen
                        </label>
                        <textarea name="keterangan" id="keterangan_document_wi" class="form-control" rows="3"></textarea>
                        <small class="form-text text-muted">
                            Jelaskan tujuan atau isi dokumen ini.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_wi" class="form-label">Upload Dokumen (PDF) <span
                                class="text-danger">*</span></label>
                        <input type="file" name="file_document" id="file_document_wi" accept="application/pdf"
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
        let selectedSopId = null;

        $(document).on('click', '.btn-add-wi', function() {
            selectedSopId = $(this).data('sop-id');
            $('#wi_sop_id').val(selectedSopId);
            $('#modalCreateWI').modal('show');
        });

        $('#formCreateWI').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = `{{ route('ppic.store-wi', ['sop' => '__sopId__']) }}`.replace('__sopId__',
                selectedSopId);
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
                        $('#modalCreateWI').modal('hide');
                        $('#formCreateWI')[0].reset();

                        $.ajax({
                            url: "{{ route('ppic.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle(); // WAJIB re-init toggle
                                $.get("{{ route('ppic.count-wi') }}", function(res) {
                                    $('#total-documents-wi-rep').text(res.wiCount);
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

                        console.log(errors);

                        // Reset dulu semua invalid state & error message
                        $('#formCreateWI .form-control').removeClass('is-invalid');
                        $('#formCreateWI .invalid-feedback').text('');

                        // Loop setiap field error dan tampilkan
                        $.each(errors, function(field, messages) {
                            // Tambah class invalid pada input yang sesuai
                            let input = $(`#formCreateWI [name="${field}"]`);
                            input.addClass('is-invalid');

                            $(`#error-${field}`).text(messages[0]);
                        });
                    } else {
                        Swal.fire('Error', 'Tidak dapat menghubungi server.', 'error');
                    }
                }
            });
        });

        $('#modalCreateWI').on('hidden.bs.modal', function() {
            const form = $('#formCreateWI');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });
    </script>
@endpush
