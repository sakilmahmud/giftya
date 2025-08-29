<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Method to insert a new invoice
    public function insert_invoice($data)
    {
        $this->db->insert('invoices', $data);
        return $this->db->insert_id(); // Return the ID of the newly created invoice
    }

    // Method to insert products associated with an invoice
    public function insert_invoice_details($invoice_id, $products)
    {
        foreach ($products as $product) {
            $insert_data = [
                'invoice_id' => $invoice_id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'custom_message' => $product['custom_message'] ?? NULL,
                'photo_urls' => $product['photo_urls'] ?? NULL,
                // Add other necessary fields for invoice_details table if they exist in $product
                // e.g., 'discount_type' => $product['discount_type'] ?? NULL,
                // 'discount' => $product['discount'] ?? NULL,
                // 'taxable_value' => $product['taxable_value'] ?? NULL,
                // 'cgst' => $product['cgst'] ?? NULL,
                // 'sgst' => $product['sgst'] ?? NULL,
                // 'gst_amount' => $product['gst_amount'] ?? NULL,
                // 'cess_amount' => $product['cess_amount'] ?? NULL,
                // 'final_price' => $product['final_price'] ?? NULL,
                // 'invoice_date' => date('Y-m-d H:i:s'), // Or from invoice_data
                // 'hsn_code' => $product['hsn_code'] ?? NULL,
                // 'hsn_code_id' => $product['hsn_code_id'] ?? NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => 1, // Assuming active
            ];
            $this->db->insert('invoice_details', $insert_data);
        }
    }

    public function get_all_invoices()
    {
        $this->db->select('invoices.*, users.full_name as created_by_name, ca.full_name as customer_full_name, ca.email as customer_email, ca.phone as customer_phone, ca.address_line_1, ca.address_line_2, ca.city, ca.state, ca.pincode');
        $this->db->from('invoices');
        $this->db->join('users', 'invoices.created_by = users.id', 'left');
        $this->db->join('customer_addresses ca', 'invoices.customer_address_id = ca.id', 'left');
        $this->db->order_by('invoices.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getFilteredInvoices($from_date, $to_date, $payment_status, $type, $created_by, $search_value, $start, $length)
    {
        // Select columns including calculated paid_amount and due_amount
        $this->db->select('
        invoices.*,
        users.full_name as created_by_name,
        invoices.total_amount,
        IFNULL(transactions_summary.paid_amount, 0) as paid_amount,
        (invoices.total_amount - IFNULL(transactions_summary.paid_amount, 0)) as due_amount,
        GROUP_CONCAT(products.name SEPARATOR ", ") as product_names,
        ca.full_name as customer_full_name, ca.email as customer_email, ca.phone as customer_phone, ca.address_line_1, ca.address_line_2, ca.city, ca.state, ca.pincode
        ');
        $this->db->from('invoices');
        $this->db->join('users', 'invoices.created_by = users.id', 'left');
        $this->db->join('customer_addresses ca', 'invoices.customer_address_id = ca.id', 'left');

        // Subquery for transactions total amount
        $this->db->join(
            '(SELECT table_id, SUM(amount) as paid_amount FROM transactions 
                      WHERE transaction_for_table = "invoices" AND trans_type = 1 AND status = 1 
                      GROUP BY table_id) as transactions_summary',
            'transactions_summary.table_id = invoices.id',
            'left'
        );

        // Join invoice_details and products for product search
        $this->db->join('invoice_details', 'invoice_details.invoice_id = invoices.id', 'left');
        $this->db->join('products', 'products.id = invoice_details.product_id', 'left');

        $this->db->where('DATE(invoices.invoice_date) >=', $from_date);
        $this->db->where('DATE(invoices.invoice_date) <=', $to_date);

        // Apply payment status filter if provided
        if ($type !== '' && $type !== null) {
            $this->db->where('invoices.is_gst', $type);
        }
        // Apply payment status filter if provided
        if ($payment_status !== '' && $payment_status !== null) {
            $this->db->where('invoices.payment_status', $payment_status);
        }

        // Apply created_by filter if provided
        if (!empty($created_by)) {
            $this->db->where('invoices.created_by', $created_by);
        }

        // Apply search filter
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('invoices.invoice_no', $search_value);
            $this->db->or_like('ca.full_name', $search_value); // Search by customer name from address
            $this->db->or_like('ca.email', $search_value); // Search by customer email from address
            $this->db->or_like('ca.phone', $search_value); // Search by customer phone from address
            $this->db->or_like('users.full_name', $search_value);
            $this->db->or_like('products.name', $search_value); // Search by product name
            $this->db->group_end();
        }

        // Group by invoice ID
        $this->db->group_by('invoices.id');

        // Pagination
        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        // Ordering
        $this->db->order_by('invoices.invoice_date', 'DESC');
        $this->db->order_by('invoices.id', 'DESC');

        $query = $this->db->get();

        // Fetch filtered data count for DataTables
        $this->db->select('COUNT(DISTINCT invoices.id) as count');
        $this->db->from('invoices');
        $this->db->join('users', 'invoices.created_by = users.id', 'left');
        $this->db->join('customer_addresses ca', 'invoices.customer_address_id = ca.id', 'left');

        // Use the same transactions subquery
        $this->db->join(
            '(SELECT table_id, SUM(amount) as paid_amount FROM transactions 
                      WHERE transaction_for_table = "invoices" AND trans_type = 1 AND status = 1 
                      GROUP BY table_id) as transactions_summary',
            'transactions_summary.table_id = invoices.id',
            'left'
        );

        $this->db->join('invoice_details', 'invoice_details.invoice_id = invoices.id', 'left');
        $this->db->join('products', 'products.id = invoice_details.product_id', 'left');

        $this->db->where('DATE(invoices.invoice_date) >=', $from_date);
        $this->db->where('DATE(invoices.invoice_date) <=', $to_date);

        if ($payment_status !== '' && $payment_status !== null) {
            $this->db->where('invoices.payment_status', $payment_status);
        }

        if (!empty($created_by)) {
            $this->db->where('invoices.created_by', $created_by);
        }

        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('invoices.invoice_no', $search_value);
            $this->db->or_like('ca.full_name', $search_value); // Search by customer name from address
            $this->db->or_like('ca.email', $search_value); // Search by customer email from address
            $this->db->or_like('ca.phone', $search_value); // Search by customer phone from address
            $this->db->or_like('users.full_name', $search_value);
            $this->db->or_like('products.name', $search_value); // Search by product name
            $this->db->group_end();
        }

        $count_query = $this->db->get();
        $count_result = $count_query->row_array();

        return [
            'data' => $query->result_array(),
            'recordsTotal' => $count_result['count'],
            'recordsFiltered' => $count_result['count'],
        ];
    }


    // Method to get a specific invoice by ID
    public function get_invoice_by_id($invoice_id)
    {
        $this->db->select('invoices.*, ca.full_name as customer_full_name, ca.email as customer_email, ca.phone as customer_phone, ca.address_line_1, ca.address_line_2, ca.city, ca.state, ca.pincode');
        $this->db->from('invoices');
        $this->db->join('customer_addresses ca', 'invoices.customer_address_id = ca.id', 'left');
        $this->db->where('invoices.id', $invoice_id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function get_invoice_details($invoice_id)
    {
        $this->db->select('invoice_details.*, products.name as product_name, hsn_codes.hsn_code');
        $this->db->from('invoice_details');
        $this->db->join('products', 'invoice_details.product_id = products.id');
        $this->db->join('hsn_codes', 'products.hsn_code_id = hsn_codes.id', 'left');
        $this->db->where('invoice_details.invoice_id', $invoice_id);
        $query = $this->db->get();
        return $query->result_array();
    }


    // Method to get products associated with a specific invoice
    public function get_invoice_details_products($invoice_id)
    {
        $this->db->select('invoice_details.*, products.name as product_name');
        $this->db->from('invoice_details');
        $this->db->join('products', 'invoice_details.product_id = products.id');
        $this->db->where('invoice_details.invoice_id', $invoice_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    // Method to delete an invoice and its products
    public function delete_invoice($invoice_id)
    {
        // Delete products associated with the invoice
        $this->db->where('invoice_id', $invoice_id);
        $this->db->delete('invoice_details');

        // Delete the invoice itself
        $this->db->where('id', $invoice_id);
        $this->db->delete('invoices');
    }

    public function get_last_invoice_no($is_gst_bill, $financial_year)
    {
        if ($is_gst_bill) {
            // GST invoice: format PREFIX/2025-26/0001
            $sql = "SELECT invoice_no FROM invoices WHERE invoice_no LIKE ? ORDER BY CAST(SUBSTRING_INDEX (invoice_no, '/', -1) AS UNSIGNED) DESC LIMIT 1";
            $like = '%/' . $financial_year . '/%';
        } else {
            // Non-GST invoice: format PREFIX/INV/0001
            $sql = "SELECT invoice_no FROM invoices WHERE invoice_no LIKE ? ORDER BY CAST(SUBSTRING_INDEX (invoice_no, '/', -1) AS UNSIGNED) DESC  LIMIT 1";
            $like = '%/INV/%';
        }

        $query = $this->db->query($sql, [$like]);

        if ($query->num_rows() > 0) {
            return $query->row()->invoice_no;
        }

        return null;
    }

    // Get total amount of an invoice by its ID
    public function get_invoice_total($invoice_id)
    {
        $this->db->select('total_amount');
        $this->db->from('invoices');
        $this->db->where('id', $invoice_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->total_amount;
        } else {
            return 0; // Return 0 if invoice not found
        }
    }

    public function get_invoice_transactions($invoice_id)
    {
        $this->db->select('transactions.id, transactions.amount, transactions.trans_type, transactions.payment_method_id, transactions.descriptions, transactions.trans_date, payment_methods.title');
        $this->db->from('transactions');
        $this->db->join('payment_methods', 'transactions.payment_method_id = payment_methods.id', 'left');
        $this->db->where('transactions.transaction_for_table', 'invoices');
        $this->db->where('transactions.table_id', $invoice_id);
        $this->db->where('transactions.status', 1); // Assuming you only want active transactions
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_transaction_by_id($id)
    {
        $this->db->select('
            t.*,
            pm.title as payment_method
        ');
        $this->db->from('transactions t');
        $this->db->join('payment_methods pm', 'pm.id = t.payment_method_id', 'left');
        $this->db->where('t.id', $id);
        $this->db->where('t.trans_type', 1); // Payment only
        $this->db->where('t.status', 1);
        return $this->db->get()->row_array();
    }

    public function update_invoice_status($invoice_id, $data) {
        $this->db->where('id', $invoice_id);
        $this->db->update('invoices', $data);
        return $this->db->affected_rows();
    }
}
