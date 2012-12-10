<?php

// if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_contact extends mp {

	function __construct()
	{
		parent::__construct();
	}

	function GetContactLookupData()
	{
		$parameters = array ();
		$request = parent::ExecuteSP('api_Common_GetContactLookupData', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function GetCongregations()
	{
		$parameters = array ();
		$request = parent::ExecuteSP('api_Common_GetCongregations', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function GetContactHouseholdInfo($parameters)
	{
		$request = parent::ExecuteSP('api_Common_GetContactHouseholdInfo', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function UpdateContact($fields_array, $logged_in_user)
	{
		//var_dump($fields_array);

		$record = parent::UpdateRecord($logged_in_user, "Contacts", "Contact_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->UpdateRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Contact record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Contact record updated successfully."
			);
		}
		return $result;
	}

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

	function UpdateContactPhoto($binaryphoto, $filename, $pageID, $contactID)
	{
		// data prep
		$description='updated contact';
		$isImage = true;
		$pixels = 300; // 300 is recommended default. 0 == no resizing

		$record = parent::AttachFile($binaryphoto, $filename, $pageID, $contactID, $description, $isImage, $pixels);
		$data = parent::SplitToArray((string)$record->AttachFileResult);
		return $data;
	}

	function UpdateDefaultImage($filename, $pageID, $recordID)
	{
		$record = parent::UpdateDefaultImage($filename, $pageID, $recordID);
		$data = parent::SplitToArray((string)$record->UpdateDefaultImageResult);
		return $data;
	}

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
}