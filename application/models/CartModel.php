<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CartModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_cart_by_session_id($session_id) {
        $this->db->where('session_id', $session_id);
        $this->db->where('status', 'active');
        return $this->db->get('carts')->row_array();
    }

    public function get_cart_by_customer_id($customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('status', 'active');
        return $this->db->get('carts')->row_array();
    }

    public function create_cart($data) {
        $this->db->insert('carts', $data);
        return $this->db->insert_id();
    }

    public function update_cart($cart_id, $data) {
        $this->db->where('id', $cart_id);
        $this->db->update('carts', $data);
        return $this->db->affected_rows();
    }

    public function get_cart_item($cart_id, $product_id) {
        $this->db->where('cart_id', $cart_id);
        $this->db->where('product_id', $product_id);
        return $this->db->get('cart_items')->row_array();
    }

    public function add_cart_item($data) {
        $this->db->insert('cart_items', $data);
        return $this->db->insert_id();
    }

    public function update_cart_item($item_id, $data) {
        $this->db->where('id', $item_id);
        $this->db->update('cart_items', $data);
        return $this->db->affected_rows();
    }

    public function get_cart_items($cart_id) {
        $this->db->select('cart_items.*, products.name as product_name, products.featured_image');
        $this->db->from('cart_items');
        $this->db->join('products', 'products.id = cart_items.product_id');
        $this->db->where('cart_id', $cart_id);
        return $this->db->get()->result_array();
    }

    public function delete_cart_item($item_id) {
        $this->db->where('id', $item_id);
        $this->db->delete('cart_items');
        return $this->db->affected_rows();
    }

    public function get_cart_item_by_id($item_id) {
        $this->db->where('id', $item_id);
        return $this->db->get('cart_items')->row_array();
    }

    public function calculate_cart_total($cart_id) {
        $this->db->select('SUM(quantity * price) as total');
        $this->db->where('cart_id', $cart_id);
        $query = $this->db->get('cart_items');
        return $query->row()->total;
    }

    public function get_cart_item_count($cart_id) {
        $this->db->where('cart_id', $cart_id);
        return $this->db->count_all_results('cart_items');
    }

    public function has_customer_used_coupon($customer_id, $session_id, $coupon_code) {
        if ($customer_id) {
            $this->db->where('customer_id', $customer_id);
        } else {
            $this->db->where('session_id', $session_id);
        }
        $this->db->where('coupon_code', $coupon_code);
        $query = $this->db->get('used_coupons');
        return $query->num_rows() > 0;
    }

    public function mark_coupon_as_used_by_customer($customer_id, $session_id, $coupon_code) {
        $data = array(
            'coupon_code' => $coupon_code,
            'used_at' => date('Y-m-d H:i:s')
        );
        if ($customer_id) {
            $data['customer_id'] = $customer_id;
        } else {
            $data['session_id'] = $session_id;
        }
        $this->db->insert('used_coupons', $data);
        return $this->db->insert_id();
    }

}
