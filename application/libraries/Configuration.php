<?php
// Check if the 'BASEPATH' constant is defined, if not, exit the script.
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// Define a class named 'Configuration'.
class Configuration
{
	// Declare private property to hold the CodeIgniter instance.
	private $CI;

	// Declare public properties for session data.
	public $session_uid = '';
	public $session_name = '';
	public $session_email = '';

	// Constructor method for the Configuration class.
	function __construct()
	{
		// Get the CodeIgniter instance and assign it to the private property.
		$this->CI =& get_instance();

		// Load the database library.
		$this->CI->load->database();

		// Call the private method to set the configuration.
		$this->_set_configuration();
	}

	// Private method to set configuration data.
	function _set_configuration($params = array())
	{
		// Define the SQL query to fetch configuration data.
		$query = 'SELECT
            configuration.id,
            configuration.name,
            configuration.status,
            concat(\'{\', GROUP_CONCAT(concat(\'"\', configuration_data.name, \'":"\', configuration_data.value, \'"\')) ,\'}\') as config_data
        FROM
            configuration
        JOIN configuration_data ON configuration_data.configuration_id = configuration.id
        group by configuration.id';

		// Execute the query and get the result.
		$query_data = $this->CI->db->query($query);

		// Convert the query result to an array.
		$response = (array) $query_data->result();

		// Iterate through each configuration record.
		foreach ($response as $key => $value) {
			// Define constants for configuration name and status if not already defined.
			defined($value->name) or define($value->name, $value->status);

			// Decode the configuration data JSON string into an associative array.
			$config_data = json_decode($value->config_data, true);

			// Iterate through each configuration data item.
			foreach ($config_data as $cd_key => $cd_value) {
				// Define constants for configuration data if not already defined.
				defined($cd_key) or define($cd_key, $cd_value);
			}

			// Define specific constants based on configuration ID.
			if ($value->id == 1) {
				define('__sms_user_id__', _sms_userid);
				define('__sms_user_password__', _sms_password);
			} else if ($value->id == 2) {
				define('__email_user_id__', _email_userid);
				define('__email_user_password__', _email_password);
			}
		}
	}

	// Method to get data from the database based on parameters.
	function getData($params = array())
	{
		// Select specific fields from the database.
		$this->CI->db->select($params['select']);

		// Specify the table to select data from.
		$this->CI->db->from($params['from']);

		// Add a WHERE clause to the query.
		$this->CI->db->where("($params[where])");

		// Add a LIMIT clause to the query if specified.
		if (!empty($params['limit'])) {
			$this->CI->db->limit($params['limit']);
		}

		// Add an ORDER BY clause to the query if specified.
		if (!empty($params['order_by'])) {
			$this->CI->db->order_by($params['order_by']);
		}

		// Execute the query and get the result.
		$query_get_list = $this->CI->db->get();

		// Return the result as an array of objects.
		return $query_get_list->result();
	}

	// Method to add data to the database.
	function add_operation($params = array())
	{
		// Return false if no parameters are provided.
		if (empty($params))
			return false;

		// Insert data into the specified table.
		$status = $this->CI->db->insert($params['table'], $params['data']);

		// If the insert is successful, get the insert ID.
		if ($status) {
			$status = $this->CI->db->insert_id();
		}

		// Return the insert ID or false.
		return $status;
	}
}
?>