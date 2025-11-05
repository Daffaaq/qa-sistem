<!-- Modal Revisi Form QA QC -->
<div class="modal fade" id="modalReviseForm" tabindex="-1" aria-labelledby="modalReviseFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formReviseForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReviseFormLabel">Revisi Dokumen Form Engineering</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle"></i> Revisi baru akan otomatis menonaktifkan versi lama dan
                        mengaktifkan versi baru.
                    </div>
                    <input type="hidden" name="form_id" id="revise_form_id">

                    <div class="mb-3">
                        <label for="title_document_form_rev" class="form-label">Judul Dokumen Baru
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_form_rev" class="form-control"
                            readonly>
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Contoh: "Revisi Form QC PT IPG Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_form" class="form-label">Upload Dokumen Revisi (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_form" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Hanya format PDF. Ukuran maksimal 100MB.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dokumen Form Aktif Saat Ini</label>
                        <div id="pdf-preview-Revisi-form"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <!-- Canvas for PDF Preview -->
                            <canvas id="current_form_pdf_canvas" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-revise-form" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-revise-form"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                id="prevPageReviseForm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumReviseForm">1</span> dari <span
                                    id="pageCountReviseForm">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="nextPageReviseForm">
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Pratinjau dokumen Form saat ini sebelum direvisi.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">Simpan Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        window.BASE_URL = "{{ asset('documents/engineering') }}"; // Set base URL dynamically

        // === OPEN MODAL REVISI FORM ===
        $(document).on('click', '.btn-revise-form', function(e) {
            e.preventDefault();
            const formId = $(this).data('form-id');

            // Use the route helper for dynamic URL
            const actionUrl = "{{ route('engineering.revise-form', ':formId') }}".replace(':formId', formId);
            $('#formReviseForm').attr('action', actionUrl);

            $('#modalReviseForm').modal('show').on('shown.bs.modal', function() {
                // Fetch data for the form document and preview
                $.get("{{ route('engineering.revisi-form', ':formId') }}".replace(':formId', formId),
                    function(data) {
                        $('#revise_form_id').val(formId);
                        $('#title_document_form_rev').val(data.title_document || 'No Title');

                        if (data.file_document) {
                            const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                            loadPDFPreviewForm(fileUrl); // Load PDF using Canvas
                        } else {
                            $('#current_form_pdf_canvas').hide();
                        }
                    });
            });
        });

        // === PDF.js untuk pratinjau dokumen Form ===
        let pdfDocForm = null;
        let currentPageForm = 1;
        const scaleForm = 1.0;

        // Fungsi untuk merender halaman PDF Form
        function renderPageForm(pageNum) {
            const canvas = document.getElementById('current_form_pdf_canvas');
            const ctx = canvas.getContext('2d');

            pdfDocForm.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleForm
                });

                // Menghitung lebar kontainer dan mengatur skala render
                const containerWidth = document.getElementById('current_form_pdf_canvas').parentElement.offsetWidth;
                const renderScale = containerWidth / viewport.width;

                const scaledViewport = page.getViewport({
                    scale: renderScale
                });

                // Mengatur ukuran canvas untuk menyesuaikan dengan viewport yang sudah disesuaikan
                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };

                page.render(renderContext).promise.then(function() {
                    currentPageForm = pageNum;
                    $('#pdf-loading-message-revise-form').hide();
                    $('#pageNumReviseForm').text(currentPageForm);
                    $('#pageCountReviseForm').text(pdfDocForm.numPages);

                    // Update navigation buttons
                    $('#prevPageReviseForm').prop('disabled', currentPageForm <= 1);
                    $('#nextPageReviseForm').prop('disabled', currentPageForm >= pdfDocForm.numPages);
                });
            });
        }

        // Fungsi untuk memuat dan menampilkan pratinjau PDF Form
        function loadPDFPreviewForm(fileUrl) {
            pdfDocForm = null;
            currentPageForm = 1;
            $('#pdf-loading-message-revise-form').text('Memuat pratinjau PDF...').show();
            $('#current_form_pdf_canvas').show();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocForm = pdf;
                renderPageForm(currentPageForm);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-revise-form').html(errorMessage).show();
                $('#current_form_pdf_canvas').hide();
            });
        }

        // Fungsi untuk mengubah halaman PDF Form
        $(document).on('click', '#prevPageReviseForm', function() {
            if (currentPageForm > 1) {
                renderPageForm(currentPageForm - 1);
            }
        });

        $(document).on('click', '#nextPageReviseForm', function() {
            if (pdfDocForm && currentPageForm < pdfDocForm.numPages) {
                renderPageForm(currentPageForm + 1);
            }
        });

        // === SUBMIT FORM REVISI FORM ===
        $('#formReviseForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = $(this).attr('action');

            Swal.fire({
                title: 'Menyimpan revisi...',
                text: 'Mohon tunggu beberapa saat.',
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        });

                        $('#formReviseForm')[0].reset();
                        $('#modalReviseForm').modal('hide');

                        // Optional: Refresh list
                        $.ajax({
                            url: "{{ route('engineering.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat memuat ulang daftar Form.'
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
                        const errors = xhr.responseJSON.errors;
                        $('#formReviseForm').find('.form-control').removeClass('is-invalid');
                        $('#formReviseForm').find('.invalid-feedback').text('');

                        $.each(errors, function(field, messages) {
                            const input = $(`#formReviseForm [name="${field}"]`);
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

        // === RESET FORM FORM ===
        $('#modalReviseForm').on('hidden.bs.modal', function() {
            const form = $('#formReviseForm');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current_form_pdf_canvas').hide();
        });
    </script>
@endpush
