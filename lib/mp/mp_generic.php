<?php

/**
 *
 *
 */

if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_generic extends mp {

	function __construct()
	{
		parent::__construct();
	}

/**
 * \\ AddRecord() Calls //
 */

	function CreateContactLogEntry($fields_array, $logged_in_user)
	{
		$record = parent::AddRecord($logged_in_user, "Contact_Log", "Contact_Log_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->AddRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Contact Log Entry creation failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Contact Log Entry created successfully."
			);
		}
		return $result;
	}

	function CreateContactAttribute($fields_array, $logged_in_user)
	{
		$record = parent::AddRecord($logged_in_user, "Contact_Attributes", "Contact_Attribute_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->AddRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Contact Attribute creation failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Contact Attribute created successfully."
			);
		}
		return $result;
	}

/**
 * \\ UpdateRecord() Calls //
 */

	function UpdateContactAttribute($fields_array, $logged_in_user)
	{
		$record = parent::UpdateRecord($logged_in_user, "Contact_Attributes", "Contact_Attribute_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->UpdateRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Contact Attribute update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Contact Attribute updated successfully."
			);
		}
		return $result;
	}

/**
 * \\ Misc API Calls //
 */



}