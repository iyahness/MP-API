<?php

// if (!defined('BASEPATH')) exit('No direct script access allowed'); // CodeIgniter

class mp_user extends mp {

	function __construct()
	{
		parent::__construct();
	}

	function GetUserInfo($userID)
	{
		$parameters = array ('User_ID' => $userID);
		$request = parent::ExecuteSP('api_GetUserInfo', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function FindContact(array $parameters)
	{
		/*
		 **
		 ** this is the array passed into the method
		 **

			$parameters = array (
				'FirstName'			=> "Aardy",
				'LastName'			=> "Aardvark",
				'Suffix'			=> "",
				'Phone'				=> "1234567890",
				'EmailAddress'		=> "thinkministryqa1@gmail.com",
			);
		*/

		// this SP is a MP Portal SP. Copy it to create your own to avoid any future changes from MP
		$request = parent::ExecuteSP('api_MPP_FindMatchingContact', $parameters);

		return $request;
		// If there are API errors, they will be noted in the $request->Errors node
		//The contact info will be in $request->NewDataSet->Table if it's present
	}

	function GetContactByEmail(array $parameters)
	{
		/*
		 **
		 ** this is the array passed into the method
		 **

			$parameters = array (
				'EmailAddress'		=> "thinkministryqa1@gmail.com"
			);
		*/

		$request = parent::ExecuteSP('api_Common_GetContactByEmail', $parameters);
		return $request;
		// If there are API errors, they will be noted in the $request->Errors node
		//The contact info will be in $request->NewDataSet->Table if it's present
	}

	function CreateSimpleContact($firstname, $lastname, $email, $phone)
	{
		// Note:  0 = anonymous userID. If you have a designated user account for API transactions, use that ID instead.

		/*
		 * @Create Household
		 * Before a new contact can be created, we always create a new Household record so the Contact is not orphaned
		 */

// 		echo "First: $firstname <br />";
// 		echo "Last: $lastname <br />";
// 		echo "Phone: $phone <br />";
// 		echo "Email: $email <br />";

		$hh_fields = array ("Household_Name" => $lastname);
		$household_record = parent::AddRecord(0, "Households", "Household_ID", $hh_fields);
		$household_result = parent::SplitToArray((string)$household_record->AddRecordResult);
		$householdID = $household_result[0];
		if( $householdID==0 ) {
			return "Household record creation failed. Error: " . $household_result[2];
		}

		else {

			/*
			 * @Create Contact
			 */
//  			echo "First: $firstname <br />";
// 				echo "Last: $lastname <br />";
// 				echo "Phone: $phone <br />";
// 				echo "Email: $email <br />";

			$fields = array (
				"Company"					=> 0,
				"First_Name"				=> $firstname,
				"Nickname"					=> $firstname,
				"Last_Name"					=> $lastname,
				"Display_Name"				=> $lastname . ", " . $firstname,
				"Contact_Status_ID"			=> $this->Default_Contact_Status_ID, // 'mp_config' value
				"Household_Position_ID"		=> 1, // head of household
				"Mobile_Phone"				=> $phone,
				"Email_Address"				=> $email,
				"Household_ID"				=> $householdID
			);

			// var_dump($fields); echo "<br /><br />";

			$contact_record = parent::AddRecord(0, "Contacts", "Contact_ID", $fields);
			$contact_result = parent::SplitToArray((string)$contact_record->AddRecordResult);
			$NewContactID = $contact_result[0];
			if( $NewContactID==0 ) {
				return "Contact record creation failed. Error: " . $contact_result[2];
			}
			else {
				return $NewContactID;
			}
		}
	}

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

	function UpdateUser($userID)
	{

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
	}

}