<!-- Modal Riwayat Revisi -->
<div class="modal fade" id="modalRiwayatRevisi" tabindex="-1" aria-labelledby="modalRiwayatRevisiLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Revisi Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0 h-100">
                    <!-- Kolom Kiri: Riwayat Revisi -->
                    <div class="col-12 col-md-12 border-end" id="modalRiwayatRevisiBody">
                        <div class="p-3">
                            <p class="text-center mt-4">Memuat data...</p>
                            <ul id="document-list">
                                <!-- Daftar SOP, WI, Form akan dimuat di sini -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview File -->
<div class="modal fade" id="modalPreviewFile" tabindex="-1" aria-labelledby="modalPreviewFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewFileLabel">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="modalPreviewFileBody">
                <p class="text-center">Memuat dokumen...</p>
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

        #modalRiwayatRevisi .modal-body {
            flex: 1 1 auto;
            padding: 0;
            overflow: hidden;
        }

        #modalRiwayatRevisi .modal-body .row {
            height: 100%;
        }

        #modalRiwayatRevisiBody {
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        #document-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        #document-list>li {
            margin-bottom: 10px;
            padding: 5px;
            border-left: 2px solid #d1d1d1;
        }

        .doc-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Set lokasi worker PDF.js supaya tidak error deprecated warning
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        $(document).on('click', '#btnShowAllRevisions', function() {
            const container = $('#modalRiwayatRevisiBody');
            $('#modalRiwayatRevisi').modal('show');
            container.html('<p class="text-center mt-4">Memuat data...</p>');

            $.get("{{ route('she.revisi-show') }}", function(response) {
                if (!response.data || response.data.length === 0) {
                    container.html('<p class="text-center mt-4">Belum ada revisi.</p>');
                    return;
                }

                let html = `
                <input type="text" class="form-control m-3" id="searchInput" placeholder="Cari judul dokumen...">
                <ul id="document-list" class="px-3">`;

                response.data.forEach(function(sop) {
                    html += renderSOP(sop);
                });

                html += '</ul>';
                container.html(html);
            }).fail(function() {
                container.html('<p class="text-danger text-center mt-4">Gagal memuat data.</p>');
            });
        });

        function renderSOP(sop) {
            let html = `<li class="mb-3">
            <div class="doc-item sop">
                <span class="badge bg-danger px-2 py-1">SOP</span>
                <strong class="ms-2">${sop.title || 'Tanpa Judul SOP'}</strong>
            </div>
            ${renderHistories(sop.revisions)}
        `;

            if (sop.wis && sop.wis.length > 0) {
                sop.wis.forEach(wi => {
                    html += renderWI(wi);
                });
            }

            html += '</li>';
            return html;
        }

        function renderWI(wi) {
            let html = `<ul class="ms-4">
            <li class="mb-2">
                <div class="doc-item wi">
                    <span class="badge bg-warning text-dark px-2 py-1">WI</span>
                    <strong class="ms-2">${wi.title || 'Tanpa Judul WI'}</strong>
                </div>
                ${renderHistories(wi.revisions)}
        `;

            if (wi.forms && wi.forms.length > 0) {
                wi.forms.forEach(form => {
                    html += renderForm(form);
                });
            }

            html += '</li></ul>';
            return html;
        }

        function renderForm(form) {
            return `
        <ul class="ms-4">
            <li class="mb-2">
                <div class="doc-item form">
                    <span class="badge bg-success px-2 py-1">Form</span>
                    <strong class="ms-2">${form.title || 'Tanpa Judul Form'}</strong>
                </div>
                ${renderHistories(form.revisions)}
            </li>
        </ul>
        `;
        }

        function renderHistories(histories) {
            if (!histories || histories.length === 0) return '';
            let html = '<ul class="ms-4">';
            histories.forEach(h => {
                const isActive = h.is_active == 1 ?
                    '<span class="badge bg-success">Aktif</span>' :
                    '<span class="badge" style="background-color:#ff3b3b;color:white;">Tidak Aktif</span>';
                html += `
            <li class="my-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <span class="text-muted">Rev. ${h.revision_number}</span>
                        <span class="ms-2">${h.title_document || ''}</span>
                        <small class="text-muted ms-2">${h.date_document} ${h.time_document}</small>
                        ${isActive}
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary btn-preview-file mt-1" 
                            data-file="${h.file_document}" 
                            data-title="${h.title_document}">
                            Preview
                        </button>
                    </div>
                </div>
            </li>`;
            });
            html += '</ul>';
            return html;
        }

        $(document).on('click', '.btn-preview-file', function() {
            const fileUrl = $(this).data('file');
            const title = $(this).data('title') || 'Preview File';
            const preview = $('#modalPreviewFileBody');

            let content = `<div class="w-100 h-100 p-3">`;

            if (fileUrl.endsWith('.pdf')) {
                content +=
                    `<div id="pdf-container" style="display: flex; flex-direction: column; gap: 20px;"></div>`;
            } else if (fileUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
                content +=
                    `<img src="${fileUrl}" style="max-width:100%; max-height:80vh; display:block; margin:auto;" alt="${title}"/>`;
            } else {
                content += `
            <p class="text-center mt-5">Tidak dapat menampilkan preview untuk tipe file ini.</p>
            <p class="text-center">
                <a href="${fileUrl}" target="_blank" rel="noopener noreferrer">Klik di sini untuk download</a>
            </p>
        `;
            }

            content += `</div>`;
            preview.html(content);

            if (fileUrl.endsWith('.pdf')) {
                const loadingTask = pdfjsLib.getDocument(fileUrl);
                loadingTask.promise.then(function(pdf) {
                    const totalPages = pdf.numPages;

                    for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                        pdf.getPage(pageNum).then(function(page) {
                            const scale = 1.0; // Sesuaikan skala jika perlu
                            const viewport = page.getViewport({
                                scale: scale
                            });

                            const canvas = document.createElement('canvas');
                            const context = canvas.getContext('2d', {
                                willReadFrequently: true
                            });
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;

                            const renderContext = {
                                canvasContext: context,
                                viewport: viewport
                            };

                            page.render(renderContext).promise.then(function() {
                                document.getElementById('pdf-container').appendChild(
                                    canvas);
                            });
                        });
                    }
                }).catch(function(error) {
                    console.error("Error loading PDF:", error);
                    preview.html('<p class="text-center text-danger">Gagal memuat PDF.</p>');
                });
            }

            $('#modalPreviewFile').modal('show'); // Menampilkan modal
        });

        // Fitur search
        $(document).on('input', '#searchInput', function() {
            const keyword = $(this).val().toLowerCase();
            $('#document-list li').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.includes(keyword));
            });
        });
    </script>
@endpush
