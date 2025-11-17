<!-- Modal Edit SQAM Customer -->
<div class="modal fade" id="modalEditSQAMCustomer" tabindex="-1" aria-labelledby="modalEditSQAMCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditSQAMCustomer">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSQAMCustomerLabel">Edit Dokumen SQAM Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.9rem;">
                            <strong>Catatan:</strong> File yang diunggah melalui form ini akan
                            <strong>menimpa file lama</strong>.
                            Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur <em>Revisi</em>.
                        </div>
                    </div>

                    <input type="hidden" name="document_id" id="edit_document_id">

                    <div class="mb-3">
                        <label for="edit_title_document" class="form-label">Judul Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="title_document" id="edit_title_document" class="form-control">
                        <div class="invalid-feedback" id="error-title_document"></div>
                        <small class="form-text text-muted">Maksimal 255 karakter.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_category_document" class="form-label">Kategori Dokumen <span
                                class="text-danger">*</span></label>
                        <input type="text" name="category_document" id="edit_category_document" class="form-control"
                            readonly>
                        <small class="form-text text-muted">Kategori ini tidak bisa diubah.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_file_document" class="form-label">Upload Dokumen Baru (PDF)</label>
                        <input type="file" name="file_document" id="edit_file_document" accept="application/pdf"
                            class="form-control">
                        <div class="invalid-feedback" id="error-file_document"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan Dokumen <span
                                class="text-danger">*</span></label>
                        <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"
                            placeholder="Wajib diisi. Contoh: Dokumen SQAM untuk customer PT ABC tahun 2025"></textarea>
                        <div class="invalid-feedback" id="error-keterangan"></div>
                        <small class="form-text text-muted">
                            Jelaskan tujuan atau isi dokumen ini.
                        </small>
                    </div>

                    <div class="mb-3" id="current-file-info" style="display: none;">
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
                    <button type="submit" class="btn btn-primary" id="btnSubmitEdit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        // Initialize pdf.js
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
            pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';


        let pdfDocCustomer = null;
        let currentPageCustomer = 1;
        const scaleCustomer = 1.0;

        // Function to render PDF page
        function renderPageCustomer(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-sqam');
            const ctx = canvas.getContext('2d');

            pdfDocCustomer.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleCustomer
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
                    currentPageCustomer = pageNum;
                    $('#pageNumEditSQAM').text(currentPageCustomer);
                    $('#pageCountEditSQAM').text(pdfDocCustomer.numPages);

                    // Update navigation buttons
                    $('#prevPageEditSQAM').prop('disabled', currentPageCustomer <= 1);
                    $('#nextPageEditSQAM').prop('disabled', currentPageCustomer >= pdfDocCustomer.numPages);
                });
            });
        }

        // Load PDF for Edit SQAM Customer
        function loadPDFCustomer(fileUrl) {
            pdfDocCustomer = null;
            currentPageCustomer = 1;
            $('#pdf-loading-message-edit').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-sqam').hide();
            $('#pdf-controls-edit').hide();

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocCustomer = pdf;
                $('#pdf-loading-message-edit').hide();
                $('#pdf-canvas-edit-sqam').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit').show();
                }

                renderPageCustomer(currentPageCustomer);
            }).catch(function(error) {
                console.error("Error loading PDF for Edit SQAM Customer:", error);
                let errorMessage =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download untuk cek</a>.</p>`;
                $('#pdf-loading-message-edit').html(errorMessage).show();
                $('#pdf-canvas-edit-sqam').hide();
                $('#pdf-controls-edit').hide();
            });
        }

        // Klik tombol edit SQAM Customer
        $(document).on('click', '.btn-edit-sqam-customer', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            // Set route URLs dynamically with id
            const sqamCustomerEditRoute = "{{ route('sqam-customer.edit', ['sqam_customer' => ':id']) }}".replace(
                ':id', id);
            const sqamCustomerUpdateRoute = "{{ route('sqam-customer.update', ['sqam_customer' => ':id']) }}"
                .replace(':id', id);

            $.get(sqamCustomerEditRoute, function(data) {
                $('#edit_document_id').val(data.id);
                $('#edit_title_document').val(data.title_document);
                $('#edit_category_document').val(data.category_document || 'Dokumen SQAM Customer');
                $('#edit_keterangan').val(data.keterangan || '');

                if (data.file_document) {
                    const fileUrl = `${window.BASE_URL}/${data.file_document}`;
                    loadPDFCustomer(fileUrl); // Load and render the PDF
                    $('#current-file-info').show();
                } else {
                    $('#current-file-info').hide();
                }

                // Set form action URL for updating
                $('#formEditSQAMCustomer').attr('action', sqamCustomerUpdateRoute);

                // Show the modal
                $('#modalEditSQAMCustomer').modal('show');
            }).fail(function() {
                Swal.fire('Gagal!', 'Tidak dapat memuat data dokumen.', 'error');
            });
        });

        // Handle previous and next page navigation for Edit SQAM Customer
        $(document).on('click', '#prevPageEditSQAM', function() {
            if (currentPageCustomer > 1) {
                renderPageCustomer(currentPageCustomer - 1);
            }
        });

        $(document).on('click', '#nextPageEditSQAM', function() {
            if (pdfDocCustomer && currentPageCustomer < pdfDocCustomer.numPages) {
                renderPageCustomer(currentPageCustomer + 1);
            }
        });

        // Submit form for Edit SQAM Customer
        $('#formEditSQAMCustomer').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let formData = new FormData(this);
            formData.append('_method', 'PUT');

            Swal.fire({
                title: 'Menyimpan perubahan...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
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

                        $('#modalEditSQAMCustomer').modal('hide');
                        sqamCustomerTable.ajax.reload(); // pastikan sesuai nama DataTable kamu
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

                        // Reset error lama
                        $('#formEditSQAMCustomer').find('.form-control').removeClass('is-invalid');
                        $('#formEditSQAMCustomer').find('.invalid-feedback').text('');

                        // Tampilkan error baru
                        $.each(errors, function(field, messages) {
                            const input = $(`#formEditSQAMCustomer [name="${field}"]`);
                            input.addClass('is-invalid');
                            $(`#formEditSQAMCustomer #error-${field}`).text(messages[0]);
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

        // Reset form and error messages when modal is closed
        $('#modalEditSQAMCustomer').on('hidden.bs.modal', function() {
            const form = $('#formEditSQAMCustomer');

            form[0].reset(); // Reset all input fields
            form.find('.form-control').removeClass('is-invalid'); // Remove error class
            form.find('.invalid-feedback').text(''); // Clear error messages
            $('#current-file-info').hide(); // Hide PDF file preview
        });
    </script>
@endpush
