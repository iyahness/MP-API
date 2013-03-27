<?php

// if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_address extends mp {

	function __construct()
	{
		parent::__construct();
	}

/**
 * \\ AddRecord() Calls //
 */

	function CreateAddress($fields_array, $logged_in_user)
	{
		$record = parent::AddRecord($logged_in_user, "Addresses", "Address_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->AddRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Address record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Address record updated successfully."
			);
		}
		return $result;
	}

/**
 * \\ UpdateRecord() Calls //
 */

	function UpdateAddress($fields_array, $logged_in_user)
	{
		$record = parent::UpdateRecord($logged_in_user, "Addresses", "Address_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->UpdateRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Address record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Address record updated successfully."
			);
		}
		return $result;
	}

/**
 * \\ Misc API Calls //
 */


}