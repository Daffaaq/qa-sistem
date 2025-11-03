<!-- Modal Create User -->
<div class="modal fade" id="modalCreateUser" tabindex="-1" aria-labelledby="modalCreateUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="formCreateUser">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCreateUserLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <div class="invalid-feedback" id="error-email"></div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required>
                        <div class="invalid-feedback" id="error-password"></div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password <span
                                class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" required>
                        <div class="invalid-feedback" id="error-password_confirmation"></div>
                    </div>

                    <!-- Role Field -->
                    <div class="mb-3">
                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="superadmin">Superadmin</option>
                            <option value="user">User</option>
                        </select>
                        <div class="invalid-feedback" id="error-role"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $('#formCreateUser').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait a moment.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });

                        $('#formCreateUser')[0].reset();
                        $('#modalCreateUser').modal('hide');
                        userTable.ajax.reload(); // reload the DataTable with updated data
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

                        // Reset old errors
                        $('#formCreateUser').find('.form-control').removeClass('is-invalid');
                        $('#formCreateUser').find('.invalid-feedback').text('');

                        // Display new errors
                        $.each(errors, function(field, messages) {
                            let input = $(`#formCreateUser [name="${field}"]`);
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

        $('#modalCreateUser').on('hidden.bs.modal', function() {
            const form = $('#formCreateUser');

            form[0].reset(); // Reset all input fields
            form.find('.form-control').removeClass('is-invalid'); // Remove error classes
            form.find('.invalid-feedback').text(''); // Clear error messages
        });
    </script>
@endpush
