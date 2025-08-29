<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="authModalLabel">Login or Register</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="authMessage" class="alert" style="display: none;"></div>
                <ul class="nav nav-tabs mb-3" id="authTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Register</button>
                    </li>
                </ul>
                <div class="tab-content" id="authTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <?php echo form_open('auth/customer_login', ['id' => 'loginForm']); ?>
                            <div class="mb-3">
                                <label for="login_mobile" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="login_mobile" name="mobile" required maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="login_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="login_password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        <?php echo form_close(); ?>
                    </div>
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <?php echo form_open('auth/customer_register', ['id' => 'registerForm']); ?>
                            <div class="mb-3">
                                <label for="register_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="register_name" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="register_mobile" class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="register_mobile" name="mobile" required maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="register_email" class="form-label">Email (Optional)</label>
                                <input type="email" class="form-control" id="register_email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="register_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="register_password" name="password" required minlength="6">
                            </div>
                            <div class="mb-3">
                                <label for="register_confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="register_confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function showAuthMessage(message, type) {
            var authMessageDiv = $('#authMessage');
            authMessageDiv.removeClass('alert-success alert-danger').addClass('alert-' + type).text(message).show();
        }

        $('#loginForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '<?php echo base_url('auth/customer_login'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAuthMessage(response.message, 'success');
                        setTimeout(function() {
                            location.reload(); // Reload page on successful login
                        }, 1000);
                    } else {
                        showAuthMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showAuthMessage('An error occurred. Please try again.', 'danger');
                }
            });
        });

        $('#registerForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '<?php echo base_url('auth/customer_register'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAuthMessage(response.message, 'success');
                        setTimeout(function() {
                            location.reload(); // Reload page on successful registration
                        }, 1000);
                    } else {
                        showAuthMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showAuthMessage('An error occurred. Please try again.', 'danger');
                }
            });
        });
    });
</script>