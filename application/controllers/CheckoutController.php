<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CheckoutController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CartModel');
        $this->load->model('InvoiceModel'); // To save the order
        $this->load->model('CustomerAddressModel'); // New: To handle customer addresses
        $this->load->model('CustomerModel'); // To handle customer data
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $customer_id = $this->session->userdata('customer_id');

        $cart = null;
        // If customer is logged in, try to get their cart
        if ($customer_id) {
            $cart = $this->CartModel->get_cart_by_customer_id($customer_id);
        } else {
            // For guest users, you might have a session-based cart or redirect to cart if empty
            // For now, we'll just ensure cart is not null to avoid errors later if not logged in
            // If cart is empty, it will redirect to cart page below
            $cart = $this->CartModel->get_cart_by_session_id(session_id()); // Fallback for guest cart
        }

        if (!$cart || empty($this->CartModel->get_cart_items($cart['id']))) {
            // Redirect to cart page if cart is empty
            redirect('cart');
        }

        $data['cart'] = $cart;
        $data['cart_items'] = $this->CartModel->get_cart_items($cart['id']);
        $data['title'] = "Checkout";

        // Fetch existing addresses for logged-in customer
        $data['customer_addresses'] = [];
        if ($customer_id) {
            $data['customer_addresses'] = $this->CustomerAddressModel->get_addresses_by_customer_id($customer_id);
        }

        $this->load->view('inc/header', $data);
        $this->load->view('checkout', $data);
        $this->load->view('inc/footer', $data);
    }

    public function process_order()
    {
        $customer_id = $this->session->userdata('customer_id');

        if (!$customer_id) {
            redirect('login'); // Redirect to login if not logged in
            return;
        }

        $cart = $this->CartModel->get_cart_by_customer_id($customer_id); // Assuming get_cart_by_customer_id can work with customer_id

        if (!$cart || empty($this->CartModel->get_cart_items($cart['id']))) {
            redirect('cart');
        }

        $cart_items = $this->CartModel->get_cart_items($cart['id']);
        $total_order_amount = $cart['total_amount'] - ($cart['coupon_discount'] ?? 0);

        $customer_address_id = null;

        // Determine if using existing address or new address
        $use_existing_address = $this->input->post('use_existing_address');

        if ($use_existing_address) {
            $this->form_validation->set_rules('address_id', 'Existing Address', 'required|numeric');
            if ($this->form_validation->run() == FALSE) {
                $this->index();
                return;
            }
            $customer_address_id = $this->input->post('address_id');
            // Verify address belongs to customer
            $address = $this->CustomerAddressModel->get_address_by_id($customer_address_id);
            if (!$address || ($address['customer_id'] != $customer_id)) {
                $this->session->set_flashdata('error_message', 'Invalid address selected.');
                redirect('checkout');
                return;
            }
        } else {
            // New address validation
            $this->form_validation->set_rules('full_name', 'Full Name', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('phone', 'Phone Number', 'required|trim|numeric|min_length[10]|max_length[10]');
            $this->form_validation->set_rules('address_line_1', 'Address Line 1', 'required|trim');
            $this->form_validation->set_rules('city', 'City', 'required|trim');
            $this->form_validation->set_rules('state', 'State', 'required|trim');
            $this->form_validation->set_rules('pincode', 'Pincode', 'required|trim|numeric|exact_length[6]');

            if ($this->form_validation->run() == FALSE) {
                $this->index();
                return;
            }

            $address_data = array(
                'customer_id' => $customer_id, // Link to customer_id
                'full_name' => $this->input->post('full_name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'address_line_1' => $this->input->post('address_line_1'),
                'address_line_2' => $this->input->post('address_line_2'),
                'city' => $this->input->post('city'),
                'state' => $this->input->post('state'),
                'pincode' => $this->input->post('pincode'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $customer_address_id = $this->CustomerAddressModel->insert_address($address_data);
            if (!$customer_address_id) {
                $this->session->set_flashdata('error_message', 'Could not save address. Please try again.');
                redirect('checkout');
                return;
            }
        }

        // Get customer details from the address
        $customer_full_name = '';
        $customer_phone = '';
        $customer_email = '';

        if ($use_existing_address) {
            $address_details = $this->CustomerAddressModel->get_address_by_id($customer_address_id);
            if ($address_details) {
                $customer_full_name = $address_details['full_name'];
                $customer_phone = $address_details['phone'];
                $customer_email = $address_details['email'];
            }
        } else {
            $customer_full_name = $this->input->post('full_name');
            $customer_phone = $this->input->post('phone');
            $customer_email = $this->input->post('email');
        }

        // Check if customer exists by mobile number (this block is largely redundant now if customer_id is from session)
        // However, we keep it to ensure customer_id is correctly set based on the session.
        $customer = $this->CustomerModel->get_customer_by_id($customer_id); // Get customer details from session customer_id

        if ($customer) {
            // Customer exists, use their ID
            $customer_id_for_invoice = $customer['id'];
            // Optionally update customer details if they changed (e.g., name, email)
            $customer_update_data = [
                'customer_name' => $customer_full_name,
                'email' => $customer_email,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->CustomerModel->update_customer($customer_id_for_invoice, $customer_update_data);
        } else {
            // This case should ideally not happen if customer_id is from session and valid
            // Handle as an error or redirect to login
            $this->session->set_flashdata('error_message', 'Customer not found. Please login again.');
            redirect('login');
            return;
        }

        // Generate invoice number
        $invoice_no = $this->generate_invoice_no(0); // 0 for non-GST as per requirement

        // Prepare invoice data
        $invoice_data = array(
            'customer_id' => $customer_id_for_invoice, // Use the customer_id from session
            'customer_name' => $customer_full_name, // Add customer_name
            'mobile' => $customer_phone, // Add mobile
            'invoice_no' => $invoice_no, // Add generated invoice number
            'customer_address_id' => $customer_address_id,
            'total_amount' => $total_order_amount,
            'coupon_code' => $cart['coupon_code'] ?? null,
            'coupon_discount' => $cart['coupon_discount'] ?? 0,
            'order_status' => 'pending_payment',
            'invoice_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_by' => $customer_id_for_invoice, // Use customer_id as created_by
        );

        // Insert invoice
        $invoice_id = $this->InvoiceModel->insert_invoice($invoice_data);

        if ($invoice_id) {
            // Insert invoice details (products)
            $invoice_products_data = [];
            foreach ($cart_items as $item) {
                $invoice_products_data[] = array(
                    'invoice_id' => $invoice_id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'custom_message' => $item['custom_message'],
                    'photo_urls' => $item['photo_urls'],
                );
            }
            $this->InvoiceModel->insert_invoice_details($invoice_id, $invoice_products_data);

            // Create a transaction record for UPI payment
            $transaction_data = array(
                'amount' => $total_order_amount,
                'trans_type' => 1, // 1 for payment
                'payment_method_id' => 2, // Assuming 2 is the ID for UPI in your payment_methods table
                'descriptions' => 'Order Payment for Invoice #' . $invoice_id,
                'transaction_for_table' => 'invoices',
                'table_id' => $invoice_id,
                'trans_by' => $user_id ?? 0,
                'trans_date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'pg_transaction_id' => NULL, // Will be updated on confirm_payment
                'status' => 0 // 0 for pending, 1 for completed
            );
            $this->db->insert('transactions', $transaction_data); // Assuming transactions table is directly accessible or via a model
            $transaction_id = $this->db->insert_id();

            // Mark cart as converted
            $this->CartModel->update_cart($cart['id'], array('status' => 'converted', 'updated_at' => date('Y-m-d H:i:s')));

            // Generate UPI Deep Link
            $upi_id = 'gcshop@ybl'; // Replace with your actual UPI ID
            $payee_name = 'Giftya Store'; // Your store name
            $amount = $total_order_amount;
            $transaction_note = 'Order #' . $invoice_id; // Unique note for transaction
            $transaction_ref_id = 'INV' . $invoice_id . 'T' . $transaction_id; // Unique ID for UPI transaction

            $upi_link = "upi://pay?pa=" . urlencode($upi_id) .
                "&pn=" . urlencode($payee_name) .
                "&am=" . urlencode(number_format($amount, 2, '.', '')) .
                "&cu=INR" .
                "&tn=" . urlencode($transaction_note) .
                "&tr=" . urlencode($transaction_ref_id); // Add transaction reference ID

            // Store UPI link and transaction ID in session to pass to payment instructions page
            $this->session->set_flashdata('upi_link', $upi_link);
            $this->session->set_flashdata('invoice_id', $invoice_id);
            $this->session->set_flashdata('order_total', number_format($amount, 2));
            $this->session->set_flashdata('transaction_id', $transaction_id); // Pass the transaction ID

            redirect('checkout/payment_instructions');
        } else {
            // Error saving order
            $this->session->set_flashdata('error_message', 'There was an error processing your order. Please try again.');
            redirect('checkout');
        }
    }

    public function payment_instructions()
    {
        $upi_link = $this->session->flashdata('upi_link');
        $invoice_id = $this->session->flashdata('invoice_id');
        $order_total = $this->session->flashdata('order_total');
        $transaction_id = $this->session->flashdata('transaction_id');

        if (empty($upi_link) || empty($invoice_id) || empty($transaction_id)) {
            redirect('cart'); // Redirect if no payment data
        }

        $data['upi_link'] = $upi_link;
        $data['invoice_id'] = $invoice_id;
        $data['order_total'] = $order_total;
        $data['transaction_id'] = $transaction_id; // Pass transaction ID to view
        $data['title'] = "Complete Your Payment";

        $this->load->view('inc/header', $data);
        $this->load->view('payment_instructions', $data);
        $this->load->view('inc/footer', $data);
    }

    // This method would be for the user to confirm payment manually
    public function confirm_payment()
    {
        $invoice_id = $this->input->post('invoice_id');
        $transaction_id = $this->input->post('transaction_id'); // Get transaction ID
        $pg_transaction_id = $this->input->post('upi_transaction_id');

        if (empty($invoice_id) || empty($transaction_id) || empty($pg_transaction_id)) {
            $this->session->set_flashdata('error_message', 'Please provide all required details.');
            redirect('checkout/payment_instructions');
        }

        // Update the transaction record
        $transaction_update_data = [
            'pg_transaction_id' => $pg_transaction_id,
            'status' => 1, // Mark as completed
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $transaction_id);
        $this->db->update('transactions', $transaction_update_data); // Assuming direct db access or a TransactionModel

        // Update invoice order status
        $invoice_update_data = [
            'order_status' => 'processing',
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->InvoiceModel->update_invoice_status($invoice_id, $invoice_update_data);

        $this->session->set_flashdata('success_message', 'Your payment details have been submitted. Your order is being processed.');
        redirect('order_success'); // Redirect to a final success page
    }

    public function generate_invoice_no($is_gst)
    {
        $invoice_prefix = getSetting('invoice_prefix');
        $financial_year = getCurrentFinancialYear();

        $last_invoice_no = $this->InvoiceModel->get_last_invoice_no($is_gst, $financial_year);

        if ($last_invoice_no) {
            $last_invoice_no_parts = explode('/', $last_invoice_no);
            $last_number = end($last_invoice_no_parts);
            $next_number = str_pad((int)$last_number + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next_number = '0001';
        }

        if ($is_gst) {
            $invoice_no = "{$invoice_prefix}/{$financial_year}/{$next_number}";
        } else {
            $invoice_no = "{$invoice_prefix}/INV/{$next_number}";
        }

        return $invoice_no;
    }
}
