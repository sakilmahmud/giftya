<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Register</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registerFormModal" class="mt-3">
                    <div class="mb-3">
                        <label for="registerFullNameModal" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="registerFullNameModal" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerMobileModal" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="registerMobileModal" name="mobile" required>
                    </div>
                    <div class="mb-3">
                        <label for="registerPasswordModal" class="form-label">Password</label>
                        <input type="password" class="form-control" id="registerPasswordModal" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Register</button>
                    <div id="registerMessageModal" class="mt-2"></div>
                </form>
            </div>
        </div>
    </div>
</div>
