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
    <script type="module">
        // === DI LUAR $(document).ready() → WAJIB! ===
        const PDF_MODULE_URL = "{{ route('pdf.module', ['file' => 'pdf']) }}";
        const PDF_WORKER_URL = "{{ route('pdf.worker', ['file' => 'pdf']) }}";

        // Dynamic import di top-level
        import(PDF_MODULE_URL).then(pdfjsLib => {
            pdfjsLib.GlobalWorkerOptions.workerSrc = PDF_WORKER_URL;

            // === SEKARANG BARU GUNAKAN jQuery ===
            $(function() {
                let pdfDoc = null;
                let currentPage = 1;
                let scale = 1.0;
                const scaleStep = 0.25;
                const maxScale = 3.0;
                const minScale = 0.75;

                function renderPage(pageNum) {
                    const canvas = document.getElementById('pdf-canvas');
                    if (!canvas || !pdfDoc) return;

                    const ctx = canvas.getContext('2d');
                    if (!ctx) return;

                    pdfDoc.getPage(pageNum).then(page => {
                        const viewport = page.getViewport({
                            scale: scale
                        });
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;

                        const renderContext = {
                            canvasContext: ctx,
                            viewport: viewport
                        };
                        page.render(renderContext).promise.then(() => {
                            $('#pageNum').text(currentPage);
                            $('#pageCount').text(pdfDoc.numPages);
                            $('#zoomLevel').text(Math.round(scale * 100) + '%');
                            $('#prevPage').prop('disabled', currentPage <= 1);
                            $('#nextPage').prop('disabled', currentPage >= pdfDoc.numPages);
                        });
                    });
                }

                // Buka modal & load PDF
                $(document).on('click', '.btn-preview-pdf', function() {
                    const fileUrl = $(this).data('file');
                    const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));

                    $('#pdf-container').hide();
                    $('#pdfLoadingSpinner')?.show();

                    pdfjsLib.getDocument(fileUrl).promise.then(pdf => {
                        pdfDoc = pdf;
                        currentPage = 1;
                        scale = 1.0;
                        renderPage(currentPage);
                        modal.show();
                    }).catch(err => {
                        console.error('PDF Load Error:', err);
                        alert('Gagal memuat PDF: ' + err.message);
                    });
                });

                // Navigasi & Zoom
                $('#prevPage').on('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPage(currentPage);
                    }
                });
                $('#nextPage').on('click', () => {
                    if (currentPage < pdfDoc?.numPages) {
                        currentPage++;
                        renderPage(currentPage);
                    }
                });
                $('#zoomIn').on('click', () => {
                    scale = Math.min(scale + scaleStep, maxScale);
                    renderPage(currentPage);
                });
                $('#zoomOut').on('click', () => {
                    scale = Math.max(scale - scaleStep, minScale);
                    renderPage(currentPage);
                });

                // Bersihkan saat modal ditutup
                $('#pdfPreviewModal').on('hidden.bs.modal', () => {
                    $('#pdf-container').html(
                        '<canvas id="pdf-canvas" style="border:1px solid #ccc; max-width: 100%;"></canvas>'
                    );
                    pdfDoc = null;
                    currentPage = 1;
                    scale = 1.0;
                    $('#pageNum').text('1');
                    $('#pageCount').text('1');
                    $('#zoomLevel').text('100%');
                });
            });
        }).catch(err => {
            console.error("Failed to load PDF.js module:", err);
            alert("Gagal memuat PDF.js. Pastikan route 'pdf.module' mengembalikan file .mjs dengan benar.");
        });

        // Reset and load PDF after modal opens (example: after a revision)
        $('#modalRevisiSQAMSupplier').on('hidden.bs.modal', function() {
            const fileUrl = $('#revisi-sqam-supplier-file-embed').attr(
                'src'); // Get updated file URL (from embed)
            if (fileUrl) {
                loadPDF(fileUrl); // Load the new PDF file
            }
        });
    </script>
@endpush
