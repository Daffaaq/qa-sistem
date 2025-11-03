<!-- Modal View Customer Audit -->
<div class="modal fade" id="modalViewCustomerAudit" tabindex="-1" aria-labelledby="modalViewCustomerAuditLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalViewCustomerAuditLabel">View Customer Audit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-3">
                    <label for="view_nama_event" class="form-label">Nama Event:</label>
                    <p id="view_nama_event"></p>
                </div>

                <div class="mb-3">
                    <label for="view_tanggal_mulai_event" class="form-label">Tanggal Mulai Event:</label>
                    <p id="view_tanggal_mulai_event"></p>
                </div>

                <div class="mb-3">
                    <label for="view_tanggal_selesai_event" class="form-label">Tanggal Selesai Event:</label>
                    <p id="view_tanggal_selesai_event"></p>
                </div>

                <div class="mb-3">
                    <label for="view_deskripsi_event" class="form-label">Deskripsi Event:</label>
                    <p id="view_deskripsi_event"></p>
                </div>

                <div class="mb-3" id="current-file-info-show" style="display: none;">
                    <label class="form-label">Preview File Saat Ini:</label>
                    <div id="file-preview-view-customer-audit"
                        style="border: 1px solid #ccc; max-height: 500px; overflow: auto; text-align: center;">
                        <canvas id="pdf-canvas-view-customer-audit" style="width: 100%;"></canvas>
                        <p id="file-loading-message-view" class="p-3 text-muted">Loading file preview...</p>
                    </div>

                    <!-- Navigation for PDF -->
                    <div class="d-flex justify-content-center mt-2" id="pdf-controls-view" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2"
                            id="prevPageViewCustomerAudit">
                            <i class="ti ti-chevron-left"></i> Prev
                        </button>
                        <span class="align-self-center">Page <span id="pageNumViewCustomerAudit">1</span> of <span
                                id="pageCountViewCustomerAudit">1</span></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                            id="nextPageViewCustomerAudit">
                            Next <i class="ti ti-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- Include PDF.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

    <script>
        window.BASE_URL = "{{ asset('documents/customer-audit') }}"; // Path to files
        window.customerAuditShowRoute = "{{ route('customer-audit.show', ':id') }}";

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDocViewCustomerAudit = null;
        let currentPageViewCustomerAudit = 1;

        // Render PDF page
        function renderPageViewCustomerAudit(pageNum) {
            const canvas = document.getElementById('pdf-canvas-view-customer-audit');
            const ctx = canvas.getContext('2d');

            pdfDocViewCustomerAudit.getPage(pageNum).then(function(page) {
                const containerWidth = document.getElementById('file-preview-view-customer-audit').offsetWidth;
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
                    currentPageViewCustomerAudit = pageNum;
                    $('#pageNumViewCustomerAudit').text(currentPageViewCustomerAudit);
                    $('#pageCountViewCustomerAudit').text(pdfDocViewCustomerAudit.numPages);

                    $('#prevPageViewCustomerAudit').prop('disabled', currentPageViewCustomerAudit <= 1);
                    $('#nextPageViewCustomerAudit').prop('disabled', currentPageViewCustomerAudit >=
                        pdfDocViewCustomerAudit.numPages);
                });
            });
        }

        // Load PDF
        function loadPDFViewCustomerAudit(fileUrl) {
            pdfDocViewCustomerAudit = null;
            currentPageViewCustomerAudit = 1;
            $('#file-loading-message-view').text('Loading file preview...').show();
            $('#pdf-canvas-view-customer-audit').hide();
            $('#pdf-controls-view').hide();

            pdfjsLib.getDocument(fileUrl).promise.then(function(pdf) {
                pdfDocViewCustomerAudit = pdf;
                $('#file-loading-message-view').hide();
                $('#pdf-canvas-view-customer-audit').show();

                if (pdf.numPages > 1) {
                    $('#pdf-controls-view').show();
                }

                renderPageViewCustomerAudit(currentPageViewCustomerAudit);
            }).catch(function(error) {
                console.error("Error loading PDF:", error);
                $('#file-loading-message-view').html(
                    `<p class="text-danger">Failed to load PDF. <a href="${fileUrl}" target="_blank">Download</a>.</p>`
                ).show();
                $('#pdf-canvas-view-customer-audit').hide();
                $('#pdf-controls-view').hide();
            });
        }

        // PDF navigation controls
        $('#prevPageViewCustomerAudit').on('click', function() {
            if (currentPageViewCustomerAudit > 1) renderPageViewCustomerAudit(currentPageViewCustomerAudit - 1);
        });

        $('#nextPageViewCustomerAudit').on('click', function() {
            if (currentPageViewCustomerAudit < pdfDocViewCustomerAudit.numPages) renderPageViewCustomerAudit(
                currentPageViewCustomerAudit + 1);
        });

        // Open the modal to show customer audit info
        $(document).on('click', '.btn-show-customer-audit', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const showUrl = window.customerAuditShowRoute.replace(':id', id);

            $.get(showUrl, function(data) {
                console.log(data); // Log the full response to ensure it's correct
                // Populate modal with customer audit details
                $('#view_nama_event').text(data.nama_event);
                // Format and assign dates correctly
                const formattedStartDate = data.tanggal_mulai_event ? data.tanggal_mulai_event.split('T')[
                    0] : ''; // Remove time part
                const formattedEndDate = data.tanggal_selesai_event ? data.tanggal_selesai_event.split('T')[
                    0] : ''; // Remove time part
                $('#view_tanggal_mulai_event').text(formattedStartDate);
                $('#view_tanggal_selesai_event').text(formattedEndDate);
                if (!!data.deskripsi_event) {
                    $('#view_deskripsi_event').html(data.deskripsi_event);
                }
                console.log(data.file_evident);
                // Check if file_evident exists
                if (data.file_evident) {
                    const fileUrl = `${window.BASE_URL}/${data.file_evident}`; // Construct the file URL
                    loadPDFViewCustomerAudit(fileUrl); // Load the PDF file
                    $('#current-file-info-show').show(); // Show the file preview section
                } else {
                    $('#current-file-info-show').hide(); // Hide the file preview section if no file exists
                }

                $('#modalViewCustomerAudit').modal('show'); // Show the modal
            });
        });
        // Reset modal
        $('#modalViewCustomerAudit').on('hidden.bs.modal', function() {
            const form = $('#formViewCustomerAudit');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $('#current-file-info-show').hide();
        })
    </script>
@endpush
