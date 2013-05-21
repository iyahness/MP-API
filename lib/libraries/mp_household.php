<?php

if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_household extends mp {

	function __construct()
	{
		parent::__construct();
	}


/**
 * \\ AddRecord() Calls //
 */
	function CreateHousehold($fields_array, $logged_in_user)
	{
		$record = parent::AddRecord($logged_in_user, "Households", "Household_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->AddRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Household record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Household record updated successfully."
			);
		}
		return $result;
	}

/**
 * \\ UpdateRecord() Calls //
 */

	function UpdateHousehold($fields_array, $logged_in_user)
	{
		$record = parent::UpdateRecord($logged_in_user, "Households", "Household_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->UpdateRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Household record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Household record updated successfully."
			);
		}
		return $result;
	}

/**
 * \\ Misc API Calls //
 */

}