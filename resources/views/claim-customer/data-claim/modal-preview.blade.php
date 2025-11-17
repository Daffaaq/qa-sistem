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
        document.addEventListener('DOMContentLoaded', () => {

            // Ambil modul PDF.JS dari Laravel route
            const pdfModuleUrl = "{{ route('pdf.module', ['file' => 'pdf']) }}";
            const pdfWorkerUrl = "{{ route('pdf.worker', ['file' => 'pdf']) }}";

            import(pdfModuleUrl).then(pdfjsLib => {

                pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

                const pdfModal = document.getElementById('pdfPreviewModal');

                let pdfDoc = null;
                let currentPage = 1;
                let scale = 1.0;
                const scaleStep = 0.25;
                const minScale = 0.75;
                const maxScale = 3.0;

                function renderPage(pageNum) {
                    const canvas = pdfModal.querySelector('#pdf-canvas');
                    const ctx = canvas.getContext('2d');

                    pdfDoc.getPage(pageNum).then(page => {
                        const modalDialog = pdfModal.querySelector('.modal-dialog');
                        const maxWidth = modalDialog.clientWidth || window.innerWidth * 0.9;

                        const viewport = page.getViewport({
                            scale: 1
                        });
                        const fitScale = maxWidth / viewport.width;
                        const adjustedScale = scale * fitScale;

                        const scaledViewport = page.getViewport({
                            scale: adjustedScale
                        });

                        canvas.width = scaledViewport.width;
                        canvas.height = scaledViewport.height;

                        page.render({
                            canvasContext: ctx,
                            viewport: scaledViewport
                        });

                        pdfModal.querySelector('#pageNum').textContent = currentPage;
                        pdfModal.querySelector('#pageCount').textContent = pdfDoc.numPages;
                        pdfModal.querySelector('#zoomLevel').textContent = Math.round(scale * 100) +
                            '%';

                        pdfModal.querySelector('#prevPage').disabled = currentPage <= 1;
                        pdfModal.querySelector('#nextPage').disabled = currentPage >= pdfDoc
                            .numPages;

                        $('#pdfLoadingSpinner').hide();
                        $('#pdf-container').show();
                    });
                }

                // BUTTON PREVIEW
                document.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('btn-preview-pdf')) return;

                    const fileUrl = e.target.dataset.file;
                    const modal = new bootstrap.Modal(pdfModal);

                    $('#pdfLoadingSpinner').show();
                    $('#pdf-container').hide();

                    modal.show();

                    pdfModal.addEventListener('shown.bs.modal', function handler() {
                        pdfModal.removeEventListener('shown.bs.modal', handler);

                        currentPage = 1;
                        scale = 1.0;

                        pdfjsLib.getDocument(fileUrl).promise.then(pdf => {
                            pdfDoc = pdf;
                            renderPage(currentPage);
                        });
                    });
                });

                // Navigation
                pdfModal.querySelector('#prevPage').addEventListener('click', () => {
                    if (currentPage > 1) {
                        currentPage--;
                        renderPage(currentPage);
                    }
                });

                pdfModal.querySelector('#nextPage').addEventListener('click', () => {
                    if (currentPage < pdfDoc.numPages) {
                        currentPage++;
                        renderPage(currentPage);
                    }
                });

                // Zoom
                pdfModal.querySelector('#zoomIn').addEventListener('click', () => {
                    scale = Math.min(scale + scaleStep, maxScale);
                    renderPage(currentPage);
                });

                pdfModal.querySelector('#zoomOut').addEventListener('click', () => {
                    scale = Math.max(scale - scaleStep, minScale);
                    renderPage(currentPage);
                });

                // Reset modal
                pdfModal.addEventListener('hidden.bs.modal', () => {
                    pdfModal.querySelector('#pdf-container').innerHTML =
                        '<canvas id="pdf-canvas" style="border:1px solid #ccc; max-width:100%"></canvas>';

                    pdfDoc = null;
                    currentPage = 1;
                    scale = 1.0;

                    pdfModal.querySelector('#pageNum').textContent = '1';
                    pdfModal.querySelector('#pageCount').textContent = '1';
                    pdfModal.querySelector('#zoomLevel').textContent = '100%';
                });

            }).catch(err => console.error("PDF module failed:", err));
        });
    </script>
@endpush
