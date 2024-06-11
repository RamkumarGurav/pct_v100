<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    //$this->load->database();
    $this->load->library('session');
    $this->load->model('Common_Model');
    $this->load->model('administrator/Login_model');
    $this->load->helper('url');
    $this->data['message'] = '';
    $this->data['alert_message'] = '';

    $this->load->helper('form');
    $this->load->library('form_validation');

    $this->data['session_uid'] = $this->session->userdata('sess_psts_uid');
    $this->data['session_name'] = $this->session->userdata('sess_psts_name');
    $this->data['session_email'] = $this->session->userdata('sess_psts_email');
  }



  /**
   * Controller method for handling ADMIN user login.
   */
  function index()
  {
    // Check if admin user is already logged in, redirect to dashboard if logged in.
    if (!empty($this->data['session_uid']) && !empty($this->data['session_name']) && !empty($this->data['session_email'])) {
      REDIRECT(MAINSITE_Admin . "wam");
    }

    // Check if login form is submitted.
    if (isset($_POST['login_btn'])) {
      // Set validation rules for username and password fields.
      $this->form_validation->set_rules('username', "Username", 'required');
      $this->form_validation->set_rules('password', "Password", 'required');

      // Set error delimiters for validation messages.
      $this->form_validation->set_error_delimiters('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i>', '</div>');

      // Validate form inputs.
      if ($this->form_validation->run() == true) {
        // Get validation errors or flash data message.
        $this->data['alert_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

        // Attempt user sign-in.
        $response = $this->Login_model->doSignInUser();

        if ($response) {
          // If sign-in successful.
          if ($response->status == 1) {
            // Set session data for logged-in user.
            $this->session->set_flashdata('alert_message', '<div class="alert alert-success alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<i class="icon fas fa-check"></i> You Are Login Successfully 
						</div>');
            $this->session->set_userdata('sess_psts_uid', $response->admin_user_id);
            $this->session->set_userdata('sess_psts_name', $response->name);
            $this->session->set_userdata('sess_psts_email', $response->email);
            $this->session->set_userdata('sess_company_profile_id', $response->roles[0]->company_profile_id);

            // Set fiscal year session data.
            $this->load->library('fiscalYear');
            $fy = new fiscalYear();
            $result = $fy->setFiscalYear();
            $this->session->set_userdata('sess_fiscal_year_id', $result->fiscal_year_id);

            // Redirect user to the dashboard.
            REDIRECT(MAINSITE_Admin . "wam");
          } else {
            // If user is blocked by management, display error message.
            $this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<i class="icon fas fa-ban"></i> You are blocked by Management.
					  </div>');
          }
        } else if (!$response) {
          // If user credentials are incorrect, display error message.
          $this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Wrong Email Id Or Password
				  </div>');
        } else {
          // If something went wrong during sign-in process, display error message.
          $this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Something Went Wrong Please Try Again. 
				  </div>');
        }
      } else {
        // If form validation fails, display validation errors.
        $this->data['alert_message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('alert_message');
      }
    }

    // Get flash message for display.
    $temp_alert_message = $this->session->flashdata('alert_message');
    if (!empty($temp_alert_message)) {
      $this->data['alert_message'] = $temp_alert_message;
    }

    // Load login view with data.
    $this->load->view('admin/login', $this->data);
  }




}
