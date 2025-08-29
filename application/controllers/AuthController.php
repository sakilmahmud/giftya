<?php
class AuthController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Load form validation library
        $this->load->library('form_validation');
        $this->load->model('UserModel');
        $this->load->model('CustomerModel'); // Load CustomerModel
    }

    public function index()
    {
        $this->load->view('auth/login');
    }

    public function login()
    {
        $data['error'] = ''; // Initialize the error message

        // If already logged in
        if ($this->session->userdata('user_id')) {
            $role = $this->session->userdata('role');
            switch ($role) {
                case 1:
                case 2:
                case 5:
                    redirect('admin/dashboard');
                case 3:
                    redirect('doer/dashboard');
                case 4:
                    redirect('client/dashboard');
                default:
                    redirect('login');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $user = $this->UserModel->getUserByUsernameOrMobileOrEmail($username);

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $this->session->set_userdata([
                    'user_id'   => $user['id'],
                    'username'  => $user['username'],
                    'full_name' => $user['full_name'],
                    'role'      => $user['user_role']
                ]);

                // Remember me cookie
                if ($this->input->post('remember')) {
                    $cookie_value = $user['id'] . ':' . sha1($user['id'] . $username . $password . 'your_secret_key');
                    $this->input->set_cookie('remember_me', $cookie_value, 60 * 60 * 24 * 30); // 30 days
                }

                // ✅ Redirect to last URL if available
                $last_url = $this->session->userdata('last_url');
                if (!empty($last_url)) {
                    $this->session->unset_userdata('last_url');
                    redirect($last_url);
                }

                // Role-based redirection
                switch ($user['user_role']) {
                    case 1:
                    case 2:
                    case 5:
                        redirect('admin/dashboard');
                    case 3:
                        redirect('doer/dashboard');
                    case 4:
                        redirect('client/dashboard');
                    default:
                        redirect('login');
                }
            } else {
                $data['error'] = 'Invalid username or password';
            }
        }

        $this->load->view('auth/login', $data);
    }

    public function register()
    {
        // Method logic for patient registration
        $data['activePage'] = 'register';

        // Validate the form input
        $this->form_validation->set_rules('full_name', 'Full Name', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|is_unique[users.mobile]');
        $this->form_validation->set_rules('email', 'Email', 'valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            // Form validation failed, reload the view with validation errors
            $this->load->view('auth/register', $data);
        } else {
            // Form validation passed, save the patient's data
            $fullName = $this->input->post('full_name');
            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            // Prepare the data to be inserted into the 'users' table
            $userData = array(
                'username' => $mobile,
                'mobile' => $mobile,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'full_name' => $fullName
            );

            // Check if the mobile number is unique before saving the data
            $isMobileUnique = $this->UserModel->isMobileUniqueonRegister($mobile);
            if (!$isMobileUnique) {
                // Mobile number already exists, show an error message
                $data['error'] = 'Mobile number already registered.';
                $this->load->view('auth/register', $data);
                return;
            }

            // Save the patient's data to the 'users' table using the UserModel
            $patientId = $this->UserModel->createPatient($userData);

            if ($patientId) {
                // Patient registration successful

                // Save the mobile number as the username in the session
                $this->session->set_userdata('user_id', $patientId);
                $this->session->set_userdata('username', $mobile);

                // Redirect to the patient dashboard
                redirect('patient/dashboard');
            } else {
                // Failed to save the patient's data
                // Handle the error accordingly (e.g., show an error message or redirect to an error page)
            }
        }
    }


    public function logout()
    {
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('user_id');
        redirect('login'); // Change 'login' to the desired destination after logout
    }

    // New methods for customer authentication

    public function customer_login()
    {
        $response = ['success' => false, 'message' => ''];

        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
        } else {
            $mobile = $this->input->post('mobile');
            $password = $this->input->post('password');

            $customer = $this->CustomerModel->authenticate_customer($mobile, $password);

            if ($customer) {
                $this->session->set_userdata([
                    'customer_id'   => $customer['id'],
                    'customer_name' => $customer['customer_name'],
                    'customer_phone' => $customer['phone']
                ]);
                $response['success'] = true;
                $response['message'] = 'Login successful!';
            } else {
                $response['message'] = 'Invalid mobile number or password.';
            }
        }
        echo json_encode($response);
    }

    public function customer_register()
    {
        $response = ['success' => false, 'message' => ''];

        $this->form_validation->set_rules('full_name', 'Full Name', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $response['message'] = validation_errors();
        } else {
            $fullName = $this->input->post('full_name');
            $mobile = $this->input->post('mobile');
            $password = $this->input->post('password');

            $existing_customer = $this->CustomerModel->get_by_phone($mobile);

            if ($existing_customer) {
                // Customer exists
                if (empty($existing_customer['password'])) {
                    // Password is not set, so update it
                    $this->CustomerModel->update_customer($existing_customer['id'], ['password' => $password]);
                    $this->session->set_userdata([
                        'customer_id'   => $existing_customer['id'],
                        'customer_name' => $existing_customer['customer_name'],
                        'customer_phone' => $existing_customer['phone']
                    ]);
                    $response['success'] = true;
                    $response['message'] = 'Password updated successfully! You are now logged in.';
                } else {
                    // Password already set
                    $response['message'] = 'A customer with this mobile number already exists.';
                }
            } else {
                // Customer does not exist, create new customer
                $customerData = array(
                    'customer_name' => $fullName,
                    'phone' => $mobile,
                    'password' => $password, // Password will be hashed in CustomerModel
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );

                $customer_id = $this->CustomerModel->insert_customer($customerData);

                if ($customer_id) {
                    $this->session->set_userdata([
                        'customer_id'   => $customer_id,
                        'customer_name' => $fullName,
                        'customer_phone' => $mobile
                    ]);
                    $response['success'] = true;
                    $response['message'] = 'Registration successful! You are now logged in.';
                } else {
                    $response['message'] = 'Failed to register. Please try again.';
                }
            }
        }
        echo json_encode($response);
    }

    public function customer_logout()
    {
        $this->session->unset_userdata('customer_id');
        $this->session->unset_userdata('customer_name');
        $this->session->unset_userdata('customer_phone');
        redirect('checkout'); // Redirect to checkout page after customer logout
    }

    public function check_customer_login_ajax()
    {
        $response = ['logged_in' => false];
        if ($this->session->userdata('customer_id')) {
            $response['logged_in'] = true;
        }
        echo json_encode($response);
    }
}
