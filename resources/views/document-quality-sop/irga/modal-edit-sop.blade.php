<!-- Modal Edit SOP -->
<div class="modal fade" id="modalEditSOPRep" tabindex="-1" aria-labelledby="modalEditSOPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditSOP">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSOPLabel">Edit SOP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.9rem;">
                        <strong>Catatan:</strong> File yang diunggah melalui form ini akan <strong>menimpa file
                            lama</strong>.
                        Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur <em>Revisi</em>.
                    </div>

                    <input type="hidden" name="sop_id" id="edit_sop_id">

                    <div class="mb-3">
                        <label for="edit_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_title_document" class="form-control">
                        <div class="invalid-feedback" id="error-edit-title_document"></div>
                        <small class="form-text text-muted">Maksimal 255 karakter.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category_document" class="form-label">Kategori Dokumen</label>
                        <input type="text" class="form-control" value="IRGA" readonly>
                        <small class="form-text text-muted">Kategori ini tidak bisa diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_file_document" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"
                            placeholder="Contoh: Dokumen SOP Irga untuk pengujian material tahun 2025"></textarea>
                        <div class="invalid-feedback" id="error-edit-keterangan"></div>
                        <small class="form-text text-muted">Jelaskan tujuan atau perubahan dokumen.</small>
                    </div>

                    <!-- Canvas for PDF Preview -->
                    <div class="mb-3" id="current-file-info" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-edit-sop"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-sop" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-edit-sop" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit-sop"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageEditSOP">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditSOP">1</span> dari <span
                                    id="pageCountEditSOP">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageEditSOP">
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
        window.BASE_URL = "{{ asset('documents/irga') }}"; // Set base URL dynamically

        // Load pdf.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let pdfDocEditSOP = null;
        let currentPageEditSOP = 1;
        const scaleEditSOP = 1.0;

        // Function to render PDF page
        function renderPageEditSOP(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-sop');
            const ctx = canvas.getContext('2d');

            pdfDocEditSOP.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleEditSOP
                });
                const containerWidth = document.getElementById('pdf-preview-edit-sop').offsetWidth;
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
                    currentPageEditSOP = pageNum;
                    $('#pageNumEditSOP').text(currentPageEditSOP);
                    $('#pageCountEditSOP').text(pdfDocEditSOP.numPages);

                    // Update navigation buttons
                    $('#prevPageEditSOP').prop('disabled', currentPageEditSOP <= 1);
                    $('#nextPageEditSOP').prop('disabled', currentPageEditSOP >= pdfDocEditSOP.numPages);
                });
            });
        }

        // Function to load the PDF
        function loadPDFEditSOP(fileUrl) {
            pdfDocEditSOP = null;
            currentPageEditSOP = 1;
            $('#pdf-loading-message-edit-sop').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-sop').hide();
            $('#pdf-controls-edit-sop').hide();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocEditSOP = pdf;
                $('#pdf-loading-message-edit-sop').hide();
                $('#pdf-canvas-edit-sop').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit-sop').show();
                }

                renderPageEditSOP(currentPageEditSOP);
            }).catch(function(error) {
                console.error("Error loading PDF for Edit SOP:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-edit-sop').html(errorMessage).show();
                $('#pdf-canvas-edit-sop').hide();
                $('#pdf-controls-edit-sop').hide();
            });
        }

        // Open modal and load the PDF for editing
        $(document).on('click', '.btn-edit-sop-rep', function(e) {
            e.preventDefault();

            let sopId = $(this).data('sop-id');

            // Call API to get the SOP data
            // Call API to get the SOP data using the route helper
            $.get("{{ route('irga.edit-sop', ['sop' => ':sopId']) }}".replace(':sopId', sopId),
                function(data) {
                    $('#edit_sop_id').val(sopId);
                    $('#edit_title_document').val(data.title_document);
                    $('#edit_keterangan').val(data.keterangan);

                    if (data.file_document) {
                        const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                        loadPDFEditSOP(fileUrl);
                        $('#current-file-info').show();
                    } else {
                        $('#current-file-info').hide();
                    }

                    // Use route() to dynamically set the form action URL
                    const updateSopUrl =
                        "{{ route('irga.update-sop', ['sop' => ':sopId']) }}".replace(
                            ':sopId', sopId);
                    $('#formEditSOP').attr('action', updateSopUrl);

                    // Show modal
                    $('#modalEditSOPRep').modal('show');
                });

        });

        $('#formEditSOP').on('submit', function(e) {
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
                method: 'POST', // _method akan tetap PUT
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

                        $('#modalEditSOPRep').modal('hide');

                        // Reload list SOP (partial)
                        $.ajax({
                            url: "{{ route('irga.content-list-partial') }}",
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

        // Navigating between pages
        $(document).on('click', '#prevPageEditSOP', function() {
            if (currentPageEditSOP > 1) {
                renderPageEditSOP(currentPageEditSOP - 1);
            }
        });

        $(document).on('click', '#nextPageEditSOP', function() {
            if (pdfDocEditSOP && currentPageEditSOP < pdfDocEditSOP.numPages) {
                renderPageEditSOP(currentPageEditSOP + 1);
            }
        });

        // Reset modal when it's closed
        $('#modalEditSOPRep').on('hidden.bs.modal', function() {
            const form = $('#formEditSOP');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-file-info').hide();
            pdfDocEditSOP = null;
            currentPageEditSOP = 1;
        });
    </script>
@endpush
