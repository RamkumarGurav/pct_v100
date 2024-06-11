<?php

include_once (APPPATH . 'models/administrator/Database_Tables.php');

class Admin_model extends Database_Tables
{

	function __construct()
	{

		parent::__construct();
		$this->db->query("SET sql_mode = ''");

	}







}
