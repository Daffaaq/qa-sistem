<!-- Modal Edit Data Claim -->
<div class="modal fade" id="modalEditClaim" tabindex="-1" aria-labelledby="modalEditClaimLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditClaim">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditClaimLabel">Edit Data Claim</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.9rem;">
                        <strong>Catatan:</strong> File baru yang diunggah akan <strong>menimpa file lama</strong>.
                        Jika tidak ingin mengganti, biarkan kolom file kosong.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Claim <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_claim" id="edit_tanggal_claim" class="form-control">
                            <div class="invalid-feedback" id="error-edit_tanggal_claim"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <input type="text" name="customer" id="edit_customer" class="form-control">
                            <div class="invalid-feedback" id="error-edit_customer"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Part No <span class="text-danger">*</span></label>
                            <input type="text" name="part_no" id="edit_part_no" class="form-control">
                            <div class="invalid-feedback" id="error-edit_part_no"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Problem <span class="text-danger">*</span></label>
                            <input type="text" name="problem" id="edit_problem" class="form-control">
                            <div class="invalid-feedback" id="error-edit_problem"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="edit_quantity" class="form-control"
                                min="1">
                            <div class="invalid-feedback" id="error-edit_quantity"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Klasifikasi <span class="text-danger">*</span></label>
                            <select name="klasifikasi" id="edit_klasifikasi" class="form-select">
                                <option value="">-- Pilih Klasifikasi --</option>
                                <option value="Function">Function</option>
                                <option value="Appearance">Appearance</option>
                                <option value="Dimension">Dimension</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="invalid-feedback" id="error-edit_klasifikasi"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" id="edit_kategori" class="form-select">
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Non Official">Non Official</option>
                                <option value="Official">Official</option>
                            </select>
                            <div class="invalid-feedback" id="error-edit_kategori"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Upload File Evident (jpg, png, pdf)</label>
                            <input type="file" name="file_evident" id="edit_file_evident" class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf">
                            <div class="invalid-feedback" id="error-edit_file_evident"></div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                        </div>

                        <!-- Preview File Lama -->
                        <div class="col-12" id="current-file-info" style="display:none;">
                            <label class="form-label">Preview File Saat Ini:</label>
                            <div id="file-preview-wrapper"
                                style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                                <canvas id="pdf-canvas-edit-claim" style="width: 100%; display:none;"></canvas>
                                <img id="img-preview-edit-claim" style="max-width:100%; display:none;" />
                                <p id="pdf-loading-message-edit-claim" class="p-3 text-muted">Memuat pratinjau file...
                                </p>
                            </div>
                            <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit-claim"
                                style="display: none;">
                                <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                    id="prevPageEditClaim">
                                    <i class="ti ti-chevron-left"></i> Prev
                                </button>
                                <span class="align-self-center">Halaman <span id="pageNumEditClaim">1</span> dari
                                    <span id="pageCountEditClaim">1</span></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                    id="nextPageEditClaim">
                                    Next <i class="ti ti-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let currentEditId = null;
        let pdfDocEditClaim = null;
        let currentPageEditClaim = 1;

        function renderPDFClaim(pageNum) {
            pdfDocEditClaim.getPage(pageNum).then(function(page) {
                const canvas = document.getElementById('pdf-canvas-edit-claim');
                const ctx = canvas.getContext('2d');
                const containerWidth = document.getElementById('file-preview-wrapper').offsetWidth;
                const viewport = page.getViewport({
                    scale: 1
                });
                const scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({
                    scale
                });

                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };
                page.render(renderContext).promise.then(function() {
                    currentPageEditClaim = pageNum;
                    $('#pageNumEditClaim').text(currentPageEditClaim);
                    $('#pageCountEditClaim').text(pdfDocEditClaim.numPages);
                    $('#prevPageEditClaim').prop('disabled', currentPageEditClaim <= 1);
                    $('#nextPageEditClaim').prop('disabled', currentPageEditClaim >= pdfDocEditClaim
                        .numPages);
                });
            });
        }

        function loadFilePreviewClaim(fileUrl) {
            $('#current-file-info').show();
            $('#pdf-canvas-edit-claim, #img-preview-edit-claim').hide();
            $('#pdf-loading-message-edit-claim').show().text('Memuat pratinjau...');

            const ext = fileUrl.split('.').pop().toLowerCase();
            if (ext === 'pdf') {
                pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
                    pdfDocEditClaim = pdf;
                    $('#pdf-loading-message-edit-claim').hide();
                    $('#pdf-canvas-edit-claim').show();
                    $('#pdf-controls-edit-claim').toggle(pdf.numPages > 1);
                    renderPDFClaim(1);
                }).catch(() => {
                    $('#pdf-loading-message-edit-claim').html(
                        `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download</a>.</p>`
                    );
                });
            } else if (['jpg', 'jpeg', 'png'].includes(ext)) {
                $('#img-preview-edit-claim').attr('src', fileUrl).show();
                $('#pdf-loading-message-edit-claim').hide();
                $('#pdf-controls-edit-claim').hide();
            } else {
                $('#pdf-loading-message-edit-claim').html(
                    `<a href="${fileUrl}" target="_blank">Lihat File Lama</a>`
                );
            }
        }

        // Navigasi PDF
        $('#prevPageEditClaim').on('click', () => {
            if (currentPageEditClaim > 1) renderPDFClaim(currentPageEditClaim - 1);
        });
        $('#nextPageEditClaim').on('click', () => {
            if (currentPageEditClaim < pdfDocEditClaim.numPages) renderPDFClaim(currentPageEditClaim + 1);
        });

        // Buka modal edit
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            currentEditId = id;

            $.ajax({
                url: "{{ route('data-claim.edit', ':id') }}".replace(':id', id),
                type: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#edit_tanggal_claim').val(data.tanggal_claim);
                        $('#edit_customer').val(data.customer);
                        $('#edit_part_no').val(data.part_no);
                        $('#edit_problem').val(data.problem);
                        $('#edit_quantity').val(data.quantity);
                        $('#edit_klasifikasi').val(data.klasifikasi);
                        $('#edit_kategori').val(data.kategori);

                        if (data.file_evident) {
                            const fileUrl = "{{ asset('documents/data-claim') }}/" + data.file_evident;
                            loadFilePreviewClaim(fileUrl);
                        } else {
                            $('#current-file-info').hide();
                        }

                        $('#modalEditClaim').modal('show');
                    } else {
                        Swal.fire('Gagal!', 'Data tidak ditemukan.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak dapat mengambil data.', 'error');
                }
            });
        });

        // Submit update
        $('#formEditClaim').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = "{{ route('data-claim.update', ':id') }}".replace(':id', currentEditId);

            Swal.fire({
                title: 'Menyimpan perubahan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#modalEditClaim').modal('hide');
                        dataClaimTable.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        for (const [field, messages] of Object.entries(errors)) {
                            const input = $(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#error-edit_${field}`).text(messages.join(' '));
                        }
                    } else {
                        Swal.fire('Gagal!', 'Tidak dapat menghubungi server.', 'error');
                    }
                }
            });
        });

        // Reset modal
        $('#modalEditClaim').on('hidden.bs.modal', function() {
            const form = $('#formEditClaim');
            form[0].reset();
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-file-info').hide();
        });
    </script>
@endpush
