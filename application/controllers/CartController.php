<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CartController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CartModel');
        $this->load->model('ProductModel'); // Assuming you have a ProductModel to get product details
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function add_to_cart_ajax()
    {
        $response = array('status' => 'error', 'message' => 'Invalid request.');

        if ($this->input->is_ajax_request()) {
            $product_id = $this->input->post('product_id');
            $quantity = $this->input->post('quantity');
            $custom_message = $this->input->post('custom_message');
            
            $uploaded_photo_urls = [];

            // Server-side validation for quantity
            if (empty($product_id) || !is_numeric($quantity) || $quantity <= 0) {
                $response['message'] = 'Product ID and quantity are required and must be valid.';
                echo json_encode($response);
                return;
            }

            $product = $this->ProductModel->get_product_by_id($product_id); // Assuming this method exists

            if (!$product) {
                $response['message'] = 'Product not found.';
                echo json_encode($response);
                return;
            }

            // Handle file uploads
            if (!empty($_FILES['product_photos']['name'][0])) {
                $this->load->library('upload');
                $files = $_FILES['product_photos'];
                $num_files = count($files['name']);

                // Server-side validation for minimum 2 photos
                if ($num_files < 2) {
                    $response['message'] = 'Please upload at least two photos.';
                    echo json_encode($response);
                    return;
                }

                $config['upload_path'] = './uploads/cart_photos/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|webp|pdf';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                for ($i = 0; $i < $num_files; $i++) {
                    $_FILES['userfile']['name'] = $files['name'][$i];
                    $_FILES['userfile']['type'] = $files['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['userfile']['error'] = $files['error'][$i];
                    $_FILES['userfile']['size'] = $files['size'][$i];

                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('userfile')) {
                        $upload_data = $this->upload->data();
                        $uploaded_photo_urls[] = $upload_data['file_name'];
                    } else {
                        $response['message'] = 'File upload error: ' . $this->upload->display_errors('', '');
                        echo json_encode($response);
                        return;
                    }
                }
            }

            $price = ($product['sale_price'] > 0) ? $product['sale_price'] : $product['regular_price'];

            $user_id = $this->session->userdata('user_id'); // Get logged-in user ID
            $session_id = session_id(); // Get current session ID for guest users

            $cart = null;
            if ($user_id) {
                $cart = $this->CartModel->get_cart_by_user_id($user_id);
            } else {
                $cart = $this->CartModel->get_cart_by_session_id($session_id);
            }

            if (!$cart) {
                // Create a new cart
                $cart_data = array(
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'status' => 'active'
                );
                if ($user_id) {
                    $cart_data['user_id'] = $user_id;
                } else {
                    $cart_data['session_id'] = $session_id;
                }
                $cart_id = $this->CartModel->create_cart($cart_data);
                $cart = $this->CartModel->get_cart_by_session_id($session_id); // Re-fetch the created cart
            } else {
                // Update existing cart's updated_at timestamp
                $this->CartModel->update_cart($cart['id'], array('updated_at' => date('Y-m-d H:i:s')));
                $cart_id = $cart['id'];
            }

            $cart_item = $this->CartModel->get_cart_item($cart_id, $product_id);

            if ($cart_item) {
                // Update existing cart item
                $new_quantity = $cart_item['quantity'] + $quantity;
                $item_data = array(
                    'quantity' => $new_quantity,
                    'custom_message' => $custom_message, // Update message if needed
                    'photo_urls' => json_encode($uploaded_photo_urls) // Store uploaded photo URLs
                );
                $this->CartModel->update_cart_item($cart_item['id'], $item_data);
            } else {
                // Add new cart item
                $item_data = array(
                    'cart_id' => $cart_id,
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'custom_message' => $custom_message,
                    'photo_urls' => json_encode($uploaded_photo_urls),
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->CartModel->add_cart_item($item_data);
            }

            // Recalculate cart total
            $new_total = $this->CartModel->calculate_cart_total($cart_id);
            $this->CartModel->update_cart($cart_id, array('total_amount' => $new_total));

            $response['status'] = 'success';
            $response['message'] = 'Product added to cart successfully!';
            $response['cart_total'] = $new_total;
            $response['cart_item_count'] = $this->CartModel->get_cart_item_count($cart_id);
        }

        echo json_encode($response);
    }

    public function index()
    {
        $data['title'] = "Cart :: Giftya";
        // This will be the cart page view
        $user_id = $this->session->userdata('user_id');
        $session_id = session_id();

        $cart = null;
        if ($user_id) {
            $cart = $this->CartModel->get_cart_by_user_id($user_id);
        } else {
            $cart = $this->CartModel->get_cart_by_session_id($session_id);
        }

        $data['cart'] = $cart;
        $data['cart_items'] = array();
        if ($cart) {
            $data['cart_items'] = $this->CartModel->get_cart_items($cart['id']);
        }

        // Get cart item count for header
        $data['cart_item_count'] = ($cart) ? $this->CartModel->get_cart_item_count($cart['id']) : 0;

        $this->load->view('inc/header', $data);
        $this->load->view('cart', $data);
        $this->load->view('inc/footer', $data);
    }

    public function update_cart_item_quantity_ajax()
    {
        $response = array('status' => 'error', 'message' => 'Invalid request.');

        if ($this->input->is_ajax_request()) {
            $item_id = $this->input->post('item_id');
            $quantity = $this->input->post('quantity');

            if (empty($item_id) || !is_numeric($quantity) || $quantity <= 0) {
                $response['message'] = 'Invalid item ID or quantity.';
                echo json_encode($response);
                return;
            }

            $cart_item = $this->CartModel->get_cart_item_by_id($item_id); // Need a new method in CartModel

            if (!$cart_item) {
                $response['message'] = 'Cart item not found.';
                echo json_encode($response);
                return;
            }

            $this->CartModel->update_cart_item($item_id, array('quantity' => $quantity));

            // Recalculate cart total
            $new_total = $this->CartModel->calculate_cart_total($cart_item['cart_id']);
            $this->CartModel->update_cart($cart_item['cart_id'], array('total_amount' => $new_total));

            $response['status'] = 'success';
            $response['message'] = 'Cart item quantity updated.';
            $response['new_item_total'] = number_format($quantity * $cart_item['price'], 2);
            $response['new_cart_total'] = number_format($new_total, 2);
            $response['cart_item_count'] = $this->CartModel->get_cart_item_count($cart_item['cart_id']);
        }
        echo json_encode($response);
    }

    public function remove_cart_item_ajax()
    {
        $response = array('status' => 'error', 'message' => 'Invalid request.');

        if ($this->input->is_ajax_request()) {
            $item_id = $this->input->post('item_id');

            if (empty($item_id)) {
                $response['message'] = 'Invalid item ID.';
                echo json_encode($response);
                return;
            }

            $cart_item = $this->CartModel->get_cart_item_by_id($item_id); // Need a new method in CartModel

            if (!$cart_item) {
                $response['message'] = 'Cart item not found.';
                echo json_encode($response);
                return;
            }

            $this->CartModel->delete_cart_item($item_id);

            // Recalculate cart total
            $new_total = $this->CartModel->calculate_cart_total($cart_item['cart_id']);
            $this->CartModel->update_cart($cart_item['cart_id'], array('total_amount' => $new_total));

            $response['status'] = 'success';
            $response['message'] = 'Cart item removed.';
            $response['new_cart_total'] = number_format($new_total, 2);
            $response['cart_item_count'] = $this->CartModel->get_cart_item_count($cart_item['cart_id']);
        }
        echo json_encode($response);
    }

    public function apply_coupon_ajax()
    {
        $response = array('status' => 'error', 'message' => 'Invalid coupon code.');

        if ($this->input->is_ajax_request()) {
            $coupon_code = strtoupper(trim($this->input->post('coupon_code')));
            $valid_coupon = 'LOVEGIFTYA';
            $discount_percentage = 10; // 10% discount

            $user_id = $this->session->userdata('user_id');
            $session_id = session_id();

            $cart = null;
            if ($user_id) {
                $cart = $this->CartModel->get_cart_by_user_id($user_id);
            } else {
                $cart = $this->CartModel->get_cart_by_session_id($session_id);
            }

            if (!$cart) {
                $response['message'] = 'Your cart is empty.';
                echo json_encode($response);
                return;
            }

            if ($coupon_code !== $valid_coupon) {
                $response['message'] = 'Invalid coupon code.';
                echo json_encode($response);
                return;
            }

            // Check if coupon already applied to this cart
            if ($cart['coupon_code'] === $valid_coupon) {
                $response['message'] = 'Coupon already applied to this cart.';
                echo json_encode($response);
                return;
            }

            // Check if user/session has already used this coupon
            if ($this->CartModel->has_user_used_coupon($user_id, $session_id, $valid_coupon)) {
                $response['message'] = 'This coupon has already been used.';
                echo json_encode($response);
                return;
            }

            // Calculate discount
            $subtotal = $cart['total_amount'];
            $coupon_discount = ($subtotal * $discount_percentage) / 100;
            $new_total = $subtotal - $coupon_discount;

            // Update cart
            $update_data = array(
                'coupon_code' => $valid_coupon,
                'coupon_discount' => $coupon_discount,
                'total_amount' => $new_total // Update total amount in cart table
            );
            $this->CartModel->update_cart($cart['id'], $update_data);

            // Mark coupon as used
            $this->CartModel->mark_coupon_as_used($user_id, $session_id, $valid_coupon);

            $response['status'] = 'success';
            $response['message'] = 'Coupon applied successfully!';
            $response['new_cart_total'] = number_format($new_total, 2);
            $response['coupon_discount'] = number_format($coupon_discount, 2);
            $response['cart_item_count'] = $this->CartModel->get_cart_item_count($cart['id']);
        }
        echo json_encode($response);
    }

    public function get_cart_count_ajax()
    {
        $response = array('status' => 'error', 'message' => 'Invalid request.', 'cart_item_count' => 0);

        if ($this->input->is_ajax_request()) {
            $user_id = $this->session->userdata('user_id');
            $session_id = session_id();

            $cart = null;
            if ($user_id) {
                $cart = $this->CartModel->get_cart_by_user_id($user_id);
            } else {
                $cart = $this->CartModel->get_cart_by_session_id($session_id);
            }

            $response['status'] = 'success';
            $response['cart_item_count'] = ($cart) ? $this->CartModel->get_cart_item_count($cart['id']) : 0;
        }
        echo json_encode($response);
    }
}
