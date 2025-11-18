<!-- Modal Edit Customer Audit -->
<div class="modal fade" id="modalEditCustomerAudit" tabindex="-1" aria-labelledby="modalEditCustomerAuditLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditCustomerAudit">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditCustomerAuditLabel">Edit Customer Audit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- KIRI: Form Input (lebar bebas, tinggi dinamis) -->
                        <div class="col-lg-7">
                            <div class="mb-3">
                                <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.9rem;">
                                    <strong>Catatan:</strong> File yang diunggah akan <strong>menimpa file
                                        lama</strong>.
                                    Gunakan fitur <em>Revisi</em> jika ingin menyimpan versi lama.
                                </div>
                            </div>

                            <input type="hidden" name="customer_audit_id" id="edit_customer_audit_id">

                            <div class="mb-3">
                                <label for="edit_nama_event" class="form-label">Nama Event <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="nama_event" id="edit_nama_event" class="form-control">
                                <div class="invalid-feedback" id="error-edit-nama_event"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_tanggal_mulai_event" class="form-label">Tanggal Mulai <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai_event" id="edit_tanggal_mulai_event"
                                    class="form-control">
                                <div class="invalid-feedback" id="error-edit-tanggal_mulai_event"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_tanggal_selesai_event" class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai_event" id="edit_tanggal_selesai_event"
                                    class="form-control">
                                <div class="invalid-feedback" id="error-edit-tanggal_selesai_event"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_deskripsi_event" class="form-label">Deskripsi Event</label>
                                <textarea name="deskripsi_event" id="edit_deskripsi_event" class="form-control" rows="4"></textarea>
                                <div class="invalid-feedback" id="error-edit-deskripsi_event"></div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_file_evident" class="form-label">Upload Bukti File (PDF)</label>
                                <input type="file" name="file_evident" id="edit_file_evident" class="form-control"
                                    accept=".pdf">
                                <div class="invalid-feedback" id="error-edit-file_evident"></div>
                                <small class="text-muted">Kosongkan jika tidak ingin ganti file.</small>
                            </div>

                            <div class="mb-3">
                                <label for="edit_logo_customer" class="form-label">Logo Customer</label>
                                <input type="file" name="logo_customer" id="edit_logo_customer" class="form-control"
                                    accept="image/*">
                                <div class="invalid-feedback" id="error-edit-logo_customer"></div>
                                <small class="text-muted">Kosongkan jika tidak ingin ganti logo (max 2MB).</small>
                            </div>

                            <!-- Preview logo baru (muncul saat pilih file) -->
                            <div id="new-logo-preview" class="mt-3" style="display:none;">
                                <label class="form-label text-success small fw-bold">Pratinjau Logo Baru:</label>
                                <div class="text-center p-3 border border-success rounded bg-light">
                                    <img id="new-logo-img" src="" alt="Preview"
                                        style="max-height:120px; max-width:100%;">
                                </div>
                            </div>
                        </div>

                        <!-- KANAN: Preview sinkron tinggi dengan form kiri -->
                        <div class="col-lg-5">
                            <div id="preview-container"
                                class="h-100 d-flex flex-column border rounded overflow-hidden bg-light"
                                style="min-height: 500px;">
                                <!-- Logo Preview → 40% tinggi -->
                                <div id="current-logo-preview" class="p-4 text-center border-bottom"
                                    style="flex: 0 0 40%; display:none; background:#ffffff;">
                                    <label class="form-label fw-bold text-primary mb-3 d-block">Logo Saat Ini</label>
                                    <img id="logo-preview-img" src="" alt="Logo Customer"
                                        class="img-fluid rounded"
                                        style="max-height:100%; max-width:100%; object-fit:contain;">
                                </div>

                                <!-- PDF Preview → 60% tinggi -->
                                <div id="current-file-info" class="flex-grow-1 d-flex flex-column"
                                    style="flex: 0 0 60%; display:none;">
                                    <div class="p-3 bg-white border-bottom">
                                        <label class="form-label fw-bold text-primary mb-0">Preview File Saat Ini
                                            (PDF)</label>
                                    </div>
                                    <div id="file-preview-edit-customer-audit"
                                        class="flex-grow-1 overflow-auto bg-white p-3">
                                        <canvas id="pdf-canvas-edit-customer-audit" class="mx-auto d-block"
                                            style="max-width:100%;"></canvas>
                                        <p id="file-loading-message-edit" class="text-center text-muted my-5">Memuat
                                            pratinjau file...</p>
                                    </div>

                                    <!-- Navigasi PDF -->
                                    <div class="p-3 bg-white border-top text-center" id="pdf-controls-edit"
                                        style="display:none;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="prevPageEditCustomerAudit">Prev</button>
                                        <span class="mx-3 text-muted">
                                            Halaman <strong id="pageNumEditCustomerAudit">1</strong> dari <strong
                                                id="pageCountEditCustomerAudit">1</strong>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            id="nextPageEditCustomerAudit">Next</button>
                                    </div>
                                </div>

                                <!-- Jika belum ada file & logo -->
                                <div class="d-flex flex-column flex-grow-1 justify-content-center align-items-center text-muted"
                                    id="no-preview-placeholder">
                                    <i class="ti ti-file-off ti-5x mb-3 opacity-50"></i>
                                    <p>Belum ada bukti file atau logo</p>
                                </div>
                            </div>
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
    <!-- Include PDF.js CDN -->

    <script type="module">
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        window.BASE_URL = "{{ asset('documents/customer-audit') }}"; // Path file
        window.customerAuditEditRoute = "{{ route('customer-audit.edit', ':id') }}";
        window.customerAuditUpdateRoute = "{{ route('customer-audit.update', ':id') }}";

        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        let pdfDocEditCustomerAudit = null;
        let currentPageEditCustomerAudit = 1;

        // Render halaman PDF
        function renderPageEditCustomerAudit(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-customer-audit');
            const ctx = canvas.getContext('2d');

            pdfDocEditCustomerAudit.getPage(pageNum).then(function(page) {
                const containerWidth = document.getElementById('file-preview-edit-customer-audit').offsetWidth;
                const viewport = page.getViewport({
                    scale: 1
                });
                const scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({
                    scale
                });

                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: scaledViewport
                };
                page.render(renderContext).promise.then(function() {
                    currentPageEditCustomerAudit = pageNum;
                    $('#pageNumEditCustomerAudit').text(currentPageEditCustomerAudit);
                    $('#pageCountEditCustomerAudit').text(pdfDocEditCustomerAudit.numPages);

                    $('#prevPageEditCustomerAudit').prop('disabled', currentPageEditCustomerAudit <= 1);
                    $('#nextPageEditCustomerAudit').prop('disabled', currentPageEditCustomerAudit >=
                        pdfDocEditCustomerAudit.numPages);
                });
            });
        }

        // Load PDF
        function loadPDFEditCustomerAudit(fileUrl) {
            pdfDocEditCustomerAudit = null;
            currentPageEditCustomerAudit = 1;
            $('#file-loading-message-edit').text('Memuat pratinjau PDF...').show();
            $('#pdf-canvas-edit-customer-audit').hide();
            $('#pdf-controls-edit').hide();

            pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
                pdfDocEditCustomerAudit = pdf;
                $('#file-loading-message-edit').hide();
                $('#pdf-canvas-edit-customer-audit').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-edit').show();
                }

                // Render the first page immediately after PDF is loaded
                renderPageEditCustomerAudit(
                    currentPageEditCustomerAudit); // Make sure the first page is rendered immediately
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                $('#file-loading-message-edit').html(
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download</a>.</p>`
                ).show();
                $('#pdf-canvas-edit-customer-audit').hide();
                $('#pdf-controls-edit').hide();
            });
        }

        // Navigasi PDF
        $('#prevPageEditCustomerAudit').on('click', function() {
            if (currentPageEditCustomerAudit > 1) renderPageEditCustomerAudit(currentPageEditCustomerAudit - 1);
        });
        $('#nextPageEditCustomerAudit').on('click', function() {
            if (currentPageEditCustomerAudit < pdfDocEditCustomerAudit.numPages) renderPageEditCustomerAudit(
                currentPageEditCustomerAudit + 1);
        });

        function showCurrentLogo(logoPath) {
            if (logoPath) {
                const fullUrl = `${window.BASE_URL}/logo/${logoPath}`;
                $('#logo-preview-img').attr('src', fullUrl);
                $('#current-logo-preview').show();
            } else {
                $('#current-logo-preview').hide();
            }
            $('#new-logo-preview').hide();
        }

        // Open modal edit
        $(document).on('click', '.btn-edit-customer-audit', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const editUrl = window.customerAuditEditRoute.replace(':id', id);

            $.get(editUrl, function(data) {
                console.log(data); // Log the full response to ensure it's correct
                // Populate form fields
                $('#edit_customer_audit_id').val(data.id);
                $('#edit_nama_event').val(data.nama_event);
                $('#edit_tanggal_mulai_event').val(data.tanggal_mulai_event ?? '');
                $('#edit_tanggal_selesai_event').val(data.tanggal_selesai_event ?? '');
                $('#edit_deskripsi_event').val(data.deskripsi_event); // langsung isi value
                console.log("mulai:", data.tanggal_mulai_event);
                console.log("selesai:", data.tanggal_selesai_event);

                showCurrentLogo(data.logo_customer);

                // Check if file_evident exists and is a valid PDF
                if (data.file_evident) {
                    const fileUrl = `${window.BASE_URL}/${data.file_evident}`; // Construct the file URL
                    console.log("File URL:", fileUrl); // Log the file URL to ensure it's correct

                    loadPDFEditCustomerAudit(fileUrl); // Load the PDF file
                    $('#current-file-info').show(); // Show the file preview section
                } else {
                    $('#current-file-info').hide(); // Hide the file preview section if no file exists
                }

                const updateUrl = window.customerAuditUpdateRoute.replace(':id', data.id);
                $('#formEditCustomerAudit').attr('action', updateUrl); // Set form action URL

                $('#modalEditCustomerAudit').modal('show'); // Show the modal
            });
        });


        // Submit form edit
        $('#formEditCustomerAudit').on('submit', function(e) {
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
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        });
                        $('#modalEditCustomerAudit').modal('hide');
                        dataCustomerAuditTable.ajax.reload(); // sesuaikan dengan tabelmu
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

        // Reset modal
        $('#modalEditCustomerAudit').on('hidden.bs.modal', function() {
            const form = $('#formEditCustomerAudit');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-file-info').hide();
        });
    </script>
@endpush
