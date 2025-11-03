<!-- Modal Preview PDF -->
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-labelledby="modalPreviewPDFLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewPDFLabel">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <canvas id="pdfCanvas" style="border:1px solid #ccc; max-width: 100%;"></canvas>

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
        window.BASE_URL = "{{ asset('documents') }}";
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

            const modalPDF = document.getElementById('modalPreviewPDF');
            const canvas = document.getElementById('pdfCanvas');
            const ctx = canvas.getContext('2d');

            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const pageNumText = document.getElementById('pageNum');
            const pageCountText = document.getElementById('pageCount');
            const zoomInBtn = document.getElementById('zoomIn');
            const zoomOutBtn = document.getElementById('zoomOut');
            const zoomLevelText = document.getElementById('zoomLevel');

            let pdfDoc = null;
            let currentPage = 1;
            let scale = 1.0;
            const scaleStep = 0.25;
            const minScale = 0.75;
            const maxScale = 3.0;

            function renderPage(num) {
                pdfDoc.getPage(num).then(page => {
                    const modalDialog = modalPDF.querySelector('.modal-dialog');
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
                    canvas.style.width = `${scaledViewport.width}px`;
                    canvas.style.height = `${scaledViewport.height}px`;

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: scaledViewport,
                    };
                    page.render(renderContext);

                    pageNumText.textContent = num;
                    pageCountText.textContent = pdfDoc.numPages;
                    zoomLevelText.textContent = Math.round(scale * 100) + '%';
                    prevPageBtn.disabled = num <= 1;
                    nextPageBtn.disabled = num >= pdfDoc.numPages;
                });
            }

            modalPDF.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const title = button.getAttribute('data-title') || 'Pratinjau Dokumen';
                const file = button.getAttribute('data-file');
                const folder = button.getAttribute('data-folder');
                const type = button.getAttribute('data-type');
                const category = button.getAttribute('data-category');

                let url = '';
                if (type === 'document') {
                    // Pakai folder untuk documents
                    url = `${window.BASE_URL}/${folder}/${file}`;
                } else {
                    if (category === 'QA QC') {
                        url = `${window.BASE_URL}/qa-qc/${file}`;
                    }
                    // Lainnya bisa handle nanti
                }

                modalPDF.querySelector('.modal-title').textContent = title;

                scale = 1.0;
                currentPage = 1;

                pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
                    pdfDoc = pdfDoc_;
                    pageCountText.textContent = pdfDoc.numPages;
                    renderPage(currentPage);
                }).catch(err => {
                    alert('Gagal memuat dokumen PDF: ' + err.message);
                    bootstrap.Modal.getInstance(modalPDF).hide();
                });
            });

            modalPDF.addEventListener('hidden.bs.modal', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                pdfDoc = null;
            });

            prevPageBtn.addEventListener('click', () => {
                if (currentPage <= 1) return;
                currentPage--;
                renderPage(currentPage);
            });

            nextPageBtn.addEventListener('click', () => {
                if (currentPage >= pdfDoc.numPages) return;
                currentPage++;
                renderPage(currentPage);
            });

            zoomInBtn.addEventListener('click', () => {
                if (!pdfDoc) return;
                scale = Math.min(scale + scaleStep, maxScale);
                renderPage(currentPage);
            });

            zoomOutBtn.addEventListener('click', () => {
                if (!pdfDoc) return;
                scale = Math.max(scale - scaleStep, minScale);
                renderPage(currentPage);
            });
        });
    </script>
@endpush
