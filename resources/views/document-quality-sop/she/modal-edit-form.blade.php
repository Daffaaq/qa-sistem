<!-- Modal Edit Form -->
<div class="modal fade" id="modalEditForm" tabindex="-1" aria-labelledby="modalEditFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditFormLabel">Edit Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_form_id">

                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.9rem;">
                        <strong>Catatan:</strong> File yang diunggah melalui form ini akan <strong>menimpa file
                            lama</strong>.
                        Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur <em>Revisi</em>.
                    </div>

                    <div class="mb-3">
                        <label for="edit_form_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_form_title_document" class="form-control">
                        <div class="invalid-feedback" id="error-edit-form-title_document"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_form_file_document" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_form_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-form-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_form_keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_form_keterangan" class="form-control" rows="3"
                            placeholder="Contoh: Dokumen SOP PPIC untuk pengujian material tahun 2025"></textarea>
                        <div class="invalid-feedback" id="error-edit-keterangan"></div>
                        <small class="form-text text-muted">Jelaskan tujuan atau perubahan dokumen.</small>
                    </div>

                    <!-- Canvas for PDF Preview -->
                    <div class="mb-3" id="current-form-file-info" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-edit-form"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-form" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-edit-form" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit-form"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageEditForm">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditForm">1</span> dari <span
                                    id="pageCountEditForm">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageEditForm">
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
    <script type="module">
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        window.BASE_URL = "{{ asset('documents/she') }}"; // Set base URL dynamically

        // Load pdf.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let pdfDocEditForm = null;
        let currentPageEditForm = 1;
        const scaleEditForm = 1.0;

        // Function to render PDF page
        function renderPageEditForm(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-form');
            const ctx = canvas.getContext('2d');

            pdfDocEditForm.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleEditForm
                });
                const containerWidth = document.getElementById('pdf-preview-edit-form').offsetWidth;
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
                    currentPageEditForm = pageNum;
                    $('#pageNumEditForm').text(currentPageEditForm);
                    $('#pageCountEditForm').text(pdfDocEditForm.numPages);

                    // Update navigation buttons
                    $('#prevPageEditForm').prop('disabled', currentPageEditForm <= 1);
                    $('#nextPageEditForm').prop('disabled', currentPageEditForm >= pdfDocEditForm.numPages);
                });
            });
        }

        // Function to load the PDF
        function loadPDFEditForm(fileUrl) {
            pdfDocEditForm = null;
            currentPageEditForm = 1;
            $('#pdf-loading-message-edit-form').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-form').hide();
            $('#pdf-controls-edit-form').hide();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocEditForm = pdf;
                $('#pdf-loading-message-edit-form').hide();
                $('#pdf-canvas-edit-form').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit-form').show();
                }

                renderPageEditForm(currentPageEditForm);
            }).catch(function(error) {
                console.error("Error loading PDF for Edit Form:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-edit-form').html(errorMessage).show();
                $('#pdf-canvas-edit-form').hide();
                $('#pdf-controls-edit-form').hide();
            });
        }

        // Fungsi untuk navigasi ke halaman sebelumnya
        $('#prevPageEditForm').on('click', function() {
            if (currentPageEditForm > 1) {
                currentPageEditForm--;
                renderPageEditForm(currentPageEditForm);
            }
        });

        // Fungsi untuk navigasi ke halaman berikutnya
        $('#nextPageEditForm').on('click', function() {
            if (currentPageEditForm < pdfDocEditForm.numPages) {
                currentPageEditForm++;
                renderPageEditForm(currentPageEditForm);
            }
        });

        // Open modal and load the PDF for editing
        $(document).on('click', '.btn-edit-form', function(e) {
            e.preventDefault();

            const formId = $(this).data('form-id');

            $.get("{{ route('she.edit-form', ':formId') }}".replace(':formId', formId),
                function(data) {
                    $('#edit_form_id').val(formId);
                    $('#edit_form_title_document').val(data.title_document);
                    $('#edit_form_keterangan').val(data.keterangan);

                    if (data.file_document) {
                        const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                        loadPDFEditForm(fileUrl);
                        $('#current-form-file-info').show();
                    } else {
                        $('#current-form-file-info').hide();
                    }

                    $('#formEditForm').attr('action',
                        "{{ route('she.update-form', ':formId') }}".replace(
                            ':formId', formId));

                    $('#modalEditForm').modal('show');
                });
        });

        $('#formEditForm').on('submit', function(e) {
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
                method: 'POST', // tetap POST karena _method=PUT
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

                        $('#modalEditForm').modal('hide');
                        // Reload list SOP (partial)
                        $.ajax({
                            url: "{{ route('she.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle(); // <<< ini WAJIB ditambahkan
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal memuat ulang daftar SOP.'
                                });
                            }
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
                            form.find(`[name="${field}"]`).addClass('is-invalid');
                            $(`#error-edit-form-${field}`).text(messages[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan pada server.'
                        });
                    }
                }
            });
        });

        // Reset modal saat ditutup
        $('#modalEditForm').on('hidden.bs.modal', function() {
            const form = $('#formEditForm');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-form-file-info').hide();
        });
    </script>
@endpush
