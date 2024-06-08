<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once ("Main.php");
class Wam extends Main
{

  function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->library('session');
    $this->load->model('Common_Model');
    $this->load->model('administrator/Admin_Common_Model');
    $this->load->model('administrator/Admin_model');
    $this->load->library('User_auth');

    $session_uid = $this->data['session_uid'] = $this->session->userdata('sess_psts_uid');
    $this->data['session_name'] = $this->session->userdata('sess_psts_name');
    $this->data['session_email'] = $this->session->userdata('sess_psts_email');
    $this->data['sess_fiscal_year_id'] = $this->session->userdata('sess_fiscal_year_id');
    $this->data['sess_company_profile_id'] = $this->session->userdata('sess_company_profile_id');

    $this->load->helper('url');

    $this->data['User_auth_obj'] = new User_auth();
    $this->data['user_data'] = $this->data['User_auth_obj']->check_user_status();

  }

  function unset_only()
  {
    $user_data = $this->session->all_userdata();
    foreach ($user_data as $key => $value) {
      if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
        $this->session->unset_userdata($key);
      }
    }
  }


  function index()
  {

    $this->data['admin_name'] = $this->session->userdata("sess_psts_name");


    parent::get_header();
    parent::get_left_nav();
    $this->load->view('test/test_dashboard', $this->data);
    parent::get_footer();
  }

  function test_dashboard()
  {
    $this->data['admin_name'] = $this->session->userdata("sess_psts_name");


    parent::get_header();
    parent::get_left_nav();
    $this->load->view('test/test_dashboard', $this->data);
    parent::get_footer();
  }





  function logout()
  {

    $this->unset_only();
    $this->session->set_flashdata('alert_message', '<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<i class="icon fas fa-check"></i> You Are Successfully Logout.
		</div>');
    $this->session->unset_userdata('sess_psts_uid');
    REDIRECT(MAINSITE_Admin . 'login');
  }



  function set_company()
  {
    $this->session->set_userdata('sess_company_profile_id', $_POST['sess_company_profile_id']);
    REDIRECT(MAINSITE_Admin . 'wam');
  }

  function access_denied()
  {
    parent::get_header();
    parent::get_left_nav();
    $this->load->view('admin/access_denied', $this->data);
    parent::get_footer();
  }

  public function index1()
  {
    $this->load->view('welcome_message');
  }


}
