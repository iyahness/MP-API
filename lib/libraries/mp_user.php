<?php

if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_user extends mp {

	function __construct()
	{
		parent::__construct();
	}

/**
 * CREATE
 */

	function CreateSimpleUser($displayname, $email, $contactID)
	{

		// create password
		$password = parent::CreatePassword();

		// echo $password;

		$fields = array (
			"User_Name"					=> $email,
			"User_Email"				=> $email,
			"dpmd5_Password"			=> $password,
			"Display_Name"				=> $displayname,
			"Contact_ID"				=> $contactID
		);

		$user_record = parent::AddRecord(101, "dp_Users", "User_ID", $fields);
		$user_result = parent::SplitToArray((string)$user_record->AddRecordResult);
		$NewUserID = $user_result[0];
		if( $NewUserID==0 ) {
			return "User record creation failed. Error: " . $user_result[2];
		}
		else {
			$result = array (
				"userID"	=> $NewUserID,
				"password"	=> $password
			);
			return $result;
		}

	}

/**
 * UPDATE
 */

	function UpdateUser($fields_array, $logged_in_user)
	{
		$record = parent::UpdateRecord($logged_in_user, "dp_Users", "User_ID", $fields_array);
		$data = parent::SplitToArray((string)$record->UpdateRecordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "User record update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "User record updated successfully."
			);
		}
		return $result;
	}

	function UpdateUserPassword($fields_array)
	{
		$record = parent::UpdateUserPassword($fields_array);
		$data = parent::SplitToArray((string)$record->UpdateUserPasswordResult);

		$status = $data[0];
		if( $status==0 ) {
			$result = array (
				"status"	=> false,
				"data"		=> $data,
				"message"	=> "Password update failed. Error: " . $data[2]
			);
		}
		else {
			$result = array (
				"status"	=> true,
				"data"		=> $data,
				"message"	=> "Password updated successfully."
			);
		}
		return $result;
	}

/**
 * MISC
 */

	function GetUserInfo($userID)
	{
		$parameters = array ('User_ID' => $userID);
		$request = parent::ExecuteSP('api_GetUserInfo', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}


/*	function ResetUserPassword($userID)
	{
		// this API method currently triggers an e-mail that contains the incorrect password. Think Ministry is aware
		// and working on a fix.

		// create password
		$password = parent::CreatePassword();

		$fields = array (
			"User_ID"		=> $userID,
			"dpmd5_Password" => $password
		);

		$user_record = parent::UpdateRecord(101, "dp_Users", "User_ID", $fields);
		$user_result = parent::SplitToArray((string)$user_record->UpdateRecordResult);

		$status = $user_result[0];
		if( $status==0 ) {
			$result = array (
				"password"	=> "User record update failed. Error: " . $user_result[2]
			);
		}
		else {
			$result = array (
				"password"	=> $password
			);
		}
		return $result;
	}*/

}