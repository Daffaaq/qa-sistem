<!-- Modal Create Data Customer Audit -->
<div class="modal fade" id="modalCreateCustomerAuditCalender" tabindex="-1" aria-labelledby="modalCreateCustomerAuditLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('customer-audit.store') }}" method="POST" enctype="multipart/form-data"
                id="formCreateCustomerAudit">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateCustomerAuditLabel">Tambah Data Customer Audit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Nama Event -->
                        <div class="col-md-6">
                            <label for="nama_event" class="form-label">Nama Event <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nama_event" id="nama_event" class="form-control" required>
                            <div class="invalid-feedback" id="error-nama_event"></div>
                        </div>

                        <!-- Tanggal Mulai Event -->
                        <div class="col-md-6">
                            <label for="tanggal_mulai_event" class="form-label">Tanggal Mulai Event <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai_event" id="tanggal_mulai_event"
                                class="form-control" required>
                            <div class="invalid-feedback" id="error-tanggal_mulai_event"></div>
                        </div>

                        <!-- Tanggal Selesai Event -->
                        <div class="col-md-6">
                            <label for="tanggal_selesai_event" class="form-label">Tanggal Selesai Event</label>
                            <input type="date" name="tanggal_selesai_event" id="tanggal_selesai_event"
                                class="form-control">
                            <div class="invalid-feedback" id="error-tanggal_selesai_event"></div>
                        </div>

                        <!-- Deskripsi Event -->
                        <div class="col-md-12">
                            <label for="deskripsi_event" class="form-label">Deskripsi Event <span
                                    class="text-danger">*</span></label>
                            <textarea name="deskripsi_event" id="deskripsi_event" class="form-control" rows="5" required></textarea>
                            <div class="invalid-feedback" id="error-deskripsi_event"></div>
                        </div>

                        <!-- Upload File Evident -->
                        <div class="col-md-6">
                            <label for="file_evident" class="form-label">Upload File Evident (PDF)</label>
                            <input type="file" name="file_evident" id="file_evident" class="form-control"
                                accept=".pdf">
                            <div class="invalid-feedback" id="error-file_evident"></div>
                            <small class="form-text text-muted">Opsional, maksimal 2MB. Hanya format PDF.</small>
                        </div>

                        <!-- logo_customer -->
                        <div class="col-md-6">
                            <label for="logo_customer" class="form-label">Logo Customer</label>
                            <input type="file" name="logo_customer" id="logo_customer" class="form-control"
                                accept=".jpg,.png,.jpeg">
                            <div class="invalid-feedback" id="error-logo_customer"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Form Submit with AJAX
        $('#formCreateCustomerAudit').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);

            // Reset error state
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            Swal.fire({
                title: 'Sedang menyimpan...',
                text: 'Mohon tunggu...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
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
                        form[0].reset();
                        $('#modalCreateCustomerAuditCalender').modal('hide');

                        // Refresh upcoming events & calendar
                        $.ajax({
                            url: "{{ route('customer-audit.refresh') }}",
                            type: "GET",
                            success: function(res) {
                                $('#upcomingEventsContainer').html(res.upcomingHtml);
                                calendar.removeAllEvents();
                                calendar.addEventSource(res.events);
                                calendar.refetchEvents();
                                calendar.updateSize();
                            }
                        });
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
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            const inputField = form.find(`[name="${field}"]`);
                            inputField.addClass('is-invalid');
                            $(`#error-${field}`).text(messages.join(' '));
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

        // Reset form saat modal ditutup
        $('#modalCreateCustomerAuditCalender').on('hidden.bs.modal', function() {
            const form = $('#formCreateCustomerAudit');
            form[0].reset();
            form.find('.form-control, .form-select').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
        });
    </script>
@endpush
