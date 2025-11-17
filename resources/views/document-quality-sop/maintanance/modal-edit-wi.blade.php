<!-- Modal Edit WI -->
<div class="modal fade" id="modalEditWIRep" tabindex="-1" aria-labelledby="modalEditWILabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditWI">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditWILabel">Edit WI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_wi_id">

                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.9rem;">
                        <strong>Catatan:</strong> File yang diunggah melalui form ini akan <strong>menimpa file
                            lama</strong>.
                        Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur <em>Revisi</em>.
                    </div>

                    <div class="mb-3">
                        <label for="edit_wi_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_wi_title_document" class="form-control">
                        <div class="invalid-feedback" id="error-edit-wi-title_document"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_wi_file_document" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_wi_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-wi-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_wi_keterangan" class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="edit_wi_keterangan" class="form-control" rows="3"
                            placeholder="Contoh: Dokumen SOP Maintanance untuk pengujian material tahun 2025"></textarea>
                        <div class="invalid-feedback" id="error-edit-keterangan"></div>
                        <small class="form-text text-muted">Jelaskan tujuan atau perubahan dokumen.</small>
                    </div>

                    <!-- Canvas for PDF Preview -->
                    <div class="mb-3" id="current-wi-file-info" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="pdf-preview-edit-wi"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-wi" style="width: 100%;"></canvas>
                            <p id="pdf-loading-message-edit-wi" class="p-3 text-muted">Memuat pratinjau PDF...</p>
                        </div>
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit-wi"
                            style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageEditWI">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditWI">1</span> dari <span
                                    id="pageCountEditWI">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageEditWI">
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
        window.BASE_URL = "{{ asset('documents/representative') }}"; // Set base URL dynamically

        // Load pdf.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let pdfDocEditWI = null;
        let currentPageEditWI = 1;
        const scaleEditWI = 1.0;

        // Function to render PDF page
        function renderPageEditWI(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-wi');
            const ctx = canvas.getContext('2d');

            pdfDocEditWI.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleEditWI
                });
                const containerWidth = document.getElementById('pdf-preview-edit-wi').offsetWidth;
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
                    currentPageEditWI = pageNum;
                    $('#pageNumEditWI').text(currentPageEditWI);
                    $('#pageCountEditWI').text(pdfDocEditWI.numPages);

                    // Update navigation buttons
                    $('#prevPageEditWI').prop('disabled', currentPageEditWI <= 1);
                    $('#nextPageEditWI').prop('disabled', currentPageEditWI >= pdfDocEditWI.numPages);
                });
            });
        }

        // Function to load the PDF
        function loadPDFEditWI(fileUrl) {
            pdfDocEditWI = null;
            currentPageEditWI = 1;
            $('#pdf-loading-message-edit-wi').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-wi').show();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocEditWI = pdf;
                renderPageEditWI(currentPageEditWI);
            }).catch(function(error) {
                console.error("Error loading PDF for Edit WI:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-edit-wi').html(errorMessage).show();
                $('#pdf-canvas-edit-wi').hide();
            });
        }

        // Fungsi untuk mengubah halaman PDF WI
        $(document).on('click', '#prevPageEditWI', function() {
            if (currentPageEditWI > 1) {
                renderPageEditWI(currentPageEditWI - 1);
            }
        });

        $(document).on('click', '#nextPageEditWI', function() {
            if (pdfDocEditWI && currentPageEditWI < pdfDocEditWI.numPages) {
                renderPageEditWI(currentPageEditWI + 1);
            }
        });

        // Open modal and load the PDF for editing
        $(document).on('click', '.btn-edit-wi', function(e) {
            e.preventDefault();

            let wiId = $(this).data('wi-id');

            // Call API to get the WI data
            $.get("{{ route('maintanance.edit-wi', ['wi' => ':wiId']) }}".replace(':wiId', wiId),
                function(data) {
                    $('#edit_wi_id').val(wiId);
                    $('#edit_wi_title_document').val(data.title_document);
                    $('#edit_wi_keterangan').val(data.keterangan);

                    if (data.file_document) {
                        const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                        loadPDFEditWI(fileUrl);
                        $('#current-wi-file-info').show();
                    } else {
                        $('#current-wi-file-info').hide();
                    }

                    // Use route() to dynamically set the form action URL
                    const updateWiUrl = "{{ route('maintanance.update-wi', ['wi' => ':wiId']) }}".replace(
                        ':wiId', wiId);
                    $('#formEditWI').attr('action', updateWiUrl);

                    // Show modal
                    $('#modalEditWIRep').modal('show');
                });
        });

        $('#formEditWI').on('submit', function(e) {
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
                method: 'POST', // tetap POST, method spoofing _method = PUT
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
                        $('#modalEditWIRep').modal('hide');
                        // Reload list SOP (partial)
                        $.ajax({
                            url: "{{ route('maintanance.content-list-partial') }}",
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
                            $(`#error-edit-wi-${field}`).text(messages[0]);
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

        $('#modalEditWIRep').on('hidden.bs.modal', function() {
            const form = $('#formEditWI');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-wi-file-info').hide();
        });
    </script>
@endpush
