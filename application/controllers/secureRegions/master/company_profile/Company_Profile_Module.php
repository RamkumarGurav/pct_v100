<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once (APPPATH . "controllers/secureRegions/Main.php");
class Company_Profile_Module extends Main
{

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('Common_Model');
		$this->load->model('administrator/Admin_Common_Model');
		$this->load->model('administrator/Admin_model');
		$this->load->model('administrator/company_profile/Company_Profile_Model');
		$this->load->library('pagination');

		$this->load->library('User_auth');

		$session_uid = $this->data['session_uid'] = $this->session->userdata('sess_psts_uid');
		$this->data['session_name'] = $this->session->userdata('sess_psts_name');
		$this->data['session_email'] = $this->session->userdata('sess_psts_email');

		$this->load->helper('url');

		$this->data['User_auth_obj'] = new User_auth();
		$this->data['user_data'] = $this->data['User_auth_obj']->check_user_status();

		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");

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
		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/company_profile/Employee_Module/list', $this->data);
		parent::get_footer();
	}

	function company_profile_list()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if (empty($this->data['user_access'])) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		$search = array();
		$field_name = '';
		$field_value = '';
		$end_date = '';
		$start_date = '';
		$record_status = "";
		$country_id = "";
		$state_id = "";
		$city_id = "";
		$user_role_id = "";
		$designation_id = "";

		if (!empty($_REQUEST['field_name']))
			$field_name = $_POST['field_name'];
		else if (!empty($field_name))
			$field_name = $field_name;

		if (!empty($_REQUEST['field_value']))
			$field_value = $_POST['field_value'];
		else if (!empty($field_value))
			$field_value = $field_value;

		if (!empty($_POST['end_date']))
			$end_date = $_POST['end_date'];

		if (!empty($_POST['start_date']))
			$start_date = $_POST['start_date'];

		if (!empty($_POST['record_status']))
			$record_status = $_POST['record_status'];

		if (!empty($_POST['country_id']))
			$country_id = $_POST['country_id'];

		if (!empty($_POST['state_id']))
			$state_id = $_POST['state_id'];

		if (!empty($_POST['city_id']))
			$city_id = $_POST['city_id'];

		if (!empty($_POST['user_role_id']))
			$user_role_id = $_POST['user_role_id'];

		if (!empty($_POST['designation_id']))
			$designation_id = $_POST['designation_id'];


		$this->data['field_name'] = $field_name;
		$this->data['field_value'] = $field_value;
		$this->data['end_date'] = $end_date;
		$this->data['start_date'] = $start_date;
		$this->data['record_status'] = $record_status;
		$this->data['country_id'] = $country_id;
		$this->data['state_id'] = $state_id;
		$this->data['city_id'] = $city_id;
		$this->data['user_role_id'] = $user_role_id;
		$this->data['designation_id'] = $designation_id;

		$search['end_date'] = $end_date;
		$search['start_date'] = $start_date;
		$search['field_value'] = $field_value;
		$search['field_name'] = $field_name;
		$search['record_status'] = $record_status;
		$search['country_id'] = $country_id;
		$search['state_id'] = $state_id;
		$search['city_id'] = $city_id;
		$search['user_role_id'] = $user_role_id;
		$search['designation_id'] = $designation_id;
		$search['search_for'] = "count";

		$data_count = $this->Company_Profile_Model->get_company_profile($search);
		$r_count = $this->data['row_count'] = $data_count[0]->counts;

		unset($search['search_for']);

		$offset = (int) $this->uri->segment(5); //echo $offset;
		if ($offset == "") {
			$offset = '0';
		}
		$per_page = _all_pagination_;

		$this->load->library('pagination');
		//$config['base_url'] =MAINSITE.'secure_region/reports/DispatchedOrders/'.$module_id.'/';
		$this->load->library('pagination');
		$config['base_url'] = MAINSITE_Admin . $this->data['user_access']->class_name . '/' . $this->data['user_access']->function_name . '/';
		$config['total_rows'] = $r_count;
		$config['uri_segment'] = '5';
		$config['per_page'] = $per_page;
		$config['num_links'] = 4;
		$config['first_link'] = '&lsaquo; First';
		$config['last_link'] = 'Last &rsaquo;';
		$config['prev_link'] = 'Prev';
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';
		$config['attributes'] = array('class' => 'paginationClass');


		$this->pagination->initialize($config);

		$this->data['page_is_master'] = $this->data['user_access']->is_master;
		$this->data['page_parent_module_id'] = $this->data['user_access']->parent_module_id;

		$search['limit'] = $per_page;
		$search['offset'] = $offset;
		$this->data['country_data'] = $this->Common_Model->getData(array('select' => '*', 'from' => 'country', 'where' => "country_id > 0", "order_by" => "country_name ASC"));
		$this->data['users_role_data'] = $this->Common_Model->getData(array('select' => '*', 'from' => 'users_role_master', 'where' => "user_role_id > 0", "order_by" => "user_role_name ASC"));

		$this->data['designation_data'] = $this->Common_Model->getData(array('select' => '*', 'from' => 'designation_master', 'where' => "designation_id > 0", "order_by" => "designation_name ASC"));
		$this->data['company_profile_data'] = $this->Company_Profile_Model->get_company_profile($search);

		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/company_profile/Company_Profile_Module/company_profile_list', $this->data);
		parent::get_footer();
	}

	function company_profile_list_export()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if (empty($this->data['user_access'])) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}

		if ($this->data['user_access']->export_data != 1) {
			$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Export " . $user_access->module_name);
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		$search = array();
		$field_name = '';
		$field_value = '';
		$end_date = '';
		$start_date = '';
		$record_status = "";
		$country_id = "";
		$state_id = "";

		if (!empty($_REQUEST['field_name']))
			$field_name = $_POST['field_name'];
		else if (!empty($field_name))
			$field_name = $field_name;

		if (!empty($_REQUEST['field_value']))
			$field_value = $_POST['field_value'];
		else if (!empty($field_value))
			$field_value = $field_value;

		if (!empty($_POST['end_date']))
			$end_date = $_POST['end_date'];

		if (!empty($_POST['start_date']))
			$start_date = $_POST['start_date'];

		if (!empty($_POST['record_status']))
			$record_status = $_POST['record_status'];

		if (!empty($_POST['country_id']))
			$country_id = $_POST['country_id'];

		if (!empty($_POST['state_id']))
			$state_id = $_POST['state_id'];


		$this->data['field_name'] = $field_name;
		$this->data['field_value'] = $field_value;
		$this->data['end_date'] = $end_date;
		$this->data['start_date'] = $start_date;
		$this->data['record_status'] = $record_status;
		$this->data['country_id'] = $country_id;
		$this->data['state_id'] = $state_id;

		$search['end_date'] = $end_date;
		$search['start_date'] = $start_date;
		$search['field_value'] = $field_value;
		$search['field_name'] = $field_name;
		$search['record_status'] = $record_status;
		$search['country_id'] = $country_id;
		$search['state_id'] = $state_id;

		$this->data['company_profile_data'] = $this->Company_Profile_Model->get_company_profile($search);


		$this->load->view('admin/company_profile/Company_Profile_Module/company_profile_list_export', $this->data);
	}

	function company_profile_view($company_profile_id = "")
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if (empty($company_profile_id)) {
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/" . $user_access->function_name);
			exit;
		}
		if (empty($this->data['user_access'])) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		$this->data['page_is_master'] = $this->data['user_access']->is_master;
		$this->data['page_parent_module_id'] = $this->data['user_access']->parent_module_id;
		$this->data['company_profile_data'] = $this->Company_Profile_Model->get_company_profile(array("company_profile_id" => $company_profile_id));
		if (empty($company_profile_id)) {
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/" . $user_access->function_name);
			exit;
		}

		$this->data['company_profile_data'] = $this->data['company_profile_data'][0];

		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/company_profile/Company_Profile_Module/company_profile_view', $this->data);
		parent::get_footer();
	}

	function company_profile_edit($company_profile_id = "")
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if (empty($this->data['user_access'])) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		if (empty($company_profile_id)) {
			if ($user_access->add_module != 1) {
				$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Add " . $user_access->module_name);
				REDIRECT(MAINSITE_Admin . "wam/access-denied");
			}
		}
		if (!empty($company_profile_id)) {
			if ($user_access->update_module != 1) {
				$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Update " . $user_access->module_name);
				REDIRECT(MAINSITE_Admin . "wam/access-denied");
			}
		}

		$this->data['country_data'] = $this->Common_Model->getData(array('select' => '*', 'from' => 'country', 'where' => "country_id > 0", "order_by" => "country_name ASC"));

		$this->data['page_is_master'] = $this->data['user_access']->is_master;
		$this->data['page_parent_module_id'] = $this->data['user_access']->parent_module_id;
		if (!empty($company_profile_id)) {
			$this->data['company_profile_data'] = $this->Company_Profile_Model->get_company_profile(array("company_profile_id" => $company_profile_id));
			if (empty($this->data['company_profile_data'])) {
				$this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Record Not Found. 
				  </div>');
				REDIRECT(MAINSITE_Admin . $user_access->class_name . '/' . $user_access->function_name);
			}
			$this->data['company_profile_data'] = $this->data['company_profile_data'][0];
		}

		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/company_profile/Company_Profile_Module/company_profile_edit', $this->data);
		parent::get_footer();
	}

	function userCompanyProfileDoEdit()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));

		if (empty($_POST['first_name']) && empty($_POST['country_id']) && empty($_POST['state_id']) && empty($_POST['address1']) && empty($_POST['email']) && empty($_POST['mobile_no']) && empty($_POST['company_name']) && empty($_POST['company_unique_name'])) {
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/" . $user_access->function_name);
			exit;
		}
		$company_profile_id = $_POST['company_profile_id'];

		//print_r($_POST);
		if (empty($this->data['user_access'])) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		if (empty($company_profile_id)) {
			if ($user_access->add_module != 1) {
				$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Add " . $user_access->module_name);
				REDIRECT(MAINSITE_Admin . "wam/access-denied");
			}
		}
		if (!empty($company_profile_id)) {
			if ($user_access->update_module != 1) {
				$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Update " . $user_access->module_name);
				REDIRECT(MAINSITE_Admin . "wam/access-denied");
			}
		}

		$company_unique_name = trim($_POST['company_unique_name']);
		$email = trim($_POST['email']);
		$name = trim($_POST['first_name']);
		if (!empty($_POST['last_name'])) {
			$name .= ' ' . trim($_POST['last_name']);
		}
		$country_id = $_POST['country_id'];
		$state_id = $_POST['state_id'];
		$city_id = $_POST['city_id'];
		$status = $_POST['status'];
		$is_exist = $this->Common_Model->getData(array('select' => '*', 'from' => 'company_profile', 'where' => "email = '$email' and company_profile_id != $company_profile_id"));
		if (!empty($is_exist)) {
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Company Email already exist in database.</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/company_profile-edit/" . $company_profile_id);
			exit;
		}

		$is_exist = $this->Common_Model->getData(array('select' => '*', 'from' => 'company_profile', 'where' => "company_unique_name = '$company_unique_name' and company_profile_id != $company_profile_id"));
		if (!empty($is_exist)) {
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Company Profile already exist in database.</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/company_profile-edit/" . $company_profile_id);
			exit;
		}

		$enter_data['gst_no'] = $_POST['gst_no'];
		$enter_data['email'] = $_POST['email'];
		$enter_data['company_unique_name'] = trim($_POST['company_unique_name']);
		$enter_data['company_name'] = trim($_POST['company_name']);
		$enter_data['first_name'] = trim($_POST['first_name']);
		$enter_data['last_name'] = trim($_POST['last_name']);
		$enter_data['address1'] = trim($_POST['address1']);
		$enter_data['address2'] = trim($_POST['address2']);
		$enter_data['address3'] = trim($_POST['address3']);
		$enter_data['mobile_no'] = trim($_POST['mobile_no']);
		$enter_data['alt_mobile_no'] = trim($_POST['alt_mobile_no']);
		$enter_data['company_email'] = trim($_POST['company_email']);
		$enter_data['company_website'] = trim($_POST['company_website']);
		$enter_data['pincode'] = trim($_POST['pincode']);
		$enter_data['country_id'] = $country_id;
		$enter_data['state_id'] = $state_id;
		$enter_data['city_id'] = $city_id;
		$enter_data['name'] = $name;
		$enter_data['status'] = $_POST['status'];

		$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong Please Try Again. </div>';
		if (!empty($company_profile_id)) {
			$enter_data['updated_on'] = date("Y-m-d H:i:s");
			$enter_data['updated_by'] = $this->data['session_uid'];
			$insertStatus = $this->Common_Model->update_operation(array('table' => 'company_profile', 'data' => $enter_data, 'condition' => "company_profile_id = $company_profile_id"));
			if (!empty($insertStatus)) {
				$alert_message = '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-check"></i> Record Updated Successfully </div>';
				$this->upload_company_letterhead_header_image($company_profile_id);
				$this->upload_company_logo($company_profile_id);
			}

		} else {
			$enter_data['added_on'] = date("Y-m-d H:i:s");
			$enter_data['added_by'] = $this->data['session_uid'];
			$company_profile_id = $insertStatus = $this->Common_Model->add_operation(array('table' => 'company_profile', 'data' => $enter_data));
			if (!empty($insertStatus)) {
				$alert_message = '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-check"></i> New Record Added Successfully </div>';
				$this->upload_company_logo($company_profile_id);
				$this->upload_company_letterhead_header_image($company_profile_id);
			}


		}
		$this->session->set_flashdata('alert_message', $alert_message);

		if (!empty($_POST['redirect_type'])) {
			REDIRECT(MAINSITE_Admin . $user_access->class_name . "/company_profile-edit");
		}

		REDIRECT(MAINSITE_Admin . $user_access->class_name . "/" . $user_access->function_name);
	}

	function upload_company_letterhead_header_image($company_profile_id)
	{
		$logo_file_name = "";
		if (isset($_FILES["letterhead_header_image"]['name'])) {
			$timg_name = $_FILES['letterhead_header_image']['name'];
			if (!empty($timg_name)) {
				$temp_var = explode(".", strtolower($timg_name));
				$timage_ext = end($temp_var);
				$timage_name_new = "letterhead_" . $company_profile_id . "." . $timage_ext;
				$image_enter_data['letterhead_header_image	'] = $timage_name_new;
				$imginsertStatus = $this->Common_Model->update_operation(array('table' => 'company_profile', 'data' => $image_enter_data, 'condition' => "company_profile_id = $company_profile_id"));
				if ($imginsertStatus == 1) {
					if (!is_dir(_uploaded_temp_files_ . 'company_profile')) {
						mkdir('./' . _uploaded_temp_files_ . 'company_profile', 0777, TRUE);
					}
					if (!is_dir(_uploaded_temp_files_ . 'company_profile/letterhead_header_image')) {
						mkdir('./' . _uploaded_temp_files_ . 'company_profile/letterhead_header_image', 0777, TRUE);
					}
					move_uploaded_file($_FILES['letterhead_header_image']['tmp_name'], _uploaded_temp_files_ . "company_profile/letterhead_header_image/" . $timage_name_new);
					$logo_file_name = $timage_name_new;
				}

			}
		}
	}

	function upload_company_logo($company_profile_id)
	{
		$logo_file_name = "";
		if (isset($_FILES["logo"]['name'])) {
			$timg_name = $_FILES['logo']['name'];
			if (!empty($timg_name)) {
				$temp_var = explode(".", strtolower($timg_name));
				$timage_ext = end($temp_var);
				$timage_name_new = "logo_" . $company_profile_id . "." . $timage_ext;
				$image_enter_data['logo'] = $timage_name_new;
				$imginsertStatus = $this->Common_Model->update_operation(array('table' => 'company_profile', 'data' => $image_enter_data, 'condition' => "company_profile_id = $company_profile_id"));
				if ($imginsertStatus == 1) {
					if (!is_dir(_uploaded_temp_files_ . 'company_profile')) {
						mkdir('./' . _uploaded_temp_files_ . 'company_profile', 0777, TRUE);
					}
					if (!is_dir(_uploaded_temp_files_ . 'company_profile/logo')) {
						mkdir('./' . _uploaded_temp_files_ . 'company_profile/logo', 0777, TRUE);
					}
					move_uploaded_file($_FILES['logo']['tmp_name'], _uploaded_temp_files_ . "company_profile/logo/" . $timage_name_new);
					$logo_file_name = $timage_name_new;
				}

			}
		}
	}

	function userCompanyProfile_doUpdateStatus()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 14;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id" => $this->data['page_module_id']));
		//print_r($this->data['user_access']);
		$task = $_POST['task'];
		$id_arr = $_POST['sel_recds'];
		if (empty($user_access)) {
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
		if ($user_access->update_module == 1) {
			$this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Something Went Wrong Please Try Again. 
				  </div>');
			$update_data = array();
			if (!empty($id_arr)) {
				$action_taken = "";
				$ids = implode(',', $id_arr);
				if ($task == "active") {
					$update_data['status'] = 1;
					$action_taken = "Activate";
				}
				if ($task == "block") {
					$update_data['status'] = 0;
					$action_taken = "Blocked";
				}
				$update_data['updated_on'] = date("Y-m-d H:i:s");
				$update_data['updated_by'] = $this->data['session_uid'];
				$response = $this->Common_Model->update_operation(array('table' => "company_profile", 'data' => $update_data, 'condition' => "company_profile_id in ($ids)"));
				if ($response) {
					$this->session->set_flashdata('alert_message', '<div class="alert alert-success alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<i class="icon fas fa-check"></i> Records Successfully ' . $action_taken . ' 
						</div>');
				}
			}
			REDIRECT(MAINSITE_Admin . $user_access->class_name . '/' . $user_access->function_name);
		} else {
			$this->session->set_flashdata('no_access_flash_message', "You Are Not Allowed To Update " . $user_access->module_name);
			REDIRECT(MAINSITE_Admin . "wam/access-denied");
		}
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



	public function index1()
	{
		$this->load->view('welcome_message');
	}

	function mypdf()
	{


		$this->load->library('pdf');


		$this->pdf->load_view('mypdf');
		$this->pdf->render();


		$this->pdf->stream("welcome.pdf");
	}
}
