<!-- Modal Edit Manual Mutu -->
<div class="modal fade" id="modalEditManualMutu" tabindex="-1" aria-labelledby="modalEditManualMutuLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditManualMutu">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditManualMutuLabel">Edit Manual Mutu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.9rem;">
                            <strong>Catatan:</strong> File yang diunggah melalui form ini akan <strong>menimpa file
                                lama</strong>.
                            Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur <em>Revisi</em>.
                        </div>
                    </div>

                    <input type="hidden" name="document_id" id="edit_document_id">

                    <div class="mb-3">
                        <label for="edit_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_title_document" class="form-control">
                        <div class="invalid-feedback" id="error-edit-title_document"></div>
                        <small class="form-text text-muted">Maksimal 255 karakter.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category_document" class="form-label">Kategori Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="category_document" id="edit_category_document" class="form-control"
                            value="Manual Mutu" readonly>
                        <small class="form-text text-muted">Kategori ini tidak bisa diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_file_document" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <!-- Preview File Saat Ini -->
                    <div class="mb-3" id="current-file-info" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-edit-manual"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-manual" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-edit" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                id="prevPageEditManualMutu">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditManualMutu">1</span> dari <span
                                    id="pageCountEditManualMutu">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="nextPageEditManualMutu">
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/manual-mutu') }}"; // Path file PDF
        window.manualMutuEditRoute = "{{ route('manual-mutu.edit', ':id') }}";
        window.manualMutuUpdateRoute = "{{ route('manual-mutu.update', ':id') }}";

        // pdf.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDocEditManual = null;
        let currentPageEditManual = 1;

        // Render halaman PDF
        function renderPageEditManual(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-manual');
            const ctx = canvas.getContext('2d');

            pdfDocEditManual.getPage(pageNum).then(function(page) {
                const containerWidth = document.getElementById('pdf-preview-edit-manual').offsetWidth;
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
                    currentPageEditManual = pageNum;
                    $('#pageNumEditManualMutu').text(currentPageEditManual);
                    $('#pageCountEditManualMutu').text(pdfDocEditManual.numPages);

                    $('#prevPageEditManualMutu').prop('disabled', currentPageEditManual <= 1);
                    $('#nextPageEditManualMutu').prop('disabled', currentPageEditManual >= pdfDocEditManual
                        .numPages);
                });
            });
        }

        // Load PDF
        function loadPDFEditManual(fileUrl) {
            pdfDocEditManual = null;
            currentPageEditManual = 1;
            $('#pdf-loading-message-edit').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-manual').hide();
            $('#pdf-controls-edit').hide();

            pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
                pdfDocEditManual = pdf;
                $('#pdf-loading-message-edit').hide();
                $('#pdf-canvas-edit-manual').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit').show();
                }

                renderPageEditManual(currentPageEditManual);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                $('#pdf-loading-message-edit').html(
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download</a>.</p>`
                    ).show();
                $('#pdf-canvas-edit-manual').hide();
                $('#pdf-controls-edit').hide();
            });
        }

        // Navigasi PDF
        $('#prevPageEditManualMutu').on('click', function() {
            if (currentPageEditManual > 1) renderPageEditManual(currentPageEditManual - 1);
        });
        $('#nextPageEditManualMutu').on('click', function() {
            if (currentPageEditManual < pdfDocEditManual.numPages) renderPageEditManual(currentPageEditManual + 1);
        });

        // Open modal edit
        $(document).on('click', '.btn-edit-manual-mutu', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const editUrl = window.manualMutuEditRoute.replace(':id', id);

            $.get(editUrl, function(data) {
                $('#edit_document_id').val(data.id);
                $('#edit_title_document').val(data.title_document);
                $('#edit_category_document').val(data.category_document || 'Manual Mutu');

                if (data.file_document) {
                    loadPDFEditManual(`${window.BASE_URL}/${data.file_document}`);
                    $('#current-file-info').show();
                } else {
                    $('#current-file-info').hide();
                }

                const updateUrl = window.manualMutuUpdateRoute.replace(':id', data.id);
                $('#formEditManualMutu').attr('action', updateUrl);

                $('#modalEditManualMutu').modal('show');
            });
        });

        // Submit form edit
        $('#formEditManualMutu').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan perubahan...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
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
                        $('#modalEditManualMutu').modal('hide');
                        manualMutuTable.ajax.reload(); // sesuaikan dengan tabelmu
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
                        form.find('.form-control').removeClass('is-invalid');
                        form.find('.invalid-feedback').text('');
                        $.each(errors, function(field, messages) {
                            const input = form.find(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#error-edit-${field}`).text(messages[0]);
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

        // Reset modal
        $('#modalEditManualMutu').on('hidden.bs.modal', function() {
            const form = $('#formEditManualMutu');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-file-info').hide();
        });
    </script>
@endpush
