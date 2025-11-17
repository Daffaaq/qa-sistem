<!-- Modal Revisi WI QA QC -->
<div class="modal fade" id="modalReviseWI" tabindex="-1" aria-labelledby="modalReviseWILabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formReviseWI">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalReviseWILabel">Revisi Dokumen WI Maintanance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle"></i> Revisi baru akan otomatis menonaktifkan versi lama dan
                        mengaktifkan versi baru sebagai revisi berikutnya.
                    </div>
                    <input type="hidden" name="wi_id" id="revise_wi_id">

                    <div class="mb-3">
                        <label for="title_document_wi_rev" class="form-label">Judul Dokumen Baru
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title_document" id="title_document_wi_rev" class="form-control"
                            readonly>
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">
                            Contoh: "Revisi WI QC PT IPG Tahun 2025".
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="file_document_wi" class="form-label">Upload Dokumen Revisi (PDF)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file_document" id="file_document_wi" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Hanya format PDF. Ukuran maksimal 100MB.</small>
                    </div>

                    <div class="mb-3">
                        <label for="revise_keterangan_wi_display" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="revise_keterangan_wi_display" class="form-control" rows="3" readonly
                            placeholder="Contoh: Dokumen SOP QA QC untuk pengujian material tahun 2025"></textarea>
                        <div class="invalid-feedback" id="error-edit-keterangan"></div>
                        <small class="form-text text-muted">Jelaskan tujuan atau perubahan dokumen.</small>
                    </div>

                    <div class="mb-3" id="current-file-info-wi-revisi" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-Revisi-wi"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="current_wi_pdf_canvas" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-revise-wi" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-revise-wi"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageRevisiWI">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumRevisiWI">1</span> dari <span
                                    id="pageCountRevisiWI">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageRevisiWI">
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

        // === OPEN MODAL REVISI WI ===
        $(document).on('click', '.btn-revise-wi', function(e) {
            e.preventDefault();
            const wiId = $(this).data('wi-id');
            $('#revise_wi_id').val(wiId);

            const actionUrl = `{{ route('maintanance.revise-wi', ':id') }}`.replace(':id', wiId);
            $('#formReviseWI').attr('action', actionUrl);

            // Ambil data judul & file aktif dari server
            $.get("{{ route('maintanance.revisi-wi', ':id') }}".replace(':id', wiId), function(data) {
                $('#title_document_wi_rev').val(data.title_document || '');
                $('#revise_keterangan_wi_display').val(data.keterangan || '');

                // Tampilkan PDF preview jika ada file
                if (data.file_document) {
                    const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                    loadPDFPreviewWI(fileUrl);
                    $('#current-file-info-wi-revisi').show();
                } else {
                    $('#current-file-info-wi-revisi').hide();
                }
            });

            $('#modalReviseWI').modal('show');
        });

        // === PDF.js untuk pratinjau dokumen WI ===
        let pdfDocWI = null;
        let currentPageWI = 1;
        const scaleWI = 1.0;

        // Fungsi untuk merender halaman PDF WI
        function renderPageWI(pageNum) {
            const canvas = document.getElementById('current_wi_pdf_canvas');
            const ctx = canvas.getContext('2d');

            pdfDocWI.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleWI
                });

                // Menghitung lebar kontainer dan mengatur skala render
                const containerWidth = document.getElementById('current_wi_pdf_canvas').parentElement.offsetWidth;
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
                    currentPageWI = pageNum;
                    $('#pdf-loading-message-revise-wi').hide();
                    $('#pageNumRevisiWI').text(currentPageWI);
                    $('#pageCountRevisiWI').text(pdfDocWI.numPages);

                    // Update navigation buttons
                    $('#prevPageRevisiWI').prop('disabled', currentPageWI <= 1);
                    $('#nextPageRevisiWI').prop('disabled', currentPageWI >= pdfDocWI.numPages);
                });
            });
        }

        // Fungsi untuk memuat dan menampilkan pratinjau PDF WI
        function loadPDFPreviewWI(fileUrl) {
            pdfDocWI = null;
            currentPageWI = 1;
            $('#pdf-loading-message-revise-wi').text('Memuat pratinjau PDF...').show();
            $('#current_wi_pdf_canvas').show();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocWI = pdf;
                renderPageWI(currentPageWI);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-revise-wi').html(errorMessage).show();
                $('#current_wi_pdf_canvas').hide();
            });
        }

        // Fungsi untuk mengubah halaman PDF WI
        $(document).on('click', '#prevPageRevisiWI', function() {
            if (currentPageWI > 1) {
                renderPageWI(currentPageWI - 1);
            }
        });

        $(document).on('click', '#nextPageRevisiWI', function() {
            if (pdfDocWI && currentPageWI < pdfDocWI.numPages) {
                renderPageWI(currentPageWI + 1);
            }
        });

        // === SUBMIT FORM REVISI WI ===
        $('#formReviseWI').on('submit', function(e) {
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

                        $('#formReviseWI')[0].reset();
                        $('#modalReviseWI').modal('hide');

                        // Refresh count & list
                        $.get("{{ route('maintanance.count-wi') }}", function(res) {
                            $('#total-documents-wi').text(res.wiCount);
                        });

                        $.ajax({
                            url: "{{ route('maintanance.content-list-partial') }}",
                            method: 'GET',
                            success: function(html) {
                                $('#sop-rep-list-container').html(html);
                                initTreeToggle();
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Tidak dapat memuat ulang daftar WI.'
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
                        $('#formReviseWI').find('.form-control').removeClass('is-invalid');
                        $('#formReviseWI').find('.invalid-feedback').text('');

                        $.each(errors, function(field, messages) {
                            const input = $(`#formReviseWI [name="${field}"]`);
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
        $('#modalReviseWI').on('hidden.bs.modal', function() {
            const form = $('#formReviseWI');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current_wi_pdf_canvas').hide();
        });
    </script>
@endpush
