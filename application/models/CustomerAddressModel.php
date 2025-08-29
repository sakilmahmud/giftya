<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerAddressModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_addresses_by_customer_id($customer_id) {
        $this->db->where('customer_id', $customer_id);
        return $this->db->get('customer_addresses')->result_array();
    }

    public function get_address_by_id($address_id) {
        $this->db->where('id', $address_id);
        return $this->db->get('customer_addresses')->row_array();
    }

    public function insert_address($data) {
        $this->db->insert('customer_addresses', $data);
        return $this->db->insert_id();
    }

    public function update_address($address_id, $data) {
        $this->db->where('id', $address_id);
        $this->db->update('customer_addresses', $data);
        return $this->db->affected_rows();
    }

    public function delete_address($address_id) {
        $this->db->where('id', $address_id);
        $this->db->delete('customer_addresses');
        return $this->db->affected_rows();
    }

    public function set_default_address($customer_id, $address_id) {
        // Unset current default for the customer
        $this->db->where('customer_id', $customer_id);
        $this->db->update('customer_addresses', ['is_default' => 0]);

        // Set new default
        $this->db->where('id', $address_id);
        $this->db->where('customer_id', $customer_id); // Ensure customer owns the address
        $this->db->update('customer_addresses', ['is_default' => 1]);
        return $this->db->affected_rows();
    }

    public function get_default_address($customer_id) {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('is_default', 1);
        return $this->db->get('customer_addresses')->row_array();
    }

}
