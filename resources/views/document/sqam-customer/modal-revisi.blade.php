<!-- Modal Revisi SQAM Customer -->
<div class="modal fade" id="modalRevisiSQAMCustomer" tabindex="-1" aria-labelledby="modalRevisiSQAMCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formRevisiSQAMCustomer">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRevisiSQAMCustomerLabel">Revisi Dokumen SQAM Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <!-- ALERT -->
                    <div class="mb-3">
                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.9rem;">
                            <strong>Catatan:</strong> Revisi dokumen akan <strong>menonaktifkan versi
                                sebelumnya</strong>
                            dan menyimpan file sebagai versi baru.
                            Judul dokumen <strong>tidak dapat diubah</strong>.
                        </div>
                    </div>

                    <input type="hidden" name="document_id" id="revisi_document_id">

                    <div class="mb-3">
                        <label for="revisi_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="revisi_title_document" class="form-control"
                            readonly required>
                    </div>

                    <div class="mb-3">
                        <label for="revisi_category_document" class="form-label">Kategori Dokumen</label>
                        <input type="text" name="category_document" id="revisi_category_document"
                            class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="revisi_file_document" class="form-label">Upload Revisi Dokumen (PDF) <span
                                class="text-danger">*</span></label>
                        <input type="file" name="file_document" id="revisi_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                    </div>

                    <!-- File Preview for previous document (using canvas) -->
                    <div class="mb-3" id="revisi-file-preview" style="display: none;">
                        <label class="form-label">File Sebelumnya:</label>
                        <div id="pdf-preview-revisi-sqam"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-revisi-sqam" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-revisi" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-revisi" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                id="prevPageRevisiSQAM">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumRevisiSQAM">1</span> dari <span
                                    id="pageCountRevisiSQAM">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="nextPageRevisiSQAM">
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/sqam-customer') }}";
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDocRevisiSQAM = null;
        let currentPageRevisiSQAM = 1;
        let currentPdfRevisiUrl = null;
        const scaleRevisiSQAM = 1.0;

        // Render halaman PDF
        function renderPageRevisiSQAM(pageNum) {
            const canvas = document.getElementById('pdf-canvas-revisi-sqam');
            const ctx = canvas.getContext('2d');

            pdfDocRevisiSQAM.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleRevisiSQAM
                });
                const containerWidth = document.getElementById('pdf-preview-revisi-sqam').offsetWidth || viewport
                    .width;
                const renderScale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({
                    scale: renderScale
                });

                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                page.render({
                    canvasContext: ctx,
                    viewport: scaledViewport
                }).promise.then(function() {
                    currentPageRevisiSQAM = pageNum;
                    $('#pageNumRevisiSQAM').text(currentPageRevisiSQAM);
                    $('#pageCountRevisiSQAM').text(pdfDocRevisiSQAM.numPages);
                    $('#prevPageRevisiSQAM').prop('disabled', currentPageRevisiSQAM <= 1);
                    $('#nextPageRevisiSQAM').prop('disabled', currentPageRevisiSQAM >= pdfDocRevisiSQAM
                        .numPages);
                });
            });
        }

        // Load PDF
        function loadPDFRevisiSQAM(fileUrl) {
            pdfDocRevisiSQAM = null;
            currentPageRevisiSQAM = 1;
            $('#pdf-loading-message-revisi').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-revisi-sqam').hide();
            $('#pdf-controls-revisi').hide();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocRevisiSQAM = pdf;
                $('#pdf-loading-message-revisi').hide();
                $('#pdf-canvas-revisi-sqam').show();
                if (pdf.numPages > 1) $('#pdf-controls-revisi').show();
                renderPageRevisiSQAM(currentPageRevisiSQAM);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                $('#pdf-loading-message-revisi').html(
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a></p>`
                ).show();
                $('#pdf-canvas-revisi-sqam').hide();
                $('#pdf-controls-revisi').hide();
            });
        }

        // Click Revisi button
        $(document).on('click', '.btn-revisi-sqam-customer', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = `{{ route('sqam-customer.revisi', ':id') }}`.replace(':id', id);

            $.get(url, function(data) {
                $('#revisi_document_id').val(data.id);
                $('#revisi_title_document').val(data.title_document);
                $('#revisi_category_document').val(data.category_document);

                currentPdfRevisiUrl = data.file_document ? `${window.BASE_URL}/${data.file_document}` :
                null;

                $('#formRevisiSQAMCustomer').attr('action',
                    `{{ route('sqam-customer.storeRevisi', ':id') }}`.replace(':id', data.id));

                $('#modalRevisiSQAMCustomer').modal('show');
            });
        });

        // Load PDF setelah modal muncul
        $('#modalRevisiSQAMCustomer').on('shown.bs.modal', function() {
            if (currentPdfRevisiUrl) {
                loadPDFRevisiSQAM(currentPdfRevisiUrl);
                $('#revisi-file-preview').show();
            } else {
                $('#revisi-file-preview').hide();
            }
        });

        // Navigasi PDF
        $(document).on('click', '#prevPageRevisiSQAM', function() {
            if (currentPageRevisiSQAM > 1) renderPageRevisiSQAM(currentPageRevisiSQAM - 1);
        });
        $(document).on('click', '#nextPageRevisiSQAM', function() {
            if (pdfDocRevisiSQAM && currentPageRevisiSQAM < pdfDocRevisiSQAM.numPages) renderPageRevisiSQAM(
                currentPageRevisiSQAM + 1);
        });

        // Submit form revisi
        $('#formRevisiSQAMCustomer').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            Swal.fire({
                title: 'Menyimpan revisi...',
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
                        $('#modalRevisiSQAMCustomer').modal('hide');
                        sqamCustomerTable.ajax.reload();
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
                            const input = $(`#formRevisiSQAMCustomer [name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#formRevisiSQAMCustomer #error-${field}`).text(messages[0]);
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
        $('#modalRevisiSQAMCustomer').on('hidden.bs.modal', function() {
            const form = $('#formRevisiSQAMCustomer');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#revisi-file-preview').hide();
            pdfDocRevisiSQAM = null;
            currentPdfRevisiUrl = null;
            $('#pdf-canvas-revisi-sqam').hide();
            $('#pdf-controls-revisi').hide();
        });
    </script>
@endpush
