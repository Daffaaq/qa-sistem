<!-- Modal Edit SQAM Supplier -->
<div class="modal fade" id="modalEditSQAMSupplier" tabindex="-1" aria-labelledby="modalEditSQAMSupplierLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditSQAMSupplier">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSQAMSupplierLabel">Edit SQAM Supplier</h5>
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

                    <input type="hidden" name="document_id" id="edit_sqam_supplier_id">

                    <div class="mb-3">
                        <label for="edit_title_document_sqam" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_title_document_sqam" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">Maksimal 255 karakter.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category_document_sqam" class="form-label">Kategori Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="category_document" id="edit_category_document_sqam"
                            class="form-control" value="SQAM Supplier" readonly>
                        <small class="form-text text-muted">Kategori ini tidak bisa diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_file_document_sqam" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_file_document_sqam" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <div class="mb-3" id="current-file-info-sqam" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-edit-sqam"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-sqam" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-edit" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageEditSQAM">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditSQAM">1</span> dari <span
                                    id="pageCountEditSQAM">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageEditSQAM">
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
        window.BASE_URL = "{{ asset('documents/sqam-supplier') }}"; // Dynamically set BASE_URL

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDocEditSQAM = null;
        let currentPageEditSQAM = 1;
        const scaleEditSQAM = 1.0;

        // Function to render PDF page
        function renderPageEditSQAM(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-sqam');
            const ctx = canvas.getContext('2d');

            pdfDocEditSQAM.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleEditSQAM
                });
                const containerWidth = document.getElementById('pdf-preview-edit-sqam').offsetWidth;
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
                    currentPageEditSQAM = pageNum;
                    $('#pageNumEditSQAM').text(currentPageEditSQAM);
                    $('#pageCountEditSQAM').text(pdfDocEditSQAM.numPages);

                    // Update navigation buttons
                    $('#prevPageEditSQAM').prop('disabled', currentPageEditSQAM <= 1);
                    $('#nextPageEditSQAM').prop('disabled', currentPageEditSQAM >= pdfDocEditSQAM.numPages);
                });
            });
        }

        // Load PDF
        function loadPDFEditSQAM(fileUrl) {
            pdfDocEditSQAM = null;
            currentPageEditSQAM = 1;
            $('#pdf-loading-message-edit').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-sqam').hide();
            $('#pdf-controls-edit').hide();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocEditSQAM = pdf;
                $('#pdf-loading-message-edit').hide();
                $('#pdf-canvas-edit-sqam').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit').show();
                }

                renderPageEditSQAM(currentPageEditSQAM);
            }).catch(function(error) {
                console.error("Error loading PDF for Edit SQAM:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-edit').html(errorMessage).show();
                $('#pdf-canvas-edit-sqam').hide();
                $('#pdf-controls-edit').hide();
            });
        }

        $(document).ready(function() {
            // Handle previous and next page navigation
            $(document).on('click', '#prevPageEditSQAM', function() {
                if (currentPageEditSQAM > 1) {
                    renderPageEditSQAM(currentPageEditSQAM - 1);
                }
            });

            $(document).on('click', '#nextPageEditSQAM', function() {
                if (pdfDocEditSQAM && currentPageEditSQAM < pdfDocEditSQAM.numPages) {
                    renderPageEditSQAM(currentPageEditSQAM + 1);
                }
            });

            // Open modal to edit SQAM supplier
            $(document).on('click', '.btn-edit-sqam-supplier', function(e) {
                e.preventDefault();
                let id = $(this).data('id');

                // Reset errors before AJAX request
                $('#formEditSQAMSupplier').find('.form-control').removeClass('is-invalid');
                $('#formEditSQAMSupplier').find('.invalid-feedback').text('');

                $.get("{{ route('sqam-supplier.edit', ['sqam_supplier' => ':id']) }}".replace(':id', id),
                    function(data) {
                        $('#edit_sqam_supplier_id').val(data.id);
                        $('#edit_title_document_sqam').val(data.title_document);
                        $('#edit_category_document_sqam').val(data.category_document ||
                        'SQAM Supplier');

                        if (data.file_document) {
                            const fileUrl = `${window.BASE_URL}/${data.file_document}`;

                            if (fileUrl.endsWith('.pdf')) {
                                loadPDFEditSQAM(fileUrl);
                                $('#current-file-info-sqam').show();
                            } else {
                                $('#pdf-loading-message-edit').html(
                                    '<p class="text-danger">Pratinjau hanya tersedia untuk file PDF.</p>'
                                ).show();
                                $('#pdf-canvas-edit-sqam').hide();
                                $('#pdf-controls-edit').hide();
                                $('#current-file-info-sqam').show();
                            }
                        } else {
                            $('#current-file-info-sqam').hide();
                        }

                        $('#formEditSQAMSupplier').attr('action',
                            "{{ route('sqam-supplier.update', ':id') }}".replace(':id', data.id));
                        $('#modalEditSQAMSupplier').modal('show');
                    }).fail(function() {
                    Swal.fire('Gagal!', 'Tidak dapat memuat data dokumen.', 'error');
                });
            });

            // Handle form submission for SQAM Supplier
            $('#formEditSQAMSupplier').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                formData.append('_method', 'PUT');

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
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message
                            });
                            $('#modalEditSQAMSupplier').modal('hide');
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

                            $('#formEditSQAMSupplier').find('.form-control').removeClass(
                                'is-invalid');
                            $('#formEditSQAMSupplier').find('.invalid-feedback').text('');

                            $.each(errors, function(field, messages) {
                                let input = $(
                                `#formEditSQAMSupplier [name="${field}"]`);
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

            // Reset modal state when closed
            $('#modalEditSQAMSupplier').on('hidden.bs.modal', function() {
                const form = $('#formEditSQAMSupplier');
                form[0].reset();
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                pdfDocEditSQAM = null;
                currentPageEditSQAM = 1;
                $('#current-file-info-sqam').hide();
                $('#pdf-loading-message-edit').text('Memuat pratinjau PDF...').show();
                $('#pdf-canvas-edit-sqam').hide();
                $('#pdf-controls-edit').hide();
            });
        });
    </script>
@endpush
