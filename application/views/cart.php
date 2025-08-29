<div class="container mt-4 mb-5">
    <h2 class="mb-4">Your Shopping Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center py-4" role="alert">
            <h4 class="alert-heading">Your cart is empty!</h4>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <hr>
            <a href="<?php echo base_url('products'); ?>" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Cart Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="border-0">Product</th>
                                        <th scope="col" class="border-0">Price</th>
                                        <th scope="col" class="border-0">Quantity</th>
                                        <th scope="col" class="border-0">Total</th>
                                        <th scope="col" class="border-0"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr data-item-id="<?php echo $item['id']; ?>">
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <?php
                                                    $display_image = base_url('assets/uploads/no_image.jpeg');
                                                    if (!empty($item['featured_image'])) {
                                                        $display_image = base_url('uploads/products/' . $item['featured_image']);
                                                    }
                                                    ?>
                                                    <img src="<?php echo $display_image; ?>" alt="Product Image" class="rounded me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                        <?php if (!empty($item['custom_message'])): ?>
                                                            <small class="text-muted d-block">Message: <?php echo htmlspecialchars($item['custom_message']); ?></small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['photo_urls'])): 
                                                            $decoded_photos = json_decode($item['photo_urls']);
                                                            if (!empty($decoded_photos) && is_array($decoded_photos)): ?>
                                                                <div class="d-flex flex-wrap mt-1">
                                                                    <?php foreach ($decoded_photos as $photo_name): ?>
                                                                        <img src="<?php echo base_url('uploads/cart_photos/' . $photo_name); ?>" alt="Uploaded Photo" class="img-thumbnail me-1 mb-1" style="width: 40px; height: 40px; object-fit: cover;">
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">₹<span class="item-price"><?php echo number_format($item['price'], 2); ?></span></td>
                                            <td class="py-3">
                                                <input type="number" value="<?php echo $item['quantity']; ?>" min="1" class="form-control quantity-input" data-item-id="<?php echo $item['id']; ?>" style="width: 80px;">
                                            </td>
                                            <td class="py-3">₹<span class="item-total"><?php echo number_format($item['quantity'] * $item['price'], 2); ?></span></td>
                                            <td class="py-3 text-center">
                                                <button class="btn btn-outline-danger btn-sm remove-item" data-item-id="<?php echo $item['id']; ?>" title="Remove Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Subtotal
                                <span>₹<span id="cart-subtotal"><?php echo number_format($cart['total_amount'], 2); ?></span></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Coupon Discount
                                <span class="text-danger">-₹<span id="cart-coupon-discount"><?php echo number_format($cart['coupon_discount'] ?? 0, 2); ?></span></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div>
                                    <strong>Total amount</strong>
                                    <strong>
                                        <p class="mb-0">(Inclusive of all taxes)</p>
                                    </strong>
                                </div>
                                <span><strong>₹<span id="cart-grand-total"><?php echo number_format($cart['total_amount'] - ($cart['coupon_discount'] ?? 0), 2); ?></span></strong></span>
                            </li>
                        </ul>
                        <hr>
                        <div class="form-group mb-3">
                            <label for="coupon_code" class="form-label">Have a coupon?</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Enter coupon code" value="<?php echo htmlspecialchars($cart['coupon_code'] ?? ''); ?>">
                                <button class="btn btn-outline-secondary" type="button" id="apply_coupon_btn">Apply</button>
                            </div>
                            <small id="coupon-message" class="form-text text-muted"></small>
                        </div>
                        <a href="<?php echo base_url('checkout'); ?>" class="btn btn-sm w-100" style="background-color: var(--main-color); color: #fff;">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        // Function to update cart totals on the frontend
        function updateFrontendTotals(newSubtotal, newCouponDiscount, newGrandTotal) {
            $('#cart-subtotal').text(parseFloat(newSubtotal).toFixed(2));
            $('#cart-coupon-discount').text(parseFloat(newCouponDiscount).toFixed(2));
            $('#cart-grand-total').text(parseFloat(newGrandTotal).toFixed(2));
        }

        // Quantity update via AJAX
        $('.quantity-input').on('change', function() {
            var $this = $(this);
            var itemId = $this.data('item-id');
            var newQuantity = parseInt($this.val());

            if (isNaN(newQuantity) || newQuantity <= 0) {
                alert('Please enter a valid quantity.');
                $this.val($this.data('old-quantity')); // Revert to old quantity
                return;
            }

            $this.data('old-quantity', newQuantity); // Store new quantity as old

            $.ajax({
                url: '<?php echo base_url('cart/update_quantity'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    item_id: itemId,
                    quantity: newQuantity
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $this.closest('tr').find('.item-total').text(response.new_item_total);
                        updateFrontendTotals(response.new_cart_total, $('#cart-coupon-discount').text(), response.new_cart_total - parseFloat($('#cart-coupon-discount').text())); // Update all totals
                        $('#cart-item-count').text(response.cart_item_count); // Update header cart count
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred while updating quantity.');
                }
            });
        });

        // Remove item via AJAX
        $('.remove-item').on('click', function() {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            var $this = $(this);
            var itemId = $this.data('item-id');

            $.ajax({
                url: '<?php echo base_url('cart/remove_item'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    item_id: itemId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $this.closest('tr').remove(); // Remove row from table
                        updateFrontendTotals(response.new_cart_total, $('#cart-coupon-discount').text(), response.new_cart_total - parseFloat($('#cart-coupon-discount').text())); // Update all totals
                        $('#cart-item-count').text(response.cart_item_count); // Update header cart count

                        // Check if cart is empty after removal
                        if ($('#cart-items-body tr').length === 0) {
                            $('.container.mt-4.mb-5').html(
                                '<div class="alert alert-info text-center py-4" role="alert">' +
                                '<h4 class="alert-heading">Your cart is empty!</h4>' +
                                '<p>Looks like you haven\'t added anything to your cart yet.</p>' +
                                '<hr>' +
                                '<a href="<?php echo base_url('products'); ?>" class="btn btn-primary">Continue Shopping</a>' +
                                '</div>'
                            );
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred while removing item.');
                }
            });
        });

        // Apply coupon via AJAX
        $('#apply_coupon_btn').on('click', function() {
            var couponCode = $('#coupon_code').val();
            if (couponCode === '') {
                $('#coupon-message').text('Please enter a coupon code.').removeClass('text-success').addClass('text-danger');
                return;
            }

            $.ajax({
                url: '<?php echo base_url('cart/apply_coupon'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    coupon_code: couponCode
                },
                success: function(response) {
                    if (response.status === 'success') {
                        updateFrontendTotals(response.new_cart_total, response.coupon_discount, response.new_cart_total - parseFloat(response.coupon_discount));
                        $('#coupon-message').text(response.message).removeClass('text-danger').addClass('text-success');
                        $('#cart-item-count').text(response.cart_item_count); // Update header cart count
                    } else {
                        $('#coupon-message').text(response.message).removeClass('text-success').addClass('text-danger');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#coupon-message').text('An error occurred while applying coupon.').removeClass('text-success').addClass('text-danger');
                }
            });
        });

        // Store initial quantity for reversion if invalid input
        $('.quantity-input').each(function() {
            $(this).data('old-quantity', parseInt($(this).val()));
        });
    });
</script>