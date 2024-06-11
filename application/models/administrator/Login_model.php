<?php

class Login_model extends CI_Model
{
	protected $search_query;

	function __construct($search_query = '')
	{
		//parent::__construct();
		$this->load->database();
		$this->search_query = $search_query;
		$this->db->query("SET sql_mode = ''");
	}


	//ADMIN_LOGIN////////////////////////////
	function doSignInUser($params = array())
	{
		// Initialize the status as true
		$status = true;

		// Get the username and password from POST data
		$username = $_POST['username'];

		// Hash the password using MD5 (Note: MD5 is not recommended for secure password hashing)
		$password = md5($_POST['password']);

		// Build the query to select the user from the admin_user table
		// The query selects all columns from the admin_user table where either the email or username matches
		// and the password matches the hashed password, limited to one result
		$this->db
			->select('u.*')
			->from('admin_user as u')
			->where("(email = '$username' or username = '$username')")
			->where('password', $password)
			->limit(1);

		// Execute the query //"$q_result" gives the object not array
		$q_result = $this->db->get();


		// Check if any rows are returned
		if ($q_result->num_rows() > 0) {
			// Convert the q_result to an array of objects
			$result = $q_result->result();//here $result is array

			// Take the first (and only) result object
			$result = $result[0];//here final $result in an object

			// Check if the user's status is active (status = 1)
			if ($result->status == 1) {
				// Build a query to get the user's roles and associated company information
				$this->db->select("aur.* , urm.user_role_name , cp.company_unique_name");
				$this->db->from("admin_user_role as aur");

				// Join the users_role_master table to get role names
				$this->db->join("users_role_master as urm", "urm.user_role_id = aur.user_role_id");

				// Join the company_profile table to get company unique names
				$this->db->join("company_profile as cp", "cp.company_profile_id = aur.company_profile_id");

				// Filter by the admin user's ID
				$this->db->where("aur.admin_user_id", $result->admin_user_id);

				// Execute the query and add the roles to the result object
				$result->roles = $this->db->get()->result();

				// Get the client's IP address
				$client_ip = $this->Common_Model->get_client_ip();

				// Prepare data to update the last login time and IP address
				$update_login['last_login'] = date('Y-m-d H:i:s');
				$update_login['last_loginip'] = $client_ip;

				// Update the admin_user table with the last login data
				$response = $this->Common_Model->update_operation(
					array(
						'table' => "admin_user",
						'data' => $update_login,
						'condition' => "(admin_user_id = $result->admin_user_id)"
					)
				);
			}

			// Return the user data, including roles
			return $result;

		} else {
			// Return false if no user was found or if the credentials are incorrect
			return false;
		}
	}




}

?>