<!-- Modal Revisi SQAM Supplier -->
<div class="modal fade" id="modalRevisiSQAMSupplier" tabindex="-1" aria-labelledby="modalRevisiSQAMSupplierLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formRevisiSQAMSupplier">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRevisiSQAMSupplierLabel">Revisi Dokumen SQAM Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- ALERT -->
                    <div class="mb-3">
                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.9rem;">
                            <strong>Catatan:</strong> Revisi dokumen akan <strong>menonaktifkan versi
                                sebelumnya</strong>
                            dan menyimpan file sebagai versi baru. Judul dokumen <strong>tidak dapat diubah</strong>.
                        </div>
                    </div>

                    <input type="hidden" name="document_id" id="revisi_sqam_supplier_document_id">

                    <div class="mb-3">
                        <label for="revisi_sqam_supplier_title_document" class="form-label">
                            Judul Dokumen <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="revisi_sqam_supplier_title_document"
                            class="form-control" readonly required>
                    </div>

                    <div class="mb-3">
                        <label for="revisi_sqam_supplier_category_document" class="form-label">
                            Kategori Dokumen
                        </label>
                        <input type="text" name="category_document" id="revisi_sqam_supplier_category_document"
                            class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="revisi_sqam_supplier_file_document" class="form-label">
                            Upload Revisi Dokumen (PDF) <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="revisi_sqam_supplier_file_document"
                            accept="application/pdf" class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan Revisi Saat Ini</label>
                        <div id="revisi_keterangan_display" class="form-control bg-light text-dark"
                            style="min-height: 80px; padding: 10px; white-space: pre-wrap; font-size: 0.95rem; line-height: 1.5; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                            —
                        </div>
                        <small class="form-text text-muted mt-1">
                            Keterangan ini hanya untuk referensi. Tidak dapat diubah saat revisi.
                        </small>
                    </div>

                    <!-- File Preview for previous document -->
                    <div class="mb-3" id="revisi-sqam-supplier-file-preview" style="display: none;">
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
    <script type="module">
        window.BASE_URL = "{{ asset('documents/sqam-supplier') }}";
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let pdfDocRevisiSQAM = null;
        let currentPageRevisiSQAM = 1;
        const scaleRevisiSQAM = 1.0;

        // Function to render PDF page for Revisi
        function renderPageRevisiSQAM(pageNum) {
            const canvas = document.getElementById('pdf-canvas-revisi-sqam');
            const ctx = canvas.getContext('2d');

            pdfDocRevisiSQAM.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleRevisiSQAM
                });
                const containerWidth = document.getElementById('pdf-preview-revisi-sqam').offsetWidth;
                const renderScale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({
                    scale: renderScale
                });

                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };

                page.render(renderContext).promise.then(function() {
                    currentPageRevisiSQAM = pageNum;
                    $('#pageNumRevisiSQAM').text(currentPageRevisiSQAM);
                    $('#pageCountRevisiSQAM').text(pdfDocRevisiSQAM.numPages);

                    // Update navigation buttons
                    $('#prevPageRevisiSQAM').prop('disabled', currentPageRevisiSQAM <= 1);
                    $('#nextPageRevisiSQAM').prop('disabled', currentPageRevisiSQAM >= pdfDocRevisiSQAM
                        .numPages);
                });
            });
        }

        // Load PDF for Revisi
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

                if (pdf.numPages > 1) {
                    $('#pdf-controls-revisi').show();
                }

                renderPageRevisiSQAM(currentPageRevisiSQAM);
            }).catch(function(error) {
                console.error("Error loading PDF for Revisi SQAM:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-revisi').html(errorMessage).show();
                $('#pdf-canvas-revisi-sqam').hide();
                $('#pdf-controls-revisi').hide();
            });
        }

        // Klik tombol revisi SQAM Supplier
        $(document).on('click', '.btn-revisi-sqam-supplier', function(e) {
            e.preventDefault();
            let id = $(this).data('id');

            // Ambil data revisi aktif dari backend
            $.get("{{ route('sqam-supplier.revisi', ':id') }}".replace(':id', id), function(data) {
                $('#revisi_sqam_supplier_document_id').val(data.id);
                $('#revisi_sqam_supplier_title_document').val(data.title_document);
                $('#revisi_sqam_supplier_category_document').val(data.category_document);
                $('#revisi_keterangan_display').text(data.keterangan || '—');

                // If there is a file document, display it in the canvas viewer
                if (data.file_document) {
                    const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                    loadPDFRevisiSQAM(fileUrl);
                    $('#revisi-sqam-supplier-file-preview').show(); // Show the file preview section
                } else {
                    $('#revisi-sqam-supplier-file-preview').hide(); // Hide if no file
                }

                // Set URL form to endpoint store revisi
                $('#formRevisiSQAMSupplier').attr('action',
                    "{{ route('sqam-supplier.storeRevisi', ':id') }}".replace(':id', data.id));
                $('#modalRevisiSQAMSupplier').modal('show');
            });
        });

        // Handle previous and next page navigation for Revisi
        $(document).on('click', '#prevPageRevisiSQAM', function() {
            if (currentPageRevisiSQAM > 1) {
                renderPageRevisiSQAM(currentPageRevisiSQAM - 1);
            }
        });

        $(document).on('click', '#nextPageRevisiSQAM', function() {
            if (pdfDocRevisiSQAM && currentPageRevisiSQAM < pdfDocRevisiSQAM.numPages) {
                renderPageRevisiSQAM(currentPageRevisiSQAM + 1);
            }
        });

        // Submit form revisi
        $('#formRevisiSQAMSupplier').on('submit', function(e) {
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

                        $('#modalRevisiSQAMSupplier').modal('hide');
                        sqamSupplierTable.ajax.reload();
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

                        // Reset error sebelumnya
                        $('#formRevisiSQAMSupplier').find('.form-control').removeClass('is-invalid');
                        $('#formRevisiSQAMSupplier').find('.invalid-feedback').text('');

                        // Tampilkan error baru
                        $.each(errors, function(field, messages) {
                            const input = $(`#formRevisiSQAMSupplier [name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#formRevisiSQAMSupplier #error-${field}`).text(messages[0]);
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

        // Reset modal state when closed
        $('#modalRevisiSQAMSupplier').on('hidden.bs.modal', function() {
            const form = $('#formRevisiSQAMSupplier');

            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#revisi-sqam-supplier-file-preview').hide();
        });
    </script>
@endpush
