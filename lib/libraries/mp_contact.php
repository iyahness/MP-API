<?php

if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_contact extends mp {

	function __construct()
	{
		parent::__construct();
	}

/**
 * \\ AddRecord() Calls //
 */

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


/**
 * \\ UpdateRecord() Calls //
 */

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

/**
 * \\ Misc API Calls //
 */

	function FindContact(array $parameters)
	{
		/*
		 **
		 ** this is the array passed into the method
		 **

			$parameters = array (
				'FirstName'			=> "Aardy",
				'LastName'			=> "Aardvark",
				'Phone'				=> "1234567890",
				'EmailAddress'		=> "thinkministryqa1@gmail.com",
			);
		*/

		$request = parent::ExecuteSP('api_MeetTheNeed_FindMatchingContact', $parameters);
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

	function GetContactLookupData()
	{
		$parameters = array ();
		$request = parent::ExecuteSP('api_Common_GetContactLookupData', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function GetContactHouseholdInfo($parameters)
	{
		$request = parent::ExecuteSP('api_Common_GetContactHouseholdInfo', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}


}