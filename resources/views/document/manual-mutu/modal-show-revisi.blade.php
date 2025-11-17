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
                        <!-- KETERANGAN REVISI -->
                        <div id="revision-keterangan-container" class="d-none border-bottom"
                            style="background: linear-gradient(to bottom, #f8f9fa, #ffffff); min-height: 80px;">
                            <div class="p-9 d-flex align-items-start gap-2 h-100 w-200">
                                <i class="ti ti-info-circle text-primary mt-1 flex-shrink-0"></i>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block fw-semibold mb-1">Keterangan:</small>
                                    <p id="revision-keterangan-text" class="mb-0 text-dark"
                                        style="font-size: 0.95rem; line-height: 1.5;"></p>
                                </div>
                            </div>
                        </div>
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
            display: flex;
            flex-wrap: nowrap;
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

        #revision-keterangan-container {
            flex: 1;
            /* Membuat keterangan mengambil ruang yang tersisa */
            min-height: 80px;
            border-bottom: 1px solid #dee2e6;
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            padding: 10px;
        }

        #modalRiwayatRevisiBody,
        #modalPreviewFileBody {
            height: 100%;
            overflow: auto;
        }

        #pdf-container-riwayat,
        #revision-keterangan-container {
            width: 100%;
            box-sizing: border-box;
            /* Pastikan padding dan border diikutkan dalam perhitungan lebar */
        }
    </style>
@endpush

@push('scripts')
    <script type="module">
        // Set workerSrc for pdf.js (Penting, harus diletakkan di awal)
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

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
                            const keteranganEscaped = (rev.keterangan || '')
                                .replace(/"/g, '&quot;'); // Escape quotes

                            html += `
<tr data-history-id="${rev.id}">
    <td>Revisi ${rev.revision_number}</td>
    <td>${rev.title_document}</td>
    <td>${rev.date_document}</td>
    <td>${rev.time_document}</td>
    <td>
        ${rev.is_active
            ? '<span class="badge bg-success">Aktif</span>'
            : `<button class="btn btn-sm btn-outline-danger btn-set-active" 
                                                                                    data-history-id="${rev.id}" 
                                                                                    data-revision-number="${rev.revision_number}">
                                                                                    Set Aktif
                                                                                </button>`
        }
    </td>
    <td>
        ${rev.file_document
            ? `<button class="btn btn-sm btn-outline-primary btn-preview-file" 
                                                                                    data-file="${fileUrl}" 
                                                                                    data-title="${rev.title_document}"
                                                                                    data-keterangan="${keteranganEscaped}">
                                                                                    Lihat
                                                                                </button>`
            : '-'}
    </td>
</tr>
`;
                        });
                    });

                    container.html(html);
                }).fail(function() {
                    container.html(
                        '<p class="text-danger text-center mt-4">Gagal memuat data.</p>'
                    );
                });
            });

            // --- Logika PDF.js dan Preview File ---
            // Handler Set Aktif
            // === SET AKTIF DENGAN KONFIRMASI + INFO REVISI SAAT INI ===
            $(document).on('click', '.btn-set-active', function() {
                const button = $(this);
                const historyId = button.data('history-id');
                const revisionNum = button.data('revision-number');

                if (button.prop('disabled')) return;

                // CARI REVISI YANG SEDANG AKTIF
                let currentActiveRev = null;
                $('#modalRiwayatRevisiBody .badge.bg-success').each(function() {
                    const revText = $(this).closest('tr').find('td').eq(0).text(); // "Revisi 2"
                    currentActiveRev = revText.replace('Revisi ', '').trim();
                });

                const confirmText = currentActiveRev ?
                    `Aktifkan Revisi ${revisionNum}? Dokumen aktif Revisi ${currentActiveRev} saat ini akan dinonaktifkan.` :
                    `Aktifkan Revisi ${revisionNum}?`;

                // KONFIRMASI
                Swal.fire({
                    title: 'Yakin?',
                    text: confirmText,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-success mr-2',
                        cancelButton: 'btn btn-secondary me-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.prop('disabled', true).html(
                            '<i class="ti ti-loader-2 spinner"></i> Memproses...');

                        $.post("{{ route('manual-mutu.set-active-revision', '') }}/" + historyId, {
                            _token: '{{ csrf_token() }}',
                            history_id: historyId
                        }).done(() => {
                            $('#modalRiwayatRevisiBody .badge.bg-success').each(function() {
                                const row = $(this).closest('tr');
                                const histId = row.data('history-id');
                                const revNum = row.find('td').eq(0).text().replace(
                                    'Revisi ', '');
                                $(this).replaceWith(`
                        <button class="btn btn-sm btn-outline-danger btn-set-active"
                                data-history-id="${histId}" data-revision-number="${revNum}">
                            Set Aktif
                        </button>
                    `);
                            });

                            button.replaceWith(
                                '<span class="badge bg-success">Aktif</span>');
                            manualMutuTable.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `Revisi ${revisionNum} diaktifkan.`,
                            });
                        }).fail((xhr) => {
                            button.prop('disabled', false).html('Set Aktif');
                            const msg = xhr.responseJSON?.message ||
                                'Gagal mengaktifkan revisi.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: msg,
                            });
                        });
                    }
                });
            });
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
                const keterangan = $(this).data('keterangan') || ''; // Ambil keterangan

                const previewOther = $('#file-other-container-riwayat');
                const previewPDF = $('#pdf-container-riwayat');
                const placeholder = $('#preview-placeholder');
                const keteranganContainer = $('#revision-keterangan-container');
                const keteranganText = $('#revision-keterangan-text');

                // Reset semua
                placeholder.addClass('d-none');
                previewOther.empty().addClass('d-none');
                previewPDF.addClass('d-none');
                keteranganContainer.addClass('d-none');

                // Tampilkan keterangan jika ada
                keteranganText.text(keterangan.trim() !== '' ? keterangan : '—');
                keteranganContainer.removeClass('d-none'); // SELALU TAMPIL

                // --- Preview File ---
                if (fileUrl.endsWith('.pdf')) {
                    if (!$('#pdf-canvas-riwayat').length) {
                        previewPDF.prepend(
                            '<canvas id="pdf-canvas-riwayat" style="border:1px solid #ccc; max-width: 100%;"></canvas>'
                        );
                    }
                    loadPDFRiwayat(fileUrl);
                    previewPDF.removeClass('d-none');

                } else if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    let content =
                        `<img src="${fileUrl}" style="max-width:100%; max-height:100%; display:block; margin:auto;" alt="${title}"/>`;
                    previewOther.removeClass('d-none').html(content);

                } else {
                    let content = `
            <div class="p-3 text-center">
                <p class="text-center mt-5">Tidak dapat menampilkan preview untuk tipe file ini.</p>
                <p class="text-center">
                    <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-info">
                        Download File
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
                $('#revision-keterangan-container').addClass('d-none');
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
