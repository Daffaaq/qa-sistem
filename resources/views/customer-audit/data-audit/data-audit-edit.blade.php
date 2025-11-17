<!-- Modal Edit Data Audit Horizontal -->
<div class="modal fade" id="modalEditDataAudit" tabindex="-1" aria-labelledby="modalEditDataAuditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body d-flex">
                <!-- Form kiri -->
                <div class="flex-shrink-0" style="width: 50%; padding-right: 1rem;">
                    <form method="POST" enctype="multipart/form-data" id="formEditDataAudit">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="data_audit_id" id="edit_data_audit_id">

                        <div class="mb-3">
                            <label for="edit_temuan" class="form-label">Temuan <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="temuan" id="edit_temuan" class="form-control">
                            <div class="invalid-feedback" id="error-edit-temuan"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" class="form-control">
                            <div class="invalid-feedback" id="error-edit-due_date"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                            </select>
                            <div class="invalid-feedback" id="error-edit-status"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_pic" class="form-label">PIC</label>
                            <input type="text" name="pic" id="edit_pic" class="form-control">
                            <div class="invalid-feedback" id="error-edit-pic"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control"></textarea>
                            <div class="invalid-feedback" id="error-edit-keterangan"></div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_file_evident" class="form-label">Upload Bukti File</label>
                            <input type="file" name="file_evident" id="edit_file_evident" class="form-control">
                            <div class="invalid-feedback" id="error-edit-file_evident"></div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti file.</small>
                        </div>

                        <div class="modal-footer p-0 mt-3">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

                <!-- Preview PDF kanan -->
                <div class="flex-grow-1" style="border-left: 1px solid #ccc; padding-left: 1rem;">
                    <label class="form-label">Preview File Saat Ini:</label>
                    <div id="file-preview-edit-data-audit"
                        style="max-height: 80vh; overflow: auto; text-align: center;">
                        <canvas id="pdf-canvas-edit-data-audit" style="width: 100%;"></canvas>
                        <p id="file-loading-message-edit" class="p-3 text-muted">Memuat pratinjau file...</p>
                    </div>
                    <div class="d-flex justify-content-center mt-2" id="pdf-controls-edit" style="display: none;">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="prevPageEditDataAudit">
                            <i class="ti ti-chevron-left"></i> Prev
                        </button>
                        <span class="align-self-center">Halaman <span id="pageNumEditDataAudit">1</span> dari <span
                                id="pageCountEditDataAudit">1</span></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="nextPageEditDataAudit">
                            Next <i class="ti ti-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import * as pdfjsLib from '{{ route('pdf.module', ['file' => 'pdf']) }}';
        pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ route('pdf.worker', ['file' => 'pdf']) }}';

        window.dataAuditEditRoute = "{{ route('customer-audit.edit-audit-data', ':id') }}";
        window.dataAuditUpdateRoute = "{{ route('customer-audit.update-audit-data', ':id') }}";
        window.DATA_AUDIT_BASE_URL = "{{ asset('documents/data-audit') }}";

        let pdfDocEditDataAudit = null;
        let currentPageEditDataAudit = 1;

        function renderPageEditDataAudit(pageNum) {
            const canvas = document.getElementById('pdf-canvas-edit-data-audit');
            const ctx = canvas.getContext('2d');
            pdfDocEditDataAudit.getPage(pageNum).then(page => {
                const containerWidth = document.getElementById('file-preview-edit-data-audit').offsetWidth;
                const viewport = page.getViewport({
                    scale: 1
                });
                const scale = containerWidth / viewport.width;
                const scaledViewport = page.getViewport({
                    scale
                });
                canvas.width = scaledViewport.width;
                canvas.height = scaledViewport.height;
                page.render({
                    canvasContext: ctx,
                    viewport: scaledViewport
                }).promise.then(() => {
                    currentPageEditDataAudit = pageNum;
                    document.getElementById('pageNumEditDataAudit').textContent = currentPageEditDataAudit;
                    document.getElementById('pageCountEditDataAudit').textContent = pdfDocEditDataAudit
                        .numPages;
                    document.getElementById('prevPageEditDataAudit').disabled = currentPageEditDataAudit <=
                        1;
                    document.getElementById('nextPageEditDataAudit').disabled = currentPageEditDataAudit >=
                        pdfDocEditDataAudit.numPages;
                });
            });
        }

        function loadPDFEditDataAudit(fileUrl) {
            pdfDocEditDataAudit = null;
            currentPageEditDataAudit = 1;
            document.getElementById('file-loading-message-edit').style.display = 'block';
            document.getElementById('pdf-canvas-edit-data-audit').style.display = 'none';
            document.getElementById('pdf-controls-edit').style.display = 'none';

            pdfjsLib.getDocument(fileUrl).promise.then(pdf => {
                pdfDocEditDataAudit = pdf;
                document.getElementById('file-loading-message-edit').style.display = 'none';
                document.getElementById('pdf-canvas-edit-data-audit').style.display = 'block';
                if (pdf.numPages > 1) document.getElementById('pdf-controls-edit').style.display = 'flex';
                renderPageEditDataAudit(currentPageEditDataAudit);
            }).catch(err => {
                console.error(err);
                document.getElementById('file-loading-message-edit').innerHTML =
                    `<p class="text-danger">Gagal memuat PDF. <a href="${fileUrl}" target="_blank">Download</a></p>`;
            });
        }

        document.getElementById('prevPageEditDataAudit').addEventListener('click', () => {
            if (currentPageEditDataAudit > 1) renderPageEditDataAudit(currentPageEditDataAudit - 1);
        });
        document.getElementById('nextPageEditDataAudit').addEventListener('click', () => {
            if (currentPageEditDataAudit < pdfDocEditDataAudit.numPages) renderPageEditDataAudit(
                currentPageEditDataAudit + 1);
        });

        $(document).on('click', '.btn-edit-data-audit', function() {
            const id = $(this).data('id');
            const editUrl = window.dataAuditEditRoute.replace(':id', id);

            $.get(editUrl, function(data) {
                const hasFile = !!data.data.file_evident;

                $('#edit_data_audit_id').val(data.data.id);
                $('#edit_temuan').val(data.data.temuan);
                $('#edit_due_date').val(data.data.due_date);
                $('#edit_status').val(data.data.status);
                $('#edit_pic').val(data.data.pic);
                $('#edit_keterangan').val(data.data.keterangan);

                const formContainer = $('#formEditDataAudit').parent();
                const previewContainer = $('#file-preview-edit-data-audit').parent();

                if (hasFile) {
                    // Tampilkan preview PDF
                    loadPDFEditDataAudit(`${window.DATA_AUDIT_BASE_URL}/${data.data.file_evident}`);

                    // Set layout 50%-50%
                    formContainer.css('width', '50%').css('padding-right', '1rem');
                    previewContainer.show();
                } else {
                    // Sembunyikan preview PDF
                    previewContainer.hide();

                    // Form full width & center
                    formContainer.css('width', '100%').css('padding-right', '0');
                }

                $('#formEditDataAudit').attr('action', window.dataAuditUpdateRoute.replace(':id', data.data
                    .id));
                $('#modalEditDataAudit').modal('show');
            });

        });

        $('#formEditDataAudit').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

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
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#modalEditDataAudit').modal('hide');
                        dataCustomerAuditTable.ajax.reload();
                    } else {
                        Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        form.find('.form-control').removeClass('is-invalid');
                        form.find('.invalid-feedback').text('');
                        $.each(errors, function(field, messages) {
                            form.find(`[name="${field}"]`).addClass('is-invalid');
                            $(`#error-edit-${field}`).text(messages[0]);
                        });
                    } else {
                        Swal.fire('Gagal!', 'Tidak dapat menghubungi server', 'error');
                    }
                }
            });
        });

        $('#modalEditDataAudit').on('hidden.bs.modal', function() {
            const form = $('#formEditDataAudit');
            form[0].reset();
            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            // Reset PDF preview
            pdfDocEditDataAudit = null;
            currentPageEditDataAudit = 1;
            const canvas = document.getElementById('pdf-canvas-edit-data-audit');
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            canvas.style.display = 'none';
            document.getElementById('file-loading-message-edit').style.display = 'block';
            document.getElementById('file-loading-message-edit').textContent = 'Memuat pratinjau file...';
            document.getElementById('pdf-controls-edit').style.display = 'none';

            // Sembunyikan preview container
            $('#file-preview-edit-data-audit').parent().hide();

            // Kembalikan form ke full width default
            $('#formEditDataAudit').parent().css('width', '100%').css('padding-right', '0');
        });
    </script>
@endpush
