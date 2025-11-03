<!-- Modal Riwayat + Preview (Manual Mutu) -->
<div class="modal fade" id="modalRiwayatRevisi" tabindex="-1" aria-labelledby="modalRiwayatRevisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Revisi Dokumen Manual Mutu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <!-- Kolom kiri: Riwayat revisi -->
                    <div class="col-12 col-md-6 border-end" id="modalRiwayatRevisiBody">
                        <div class="p-3">
                            <p class="text-center mt-4">Memuat data...</p>
                        </div>
                    </div>

                    <!-- Kolom kanan: Preview file -->
                    <div class="col-12 col-md-6 d-flex flex-column align-items-center justify-content-center h-100"
                        id="modalPreviewFileBody">
                        <p class="text-center text-muted" id="preview-placeholder">Pilih file untuk preview</p>

                        <div id="pdf-container-riwayat"
                            class="d-none w-100 h-100 overflow-auto p-3 d-flex flex-column align-items-center">
                            <canvas id="pdf-canvas-riwayat" style="border:1px solid #ccc; max-width: 100%;"></canvas>

                            <div class="mt-3 mb-3 d-flex justify-content-center align-items-center gap-3 flex-wrap">
                                <button id="prevPageRiwayat" class="btn btn-sm btn-secondary">Prev</button>
                                <span>Halaman: <span id="pageNumRiwayat">1</span> / <span
                                        id="pageCountRiwayat">1</span></span>
                                <button id="nextPageRiwayat" class="btn btn-sm btn-secondary">Next</button>

                                <button id="zoomOutRiwayat" class="btn btn-sm btn-secondary ms-4">- Zoom</button>
                                <span id="zoomLevelRiwayat" style="margin: 0 10px;">100%</span>
                                <button id="zoomInRiwayat" class="btn btn-sm btn-secondary">+ Zoom</button>
                            </div>
                        </div>

                        <div id="file-other-container-riwayat"
                            class="d-none w-100 h-100 overflow-auto d-flex align-items-center justify-content-center p-3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #modalRiwayatRevisi .modal-dialog {
            max-width: 90vw;
        }

        #modalRiwayatRevisi .modal-content {
            height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #modalRiwayatRevisi .modal-header {
            flex: 0 0 auto;
        }

        #modalRiwayatRevisi .modal-body {
            flex: 1 1 auto;
            padding: 0;
            overflow: hidden;
        }

        #modalRiwayatRevisi .modal-body .row {
            height: 100%;
        }

        #modalRiwayatRevisiBody,
        #modalPreviewFileBody {
            height: 100%;
            overflow: auto;
        }

        #modalRiwayatRevisiBody>.p-3,
        #modalPreviewFileBody>.p-3 {
            padding: 1rem;
        }

        #modalRiwayatRevisi table {
            width: 100%;
            table-layout: auto;
        }

        #modalRiwayatRevisi .table thead {
            background-color: #f8f9fa;
        }

        #modalRiwayatRevisi .document-group h5 {
            font-size: 1.05rem;
        }

        #modalPreviewFileBody iframe,
        #modalPreviewFileBody img {
            max-width: 100%;
            max-height: calc(100vh - 120px);
            display: block;
            margin: 0 auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Set workerSrc for pdf.js (Penting, harus diletakkan di awal)
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        let pdfDocRiwayat = null; // Dokumen PDF saat ini
        let currentPageRiwayat = 1; // Halaman saat ini
        let scaleRiwayat = 1.0; // Level zoom
        const scaleStep = 0.25;
        const maxScale = 3.0;
        const minScale = 0.75;
        const defaultScale = 1.0;
        window.BASE_URL = "{{ asset('documents/manual-mutu') }}";

        // Fungsi untuk memuat dan merender PDF
        function loadPDFRiwayat(fileUrl) {
            // Sembunyikan placeholder dan preview lain
            $('#preview-placeholder').addClass('d-none');
            $('#file-other-container-riwayat').empty().addClass('d-none');

            // Tampilkan kontainer PDF
            $('#pdf-container-riwayat').removeClass('d-none');

            // Reset state
            pdfDocRiwayat = null;
            currentPageRiwayat = 1;
            scaleRiwayat = defaultScale;
            $('#zoomLevelRiwayat').text(`${Math.round(defaultScale * 100)}%`);

            const loadingTask = pdfjsLib.getDocument(fileUrl);
            loadingTask.promise.then(function(pdf) {
                pdfDocRiwayat = pdf;
                renderPageRiwayat(currentPageRiwayat);
            }).catch(function(error) {
                console.error("Error loading PDF for Riwayat:", error);
                const errorHtml = `
                    <p class="text-danger text-center mt-5">Gagal memuat PDF. Pastikan file tersedia dan formatnya valid.</p>
                    <p class="text-center">
                        <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-info">
                            <i class="ti ti-download"></i> Klik di sini untuk download
                        </a>
                    </p>`;

                // Ganti konten kontainer PDF dengan pesan error
                $('#pdf-container-riwayat').html(errorHtml).removeClass('d-flex').addClass('d-flex');
            });
        }

        // Fungsi untuk merender halaman spesifik
        function renderPageRiwayat(pageNum) {
            if (!pdfDocRiwayat) return;

            const canvas = document.getElementById('pdf-canvas-riwayat');
            const ctx = canvas.getContext('2d');

            pdfDocRiwayat.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: scaleRiwayat
                });

                // Atur ukuran canvas
                canvas.width = viewport.width;
                canvas.height = viewport.height;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                // Render halaman ke canvas
                page.render(renderContext).promise.then(function() {
                    // Update UI kontrol
                    currentPageRiwayat = pageNum;
                    $('#pageNumRiwayat').text(currentPageRiwayat);
                    $('#pageCountRiwayat').text(pdfDocRiwayat.numPages);
                    $('#zoomLevelRiwayat').text(`${Math.round(scaleRiwayat * 100)}%`);

                    // Atur tombol navigasi
                    $('#prevPageRiwayat').prop('disabled', currentPageRiwayat <= 1);
                    $('#nextPageRiwayat').prop('disabled', currentPageRiwayat >= pdfDocRiwayat.numPages);
                });
            });
        }

        $(document).ready(function() {
            // --- Logika Tombol Lihat Semua Revisi (Tetap) ---
            $(document).on('click', '#btnShowAllRevisions', function() {
                const container = $('#modalRiwayatRevisiBody');

                $('#modalRiwayatRevisi').modal('show');
                container.html('<p class="text-center mt-4">Memuat data...</p>');

                // Reset Preview di kanan
                $('#pdf-container-riwayat').addClass('d-none');
                $('#file-other-container-riwayat').empty().addClass('d-none');
                $('#preview-placeholder').removeClass('d-none');

                window.sqamRevisiShowRoute =
                    "{{ route('manual-mutu.revisi-show') }}"; // Update route here
                $.get(window.sqamRevisiShowRoute, function(response) {
                    if (!response.documents || Object.keys(response.documents).length === 0) {
                        container.html('<p class="text-center mt-4">Belum ada revisi.</p>');
                        return;
                    }

                    let html = '';

                    $.each(response.documents, function(documentId, doc) {
                        html += `
                        <div class="document-group mb-4 px-3">
                            <h5 class="fw-bold border-bottom pb-2 mb-2 text-primary">
                                ${doc.document_title ? doc.document_title : '<i class="text-muted">Dokumen tanpa judul</i>'}
                            </h5>
                            <table class="table table-bordered table-sm table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Revisi Ke</th>
                                        <th>Judul</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>File</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;

                        $.each(doc.revisions, function(_, rev) {
                            const fileUrl =
                                `${window.BASE_URL}/${rev.file_document}`;
                            html += `
                            <tr>
                                <td>Revisi ${rev.revision_number}</td>
                                <td>${rev.title_document}</td>
                                <td>${rev.date_document}</td>
                                <td>${rev.time_document}</td>
                                <td>${rev.is_active
                                    ? '<span class="badge bg-success">Aktif</span>'
                                    : '<span class="badge bg-secondary">Nonaktif</span>'}</td>
                                <td>
                                    ${rev.file_document
                                        ? `<button class="btn btn-sm btn-outline-primary btn-preview-file" 
                                                                            data-file="${fileUrl}" 
                                                                            data-title="${rev.title_document}">
                                                                            <i class="ti ti-file-text"></i> Lihat
                                                                        </button>` 
                                        : '-'}
                                </td>
                            </tr>
                            `;
                        });

                        html += `
                                </tbody>
                            </table>
                        </div>
                        `;
                    });

                    container.html(html);
                }).fail(function() {
                    container.html(
                        '<p class="text-danger text-center mt-4">Gagal memuat data.</p>');
                });
            });

            // --- Logika PDF.js dan Preview File ---

            // Navigasi halaman PDF
            $(document).on('click', '#prevPageRiwayat', function() {
                if (currentPageRiwayat > 1) {
                    renderPageRiwayat(currentPageRiwayat - 1);
                }
            });

            $(document).on('click', '#nextPageRiwayat', function() {
                if (pdfDocRiwayat && currentPageRiwayat < pdfDocRiwayat.numPages) {
                    renderPageRiwayat(currentPageRiwayat + 1);
                }
            });

            // Zoom in/out
            $(document).on('click', '#zoomInRiwayat', function() {
                if (!pdfDocRiwayat) return;
                scaleRiwayat = Math.min(scaleRiwayat + scaleStep, maxScale);
                renderPageRiwayat(currentPageRiwayat);
            });

            $(document).on('click', '#zoomOutRiwayat', function() {
                if (!pdfDocRiwayat) return;
                scaleRiwayat = Math.max(scaleRiwayat - scaleStep, minScale);
                renderPageRiwayat(currentPageRiwayat);
            });

            // Handler untuk klik tombol 'Lihat'
            $(document).on('click', '.btn-preview-file', function() {
                const fileUrl = $(this).data('file');
                const title = $(this).data('title') || 'Preview File';
                const previewOther = $('#file-other-container-riwayat');
                const previewPDF = $('#pdf-container-riwayat');
                const placeholder = $('#preview-placeholder');

                placeholder.addClass('d-none');

                if (fileUrl.endsWith('.pdf')) {
                    // 1. Handle PDF menggunakan pdf.js
                    previewOther.empty().addClass('d-none');
                    // Tambahkan kembali canvas jika hilang karena error
                    if (!$('#pdf-canvas-riwayat').length) {
                        previewPDF.prepend(
                            '<canvas id="pdf-canvas-riwayat" style="border:1px solid #ccc; max-width: 100%;"></canvas>'
                        );
                    }
                    loadPDFRiwayat(fileUrl);

                } else if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    // 2. Handle Gambar
                    previewPDF.addClass('d-none');
                    let content =
                        `<img src="${fileUrl}" style="max-width:100%; max-height:100%; display:block; margin:auto;" alt="${title}"/>`;

                    previewOther.removeClass('d-none').html(content);
                } else {
                    // 3. Handle file lain (link download)
                    previewPDF.addClass('d-none');
                    let content = `
                        <div class="p-3 text-center">
                            <p class="text-center mt-5">Tidak dapat menampilkan preview untuk tipe file ini.</p>
                            <p class="text-center">
                                <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-info">
                                    <i class="ti ti-download"></i> Download File
                                </a>
                            </p>
                        </div>`;
                    previewOther.removeClass('d-none').html(content);
                }
            });

            // Reset preview saat modal ditutup
            $('#modalRiwayatRevisi').on('hidden.bs.modal', function() {
                pdfDocRiwayat = null;
                currentPageRiwayat = 1;
                scaleRiwayat = defaultScale;

                // Reset preview area
                $('#pdf-container-riwayat').addClass('d-none');
                $('#file-other-container-riwayat').empty().addClass('d-none');
                $('#preview-placeholder').removeClass('d-none');

                // Hapus dan tambahkan kembali canvas untuk memastikan state bersih
                $('#pdf-canvas-riwayat').remove();
                $('#pdf-container-riwayat').prepend(
                    '<canvas id="pdf-canvas-riwayat" style="border:1px solid #ccc; max-width: 100%;"></canvas>'
                );
            });
        });
    </script>
@endpush
