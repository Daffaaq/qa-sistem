<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" style="max-width:90vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewModalLabel">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <div id="pdf-container" class="d-flex flex-column align-items-center w-100">
                    <canvas id="pdf-canvas" style="border:1px solid #ccc; max-width: 100%;"></canvas>
                </div>

                <div class="mt-3 d-flex justify-content-center align-items-center gap-3 flex-wrap">
                    <button id="prevPage" class="btn btn-sm btn-secondary">Prev</button>
                    <span>Halaman: <span id="pageNum">1</span> / <span id="pageCount">1</span></span>
                    <button id="nextPage" class="btn btn-sm btn-secondary">Next</button>

                    <button id="zoomOut" class="btn btn-sm btn-secondary ms-4">- Zoom</button>
                    <span id="zoomLevel" style="margin: 0 10px;">100%</span>
                    <button id="zoomIn" class="btn btn-sm btn-secondary">+ Zoom</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

            let pdfDoc = null; // Global variable for the PDF document
            let currentPage = 1; // Initialize current page
            let scale = 1.0; // Initialize scale (zoom)
            const scaleStep = 0.25; // Step size for zooming
            const maxScale = 3.0;
            const minScale = 0.75;

            // Function to load the PDF and initialize canvas
            function loadPDF(fileUrl) {
                const loadingTask = pdfjsLib.getDocument(fileUrl);
                loadingTask.promise.then(function(pdf) {
                    pdfDoc = pdf;
                    renderPage(currentPage); // Render the first page
                }).catch(function(error) {
                    console.error("Error loading PDF:", error);
                    $('#pdfLoadingSpinner').hide();
                });
            }

            // Function to render PDF page
            function renderPage(pageNum) {
                const canvas = document.getElementById('pdf-canvas');
                if (!canvas) {
                    console.error("Canvas element not found.");
                    return;
                }

                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    console.error("Canvas context not found.");
                    return;
                }

                pdfDoc.getPage(pageNum).then(function(page) {
                    const viewport = page.getViewport({
                        scale: scale
                    });
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    page.render(renderContext).promise.then(function() {
                        // Hide spinner and display canvas
                        $('#pdfLoadingSpinner').hide();
                        $('#pdf-container').show();

                        // Update page number UI
                        $('#pageNum').text(currentPage);
                        $('#pageCount').text(pdfDoc.numPages);
                    });
                });
            }

            // Trigger to open PDF preview
            $(document).on('click', '.btn-preview-pdf', function() {
                const fileUrl = $(this).data('file'); // Get file URL
                const modal = new bootstrap.Modal(document.getElementById(
                    'pdfPreviewModal')); // Modal for preview

                // Show spinner & hide canvas
                $('#pdfLoadingSpinner').show();
                $('#pdf-container').hide();

                // Load the PDF and show modal
                loadPDF(fileUrl);
                modal.show();
            });

            // Navigasi halaman PDF
            $('#prevPage').on('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage(currentPage);
                }
            });

            $('#nextPage').on('click', function() {
                if (currentPage < pdfDoc.numPages) {
                    currentPage++;
                    renderPage(currentPage);
                }
            });

            // Zoom in/out
            $('#zoomIn').on('click', function() {
                if (!pdfDoc) return;
                scale = Math.min(scale + scaleStep, maxScale);
                renderPage(currentPage);
            });

            $('#zoomOut').on('click', function() {
                if (!pdfDoc) return;
                scale = Math.max(scale - scaleStep, minScale);
                renderPage(currentPage);
            });

            // Clear canvas when modal is closed
            // Clear canvas when modal is closed
            $('#pdfPreviewModal').on('hidden.bs.modal', function() {
                // Reset canvas by replacing the content of the container
                $('#pdf-container').html(
                    '<canvas id="pdf-canvas" style="border:1px solid #ccc; max-width: 100%;"></canvas>'
                );

                // Reset the page and scale
                currentPage = 1;
                scale = 1.0;
                // Also reset UI elements if needed (though they'll be updated on next load)
                $('#pageNum').text('1');
                $('#pageCount').text('1');
                $('#zoomLevel').text('100%');
            });

        });
    </script>
@endpush
