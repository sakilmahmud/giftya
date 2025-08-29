<div class="container mt-4 mb-5">
    <h2 class="mb-4">Checkout</h2>

    <?php if (validation_errors()): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo validation_errors(); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $this->session->flashdata('error_message'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $this->session->flashdata('success_message'); ?>
        </div>
    <?php endif; ?>


    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($this->session->userdata('customer_id'))): ?>
                        <div class="alert alert-info" role="alert">
                            Please <a href="#" data-bs-toggle="modal" data-bs-target="#authModal">Login or Register</a> to manage your addresses and place an order.
                        </div>
                    <?php else: ?>
                        <?php echo form_open('checkout/process'); ?>
                        <?php if (!empty($customer_addresses)): ?>
                            <h5 class="mb-3">Select Shipping Address</h5>
                            <div class="mb-3">
                                <?php foreach ($customer_addresses as $address): ?>
                                    <div class="form-check mb-2 p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="address_id" id="address_<?php echo $address['id']; ?>" value="<?php echo $address['id']; ?>" <?php echo set_radio('address_id', $address['id'], $address['is_default'] == 1); ?> required>
                                        <label class="form-check-label" for="address_<?php echo $address['id']; ?>">
                                            <strong><?php echo htmlspecialchars($address['full_name']); ?></strong><br>
                                            <?php echo htmlspecialchars($address['address_line_1']); ?><?php echo !empty($address['address_line_2']) ? ', ' . htmlspecialchars($address['address_line_2']) : ''; ?><br>
                                            <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> - <?php echo htmlspecialchars($address['pincode']); ?><br>
                                            Phone: <?php echo htmlspecialchars($address['phone']); ?><br>
                                            Email: <?php echo htmlspecialchars($address['email']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="address_id" id="address_new" value="new" <?php echo set_radio('address_id', 'new', empty($customer_addresses)); ?> required>
                                    <label class="form-check-label" for="address_new">
                                        Add New Address
                                    </label>
                                </div>
                            </div>
                            <hr>
                        <?php endif; ?>

                        <div id="new_address_form" style="<?php echo empty($customer_addresses) || set_value('address_id') == 'new' ? 'display: block;' : 'display: none;'; ?>">
                            <h5 class="mb-3">New Shipping Address</h5>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo set_value('full_name'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo set_value('email'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo set_value('phone'); ?>" maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="<?php echo set_value('address_line_1'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address_line_2" class="form-label">Address Line 2 (Optional)</label>
                                <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="<?php echo set_value('address_line_2'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo set_value('city'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" value="<?php echo set_value('state'); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pincode" name="pincode" value="<?php echo set_value('pincode'); ?>" maxlength="6">
                            </div>
                            <hr>
                        </div>

                        <h5 class="mb-3">Payment Method: UPI</h5>
                        <p class="text-muted">You will be redirected to a page with UPI payment instructions after placing the order.</p>
                        <hr>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Place Order</button>
                        <?php echo form_close(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($cart_items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?><br>
                                    <?php if (!empty($item['custom_message'])): ?>
                                        <small class="text-muted">Message: <?php echo htmlspecialchars($item['custom_message']); ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($item['photo_urls'])):
                                        $decoded_photos = json_decode($item['photo_urls']);
                                        if (!empty($decoded_photos) && is_array($decoded_photos)): ?>
                                            <div class="d-flex flex-wrap mt-1">
                                                <?php foreach ($decoded_photos as $photo_name): ?>
                                                    <img src="<?php echo base_url('uploads/cart_photos/' . $photo_name); ?>" alt="Uploaded Photo" class="img-thumbnail me-1 mb-1" style="width: 30px; height: 30px; object-fit: cover;">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <span>₹<?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                            Subtotal
                            <span>₹<?php echo number_format($cart['total_amount'], 2); ?></span>
                        </li>
                        <?php if (!empty($cart['coupon_discount'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Coupon Discount
                                <span class="text-danger">-₹<?php echo number_format($cart['coupon_discount'], 2); ?></span>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                            <div>
                                <strong>Total amount</strong>
                                <strong>
                                    <p class="mb-0">(Inclusive of all taxes)</p>
                                </strong>
                            </div>
                            <span><strong>₹<?php echo number_format($cart['total_amount'] - ($cart['coupon_discount'] ?? 0), 2); ?></strong></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <?php $this->load->view('inc/auth_modal'); ?>

    <script>
        $(document).ready(function() {
            // Check if customer is logged in
            var customerLoggedIn = <?php echo json_encode(!empty($this->session->userdata('customer_id'))); ?>;

            if (!customerLoggedIn) {
                var authModal = new bootstrap.Modal(document.getElementById('authModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                authModal.show();
            }

            $('input[name="address_id"]').on('change', function() {
                if ($(this).val() === 'new') {
                    $('#new_address_form').slideDown();
                    $('#new_address_form input, #new_address_form textarea').prop('required', true);
                } else {
                    $('#new_address_form').slideUp();
                    $('#new_address_form input, #new_address_form textarea').prop('required', false);
                }
            });

            // Initial state based on loaded values
            if ($('input[name="address_id"]:checked').val() === 'new') {
                $('#new_address_form').show();
                $('#new_address_form input, #new_address_form textarea').prop('required', true);
            } else if ($('input[name="address_id"]').length === 0) { // No existing addresses, new form is default
                $('#new_address_form').show();
                $('#new_address_form input, #new_address_form textarea').prop('required', true);
            }
        });
    </script>