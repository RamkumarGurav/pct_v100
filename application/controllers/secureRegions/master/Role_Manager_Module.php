<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH."controllers/secureRegions/Main.php");
class Role_Manager_Module extends Main {

	function __construct() {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->model('Common_Model');
		$this->load->model('administrator/Admin_Common_Model');
		$this->load->model('administrator/Admin_model');
		$this->load->model('administrator/master/Role_Manager_Model');
		$this->load->library('pagination');
		
		$this->load->library('User_auth');
		
		$session_uid = $this->data['session_uid']=$this->session->userdata('sess_psts_uid');
		$this->data['session_name']=$this->session->userdata('sess_psts_name');
		$this->data['session_email']=$this->session->userdata('sess_psts_email');

		$this->load->helper('url');
		
		$this->data['User_auth_obj'] = new User_auth();
		$this->data['user_data'] = $this->data['User_auth_obj']->check_user_status();

		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->data['master_name'] = array(
			"1"=>"Master",
			"2"=>"Human Resource",
			"3"=>"Company",
			"4"=>"Catalog",
			"5"=>"Enquiry",
			"6"=>"Banner",
			"7"=>"Gallery",
			"8"=>"Customers",
			"9"=>"Invoice",
			"10"=>"Customer Reports",
			"11"=>"Operations",
			"12"=>"Delivery Note",
			"13"=>"Orders"
		);
		
    }

	function unset_only() {
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
		$this->load->view('admin/master/Role_Manager_Module/list' , $this->data);
		parent::get_footer();
	}

	function role_manager_list()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if(empty($this->data['user_access']))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		$search = array();
		$field_name = '';
		$field_value = '';
		$end_date = '';
		$start_date = '';
		$record_status="";

		if(!empty($_REQUEST['field_name']))
			$field_name = $_POST['field_name'];
		else if(!empty($field_name))	
			$field_name = $field_name;
			
		if(!empty($_REQUEST['field_value']))
			$field_value = $_POST['field_value'];
		else if(!empty($field_value))	
			$field_value = $field_value;
			
		if(!empty($_POST['end_date']))
			$end_date = $_POST['end_date'];
		
			if(!empty($_POST['start_date']))
			$start_date = $_POST['start_date'];
			 
		if(!empty($_POST['record_status']))
			$record_status = $_POST['record_status'];
				 
		
		$this->data['field_name'] = $field_name;
		$this->data['field_value'] = $field_value;
		$this->data['end_date'] = $end_date;
		$this->data['start_date'] = $start_date;
		$this->data['record_status'] = $record_status;
		
		$search['end_date'] = $end_date;
		$search['start_date'] = $start_date;
		$search['field_value'] = $field_value;
		$search['field_name'] = $field_name;
		$search['record_status'] = $record_status;
		$search['search_for'] = "count";
		
		$data_count = $this->Role_Manager_Model->get_users_role_master($search);
		$r_count = $this->data['row_count'] = $data_count[0]->counts;

		unset($search['search_for']);
		
		$offset = (int)$this->uri->segment(5); //echo $offset;
		if($offset == "")
		{
			$offset ='0' ;
		} 
		$per_page = _all_pagination_;
		
		$this->load->library('pagination');
		//$config['base_url'] =MAINSITE.'secure_region/reports/DispatchedOrders/'.$module_id.'/';
		$this->load->library('pagination');
		$config['base_url'] =MAINSITE_Admin.$this->data['user_access']->class_name.'/'.$this->data['user_access']->function_name.'/';
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
		$this->data['users_role_master_data'] = $this->Role_Manager_Model->get_users_role_master($search);
		
		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/master/Role_Manager_Module/role_manager_list' , $this->data);
		parent::get_footer();
	}

	function role_manager_list_export()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if(empty($this->data['user_access']))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}

		if($this->data['user_access']->export_data!=1)
		{
			$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Export ".$user_access->module_name);
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		$search = array();
		$field_name = '';
		$field_value = '';
		$end_date = '';
		$start_date = '';
		$record_status="";

		if(!empty($_REQUEST['field_name']))
			$field_name = $_POST['field_name'];
		else if(!empty($field_name))	
			$field_name = $field_name;
			
		if(!empty($_REQUEST['field_value']))
			$field_value = $_POST['field_value'];
		else if(!empty($field_value))	
			$field_value = $field_value;
			
		if(!empty($_POST['end_date']))
			$end_date = $_POST['end_date'];
		
			if(!empty($_POST['start_date']))
			$start_date = $_POST['start_date'];
			 
		if(!empty($_POST['record_status']))
			$record_status = $_POST['record_status'];
				 
		
		$this->data['field_name'] = $field_name;
		$this->data['field_value'] = $field_value;
		$this->data['end_date'] = $end_date;
		$this->data['start_date'] = $start_date;
		$this->data['record_status'] = $record_status;
		
		$search['end_date'] = $end_date;
		$search['start_date'] = $start_date;
		$search['field_value'] = $field_value;
		$search['field_name'] = $field_name;
		$search['record_status'] = $record_status;
		
		$this->data['users_role_master_data'] = $this->Role_Manager_Model->get_users_role_master($search);
		
		
		$this->load->view('admin/master/Role_Manager_Module/role_manager_list_export' , $this->data);
	}

	function role_manager_view($user_role_id="")
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if(empty($user_role_id))
		{
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin.$user_access->class_name."/".$user_access->function_name);
			exit;
		}
		if(empty($this->data['user_access']))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		$this->data['page_is_master'] = $this->data['user_access']->is_master;
		$this->data['page_parent_module_id'] = $this->data['user_access']->parent_module_id;
		$this->data['users_role_master_data'] = $this->Role_Manager_Model->get_users_role_master(array("user_role_id"=>$user_role_id));
		if(empty($user_role_id))
		{
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin.$user_access->class_name."/".$user_access->function_name);
			exit;
		}
		$this->data['module_data'] = $this->Common_Model->getData(array('select'=>'*' , 'from'=>'module_master' , 'where'=>"module_id >0"));
		$this->data['module_permission_data'] = $this->Common_Model->getData(array('select'=>'*' , 'from'=>'module_permissions' , 'where'=>"user_role_id = $user_role_id"));
		
		$this->data['users_role_master_data'] = $this->data['users_role_master_data'][0];
		
		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/master/Role_Manager_Module/role_manager_view' , $this->data);
		parent::get_footer();
	}

	function role_manager_edit($user_role_id=0)
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		//print_r($this->data['user_access']);
		if(empty($this->data['user_access']))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		if(empty($user_role_id))
		{
			if($user_access->add_module!=1)
			{
				$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Add ".$user_access->module_name);
				REDIRECT(MAINSITE_Admin."wam/access-denied");
			}
		}
		if(!empty($user_role_id))
		{
			if($user_access->update_module!=1)
			{
				$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Update ".$user_access->module_name);
				REDIRECT(MAINSITE_Admin."wam/access-denied");
			}
		}

		$this->data['module_data'] = $this->Common_Model->getData(array('select'=>'*' , 'from'=>'module_master' , 'where'=>"module_id >0"));
		$this->data['module_permission_data'] = $this->Common_Model->getData(array('select'=>'*' , 'from'=>'module_permissions' , 'where'=>"user_role_id = $user_role_id"));

		$this->data['page_is_master'] = $this->data['user_access']->is_master;
		$this->data['page_parent_module_id'] = $this->data['user_access']->parent_module_id;
		if(!empty($user_role_id)){
			$this->data['users_role_master_data'] = $this->Role_Manager_Model->get_users_role_master(array("user_role_id"=>$user_role_id));
			if(empty($this->data['users_role_master_data']))
			{
				$this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Record Not Found. 
				  </div>');
				REDIRECT(MAINSITE_Admin.$user_access->class_name.'/'.$user_access->function_name);
			}
			$this->data['users_role_master_data'] = $this->data['users_role_master_data'][0];
		}
		
		parent::get_header();
		parent::get_left_nav();
		$this->load->view('admin/master/Role_Manager_Module/role_manager_edit' , $this->data);
		parent::get_footer();
	}

	function userRoleDoEdit()
	{
		
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		
		if(empty($_POST['user_role_name']))
		{
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong. Please Try Again. anubhav</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			REDIRECT(MAINSITE_Admin.$user_access->class_name."/".$user_access->function_name);
			exit;
		}
		$user_role_id = $_POST['user_role_id'];
		//print_r($_POST);
		if(empty($this->data['user_access']))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		if(empty($user_role_id))
		{
			if($user_access->add_module!=1)
			{
				$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Add ".$user_access->module_name);
				REDIRECT(MAINSITE_Admin."wam/access-denied");
			}
		}
		if(!empty($user_role_id))
		{
			if($user_access->update_module!=1)
			{
				$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Update ".$user_access->module_name);
				REDIRECT(MAINSITE_Admin."wam/access-denied");
			}
		}
		$user_role_name = trim($_POST['user_role_name']);
		$status = $_POST['status'];
		$is_exist = $this->Common_Model->getData(array('select'=>'*' , 'from'=>'users_role_master' , 'where'=>"user_role_name = '$user_role_name' and user_role_id != $user_role_id"));
	//	echo $this->db->last_query();
	//	print_r($is_exist);
		if(!empty($is_exist))
		{
			$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> User Role already exist in database.</div>';
			$this->session->set_flashdata('alert_message', $alert_message);
			//echo $this->session->flashdata('alert_message' );
			//echo "anubhav";
			REDIRECT(MAINSITE_Admin.$user_access->class_name."/role-manager-edit/".$user_role_id);
			exit;
		}

		$enter_data['user_role_name'] = $user_role_name;
		$enter_data['status'] = $_POST['status'];
		
		$alert_message = '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-ban"></i> Something Went Wrong Please Try Again. </div>';
		$insertStatus=0;
		if(!empty($user_role_id))
		{
			$enter_data['updated_on'] = date("Y-m-d H:i:s");
			$enter_data['updated_by'] = $this->data['session_uid'];
			$insertStatus = $this->Common_Model->update_operation(array('table'=>'users_role_master', 'data'=>$enter_data, 'condition'=>"user_role_id = $user_role_id"));
			if(!empty($insertStatus))
			{
				$alert_message = '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-check"></i> Record Updated Successfully </div>';
			}
		}
		else
		{
			$enter_data['added_on'] = date("Y-m-d H:i:s");
			$enter_data['added_by'] = $this->data['session_uid'];
			$user_role_id = $insertStatus = $this->Common_Model->add_operation(array('table'=>'users_role_master' , 'data'=>$enter_data));
			if(!empty($insertStatus))
			{
				$alert_message = '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><i class="icon fas fa-check"></i> New Record Added Successfully </div>';
			}
		}
		if($insertStatus>0)
		{
			$this->Common_Model->delete_operation(array('table'=>'module_permissions' , 'where'=>"user_role_id = $user_role_id"));
			//delete_operation
			if(!empty($_POST['module_ids']))
			{
				$module_id_arr = $_POST['module_ids'];
				foreach($module_id_arr as $module_id)
				{
					$is_insert = false;
					$module_permission_data['module_id'] = $module_id;
					$module_permission_data['user_role_id'] = $user_role_id;
					$module_permission_data['view_module'] = 0;
					$module_permission_data['add_module'] = 0;
					$module_permission_data['update_module'] = 0;
					$module_permission_data['delete_module'] = 0;
					$module_permission_data['approval_module'] = 0;
					$module_permission_data['import_data'] = 0;
					$module_permission_data['export_data'] = 0;
					
					if(!empty($_POST['view_'.$module_id]))
					{ $module_permission_data['view_module'] = 1;			$is_insert = true; }

					if(!empty($_POST['add_'.$module_id]))
					{ $module_permission_data['add_module'] = 1;			$is_insert = true; }

					if(!empty($_POST['update_'.$module_id]))
					{ $module_permission_data['update_module'] = 1;			$is_insert = true; }

					if(!empty($_POST['delete_'.$module_id]))
					{ $module_permission_data['delete_module'] = 1;			$is_insert = true; }

					if(!empty($_POST['approve_'.$module_id]))
					{ $module_permission_data['approval_module'] = 1;		$is_insert = true; }

					if(!empty($_POST['import_'.$module_id]))
					{ $module_permission_data['import_data'] = 1;			$is_insert = true; }

					if(!empty($_POST['export_'.$module_id]))
					{ $module_permission_data['export_data'] = 1;			$is_insert = true; }

					if($is_insert)
					{
						$this->Common_Model->add_operation(array('table'=>'module_permissions' , 'data'=>$module_permission_data));
					}
				}
			}
		}
		$this->session->set_flashdata('alert_message', $alert_message);

		if(!empty($_POST['redirect_type']))
		{
			REDIRECT(MAINSITE_Admin.$user_access->class_name."/role-manager-edit");
		}

		REDIRECT(MAINSITE_Admin.$user_access->class_name."/".$user_access->function_name);
	}
	
	function userRole_doUpdateStatus()
	{
		$this->data['page_type'] = "list";
		$this->data['page_module_id'] = 1;
		$user_access = $this->data['user_access'] = $this->data['User_auth_obj']->check_user_access(array("module_id"=>$this->data['page_module_id']));
		//print_r($this->data['user_access']);
		$task = $_POST['task'];
		$id_arr = $_POST['sel_recds'];
		if(empty($user_access))
		{
			REDIRECT(MAINSITE_Admin."wam/access-denied");
		}
		if($user_access->update_module==1)
		{
			$this->session->set_flashdata('alert_message', '<div class="alert alert-danger alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
					<i class="icon fas fa-ban"></i> Something Went Wrong Please Try Again. 
				  </div>');
			$update_data = array();
			if(!empty($id_arr))
			{
				$action_taken = "";
				$ids = implode(',' , $id_arr);
				if($task=="active")
				{
					$update_data['status'] = 1;
					$action_taken = "Activate";
				}
				if($task=="block")
				{
					$update_data['status'] = 0;
					$action_taken = "Blocked";
				}
				$update_data['updated_on'] = date("Y-m-d H:i:s");
				$update_data['updated_by'] = $this->data['session_uid'];
				$response = $this->Common_Model->update_operation(array('table'=>"users_role_master" , 'data'=>$update_data , 'condition'=>"user_role_id in ($ids)" ));
				if($response){
					$this->session->set_flashdata('alert_message', '<div class="alert alert-success alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
						<i class="icon fas fa-check"></i> Records Successfully '.$action_taken.' 
						</div>');
				}
			}
			REDIRECT(MAINSITE_Admin.$user_access->class_name.'/'.$user_access->function_name);
		}
		else
		{
			$this->session->set_flashdata('no_access_flash_message' , "You Are Not Allowed To Update ".$user_access->module_name);
			REDIRECT(MAINSITE_Admin."wam/access-denied");
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
		REDIRECT(MAINSITE_Admin.'login');
	}

	

	public function index1()
	{
		$this->load->view('welcome_message');
	}

	function mypdf(){


		$this->load->library('pdf');
	
	
		  $this->pdf->load_view('mypdf');
		  $this->pdf->render();
	
	
		  $this->pdf->stream("welcome.pdf");
	   }
}
