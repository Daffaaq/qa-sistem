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
                    <div class="mb-3">
                        <div class="alert alert-warning py-2 px-3 mb-0" style="font-size: 0.9rem;">
                            <strong>Catatan:</strong> File yang diunggah melalui form ini akan <strong>menimpa file
                                lama</strong>. Jika Anda ingin menyimpan versi sebelumnya, gunakan fitur
                            <em>Revisi</em>.
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
                        <label for="edit_tanggal_mulai_event" class="form-label">Tanggal Mulai Event <span
                                class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai_event" id="edit_tanggal_mulai_event"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-tanggal_mulai_event"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_tanggal_selesai_event" class="form-label">Tanggal Selesai Event</label>
                        <input type="date" name="tanggal_selesai_event" id="edit_tanggal_selesai_event"
                            class="form-control">
                        <div class="invalid-feedback" id="error-edit-tanggal_selesai_event"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_deskripsi_event" class="form-label">Deskripsi Event</label>
                        <textarea name="deskripsi_event" id="edit_deskripsi_event" class="form-control"></textarea>
                        <div class="invalid-feedback" id="error-edit-deskripsi_event"></div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_file_evident" class="form-label">Upload Bukti File</label>
                        <input type="file" name="file_evident" id="edit_file_evident" class="form-control">
                        <div class="invalid-feedback" id="error-edit-file_evident"></div>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                    </div>

                    <!-- Preview File Saat Ini -->
                    <div class="mb-3" id="current-file-info" style="display: none;">
                        <label class="form-label">Preview File Saat Ini:</label>
                        <div id="file-preview-edit-customer-audit"
                            style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                            <canvas id="pdf-canvas-edit-customer-audit" style="width: 100%;"></canvas>
                            <p id="file-loading-message-edit" class="p-3 text-muted">Memuat pratinjau file...</p>
                        </div>

                        <!-- Navigation for PDF -->
                        <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit" style="display: none;">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                                id="prevPageEditCustomerAudit">
                                <i class="ti ti-chevron-left"></i> Prev
                            </button>
                            <span class="align-self-center">Halaman <span id="pageNumEditCustomerAudit">1</span> dari
                                <span id="pageCountEditCustomerAudit">1</span></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="nextPageEditCustomerAudit">
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
                // Format and assign dates correctly
                const formattedStartDate = data.tanggal_mulai_event ? data.tanggal_mulai_event.split('T')[
                    0] : ''; // Remove time part
                const formattedEndDate = data.tanggal_selesai_event ? data.tanggal_selesai_event.split('T')[
                    0] : ''; // Remove time part

                $('#edit_tanggal_mulai_event').val(formattedStartDate);
                $('#edit_tanggal_selesai_event').val(formattedEndDate);
                $('#edit_deskripsi_event').val(data.deskripsi_event); // langsung isi value

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
