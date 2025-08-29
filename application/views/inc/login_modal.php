<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginFormModal" class="mt-3">
                    <div class="mb-3">
                        <label for="loginMobileModal" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="loginMobileModal" name="mobile" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPasswordModal" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPasswordModal" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                    <div id="loginMessageModal" class="mt-2"></div>
                </form>
            </div>
            <div class="modal-footer">
                <p>Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register Now</a></p>
            </div>
        </div>
    </div>
</div>
