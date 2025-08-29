<div class="container mt-5 mb-5 text-center">
    <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Complete Your Payment</h4>
        </div>
        <div class="card-body">
            <p class="lead">Your order #<strong><?php echo htmlspecialchars($invoice_id); ?></strong> has been placed successfully!</p>
            <p>Please complete the payment of <strong>₹<?php echo htmlspecialchars($order_total); ?></strong> using UPI.</p>
            
            <div class="mb-4">
                <p>Click the button below to pay using your preferred UPI app:</p>
                <a href="<?php echo htmlspecialchars($upi_link); ?>" class="btn btn-success btn-lg" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-money-bill-wave me-2"></i> Pay with UPI
                </a>
                <p class="mt-3 text-muted">If you are on a desktop, you can scan the QR code (not displayed here, but your UPI app might generate one).</p>
            </div>

            <hr>

            <h5>After Payment:</h5>
            <p>Once you have completed the payment, please enter the UPI Transaction ID (UTR/Txn ID) below to confirm your order.</p>
            
            <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $this->session->flashdata('error_message'); ?>
                </div>
            <?php endif; ?>

            <?php echo form_open('checkout/confirm_payment'); ?>
                <input type="hidden" name="invoice_id" value="<?php echo htmlspecialchars($invoice_id); ?>">
                <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id); ?>">
                <div class="mb-3">
                    <label for="upi_transaction_id" class="form-label">UPI Transaction ID (UTR/Txn ID)</label>
                    <input type="text" class="form-control" id="upi_transaction_id" name="upi_transaction_id" placeholder="Enter 12-digit UTR/Txn ID" required>
                </div>
                <button type="submit" class="btn btn-primary">Confirm Payment</button>
            <?php echo form_close(); ?>

            <p class="mt-4 text-muted">
                Your order will be processed after successful payment verification.
            </p>
        </div>
    </div>
</div>