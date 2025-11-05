<!-- Modal Revisi SOP QA QC -->
<div class="modal fade" id="modalReviseSOP" tabindex="-1" aria-labelledby="modalReviseSOPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formReviseSOP">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReviseSOPLabel">Revisi Dokumen SOP Management Representative</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle"></i> Revisi baru akan otomatis menonaktifkan versi lama dan
                        mengaktifkan versi baru sebagai revisi berikutnya.
                    </div>
                    <input type="hidden" name="sop_id" id="revise_sop_id">

                    <div class="mb-3">
                        <label for="title_document_revise" class="form-label">Judul Dokumen Baru
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_revise" class="form-control"
                            readonly>
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Contoh: "Revisi SOP QC PT IPG Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_revise" class="form-label">Upload Dokumen Revisi (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_revise" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Hanya format PDF. Ukuran maksimal 100MB.</small>
                    </div>

                    <div class="mb-3" id="current-file-info-revisi" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-revisi-sop"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="current_pdf_canvas" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-revisi-sop" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-revisi-sop"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageRevisiSOP">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumRevisiSOP">1</span> dari <span
                                    id="pageCountRevisiSOP">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageRevisiSOP">
                                Next <i class="ti ti-chevron-right"></i>
                            </button>
                        </div>
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
        // Tentukan BASE_URL untuk path penyimpanan
        window.BASE_URL = "{{ asset('documents') }}";

        // === OPEN MODAL REVISI ===
        $(document).on('click', '.btn-revise-sop', function(e) {
            e.preventDefault();
            const sopId = $(this).data('sop-id');
            $('#revise_sop_id').val(sopId);

            const actionUrl = `{{ route('human-capital.revise-sop', ':id') }}`.replace(':id', sopId);
            $('#formReviseSOP').attr('action', actionUrl);

            // Ambil data judul & file aktif dari server
            $.get("{{ route('human-capital.revisi-sop', ':id') }}".replace(':id', sopId), function(data) {
                $('#title_document_revise').val(data.title_document || '');

                // Tampilkan PDF preview jika ada file
                if (data.file_document) {
                    const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                    loadPDFPreview(fileUrl);
                    $('#current-file-info-revisi').show();
                } else {
                    $('#current-file-info-revisi').hide();
                }
            });

            $('#modalReviseSOP').modal('show');
        });

        // === PDF.js untuk pratinjau dokumen ===
        let pdfDoc = null;
        let currentPage = 1;
        const scale = 1.0;

        // Fungsi untuk merender halaman PDF
        function renderPage(pageNum) {
            const canvas = document.getElementById('current_pdf_canvas');
            const ctx = canvas.getContext('2d');

            pdfDoc.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scale
                });

                // Menghitung lebar kontainer dan mengatur skala render
                const containerWidth = document.getElementById('pdf-preview-revisi-sop').offsetWidth;
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
                    currentPage = pageNum;
                    $('#pageNumRevisiSOP').text(currentPage);
                    $('#pageCountRevisiSOP').text(pdfDoc.numPages);

                    // Update navigation buttons
                    $('#prevPageRevisiSOP').prop('disabled', currentPage <= 1);
                    $('#nextPageRevisiSOP').prop('disabled', currentPage >= pdfDoc.numPages);
                });
            });
        }

        // Fungsi untuk memuat dan menampilkan pratinjau PDF
        function loadPDFPreview(fileUrl) {
            pdfDoc = null;
            currentPage = 1;
            $('#pdf-loading-message').text('Memuat pratinjau PDF...').show();
            $('#current_pdf_canvas').show();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDoc = pdf;
                renderPage(currentPage);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message').html(errorMessage).show();
                $('#current_pdf_canvas').hide();
            });
        }

        // Fungsi untuk mengubah halaman PDF
        $(document).on('click', '#prevPageRevisiSOP', function() {
            if (currentPage > 1) {
                renderPage(currentPage - 1);
            }
        });

        $(document).on('click', '#nextPageRevisiSOP', function() {
            if (pdfDoc && currentPage < pdfDoc.numPages) {
                renderPage(currentPage + 1);
            }
        });

        // === SUBMIT FORM REVISI ===
        $('#formReviseSOP').on('submit', function(e) {
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

                        $('#formReviseSOP')[0].reset();
                        $('#modalReviseSOP').modal('hide');

                        // Refresh count & list
                        $.get("{{ route('human-capital.count-sop') }}", function(res) {
                            $('#total-documents-sop-rep').text(res.sopCount);
                        });

                        $.ajax({
                            url: "{{ route('human-capital.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat memuat ulang daftar SOP.'
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
                        $('#formReviseSOP').find('.form-control').removeClass('is-invalid');
                        $('#formReviseSOP').find('.invalid-feedback').text('');

                        $.each(errors, function(field, messages) {
                            const input = $(`#formReviseSOP [name="${field}"]`);
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

        // === RESET FORM KETIKA MODAL DITUTUP ===
        $('#modalReviseSOP').on('hidden.bs.modal', function() {
            const form = $('#formReviseSOP');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current_pdf_canvas').hide();
        });
    </script>
@endpush
