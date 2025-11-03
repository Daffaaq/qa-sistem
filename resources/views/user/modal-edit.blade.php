<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="formEditUser">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditUserLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>

                    <!-- Role Field -->
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="user">User</option>
                        </select>
                        <div class="invalid-feedback" id="error-role"></div>
                    </div>

                    <!-- Password Field (Optional) -->
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password <span class="text-muted">(Leave blank if
                                not changing)</span></label>
                        <input type="password" name="password" id="edit_password" class="form-control">
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="edit_password_confirmation"
                            class="form-control">
                        <div class="invalid-feedback" id="error-password_confirmation"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open modal and populate fields with user data
            $(document).on('click', '.btn-edit-user', function(e) {
                e.preventDefault();
                let userId = $(this).data('id');

                // Reset errors before AJAX request
                $('#formEditUser').find('.form-control').removeClass('is-invalid');
                $('#formEditUser').find('.invalid-feedback').text('');

                // Fetch user data
                $.get("{{ route('users.edit', ':id') }}".replace(':id', userId), function(data) {
                    $('#edit_user_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_role').val(data.role);

                    $('#formEditUser').attr('action', "{{ route('users.update', ':id') }}".replace(
                        ':id', data.id));

                    $('#modalEditUser').modal('show');
                }).fail(function() {
                    Swal.fire('Error!', 'Unable to fetch user data.', 'error');
                });
            });

            // Handle form submission for user update
            $('#formEditUser').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                formData.append('_method', 'PUT'); // Add method to override with PUT

                Swal.fire({
                    title: 'Saving changes...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            });
                            $('#modalEditUser').modal('hide');
                            userTable.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message || 'There was an error.'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Reset previous errors
                            $('#formEditUser').find('.form-control').removeClass('is-invalid');
                            $('#formEditUser').find('.invalid-feedback').text('');

                            // Display new errors
                            $.each(errors, function(field, messages) {
                                let input = $(`#formEditUser [name="${field}"]`);
                                input.addClass('is-invalid');
                                $(`#error-${field}`).text(messages[0]);
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: 'Unable to reach server.'
                            });
                        }
                    }
                });
            });

            // Reset modal state when closed
            $('#modalEditUser').on('hidden.bs.modal', function() {
                const form = $('#formEditUser');
                form[0].reset();
                form.find('.form-control').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
