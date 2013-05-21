<?php

// for CodeIgniter
	if (!defined('BASEPATH')) exit('No direct script access allowed');

class mp {

	public $wsdl;
	public $guid;
	public $pw;
	public $servername;
	public $client;
	public $params;

	function __construct() {
		$this->obj =& get_instance(); // load CI object to access base libs

		// all config items stored in /Application/config/mp_config.php

		$this->wsdl = $this->obj->config->item('wsdl');
		$this->servername = $this->obj->config->item('servername');
		$this->guid = $this->obj->config->item('guid');
		$this->pw = $this->obj->config->item('pw');
		$this->params = $this->obj->config->item('params');
	}

	function APIEncode($ToEncode)
	{
		$ToReturn = str_replace("#","dp_Pound",$ToEncode);
		$ToReturn = str_replace("&","dp_Amp",$ToReturn);
		$ToReturn = str_replace("=","dp_Equal",$ToReturn);
		$ToReturn = str_replace("?","dp_Qmark",$ToReturn);
		return trim($ToReturn);
	}

	function ConvertToString($array) {
		$temp = array();
		foreach($array as $k=>$v) {
			$temp[] = $k . "=" . $this->APIEncode($v);
		}
		$string = implode( "&", $temp);
		return $string;
	}

	function SplitToArray($string) {
		$array = explode("|",$string); // separates the pipe delimited response string into an array
		return $array; // [0] = new ID
	}

	function CreatePassword() {
		mt_srand((double)microtime()*10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45); // "-"
			/*
			// build the full GUID
			$uuid = chr(123) // "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125); // "}"
			return $uuid;
			*/
		$newpass = substr($charid, 0, 8);
		return $newpass;
	}

/**
 *
 * @Method Parameters:
 *
 * $fn -> the API call (function name, a la "ExecuteStoredProcedure",
 * "AuthenticateUser", etc)
 *
 * $parameters -> An array of parameters passed through to the SOAP call. Includes
 * fields, Stored Procedure names, etc.
 *
 **/

	private function API_Call($fn, array $parameters)
	{
		try {
			$this->client = @new SoapClient($this->wsdl, $this->params);
		}
		catch(SoapFault $soap_error) {
			//echo $soap_error->faultstring;
			$request->Errors->API 	= "There was an error connecting to the API.";
			$request->Errors->SOAP 	= $soap_error->faultstring;
			return $request;
			exit;
		}

		try {
			$request = $this->client->__soapCall($fn, array('parameters' => $parameters));
		}
		catch(SoapFault $soap_error) {
			//echo $soap_error->faultstring;
			$request->Errors->API	= "Error retrieving data.";
			$request->Errors->SOAP 	= $soap_error->faultstring;
			return $request;
			exit;
		}

		return $request;
		unset($request);
	}

	function getFunctionList()
	{
		try {
			$this->client = @new SoapClient($this->wsdl, $this->params);
		}
		catch(SoapFault $soap_error) {
			echo $soap_error->faultstring;
		}

		$request = $this->client->__getFunctions();
		return $request;
		unset($request);
	}

/**
 * @METHODS
 *
 * The following methods use $this->API_Call (and optionally $this->ConvertToString)
 * to process specific MinistryPlatform API functions. They have been written such that
 * a user can create a new object, pass the parameters for a MP function, and get the
 * results back.
 *
 * $MP = new mp();
 * $stored_procedure_results = $MP->ExecuteSP($proc_name, $parameters);
 *
 * $stored_procedure_results would contain the data table returned from the call.
 * The benefit is that programmers do not need to spend the time writing out the SOAP
 * calls or other functions to actually process and handle the data. That is done for
 * you. You just need to understand what type each argument should be in order to
 * correctly process the API call.
 *
**/

	function authenticate_user( $user, $password )
	{

		$fields = array(
			'UserName' 		=> $user,
			'Password' 		=> $password,
			'ServerName' 	=> $this->servername
		);
		$request = $this->API_Call('AuthenticateUser', $fields);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function ExecuteSP($sp, array $request)
	{

		/**
		 * @sp -> Stored Procedure Name
		 * $sp = "api_myapp_SampleStoredProcName";
		 *
		 * Example $request array (should be field=>value pairings per MP API docs)
		 * $request = array(
		 *	'Field1'	=>	'Value1',
		 *	'Field2'	=>	'Value2'
		 * );
		 *
		 * $myResults = $this->MP_ExecuteSP($sp, $request);
		 *
		**/

		$requestString = $this->ConvertToString($request);

		$params = array(
			'GUID' => $this->guid,
			'Password' => $this->pw,
			'StoredProcedureName' => $sp,
			'RequestString' => $requestString
		);

		$result = $this->API_Call('ExecuteStoredProcedure', $params);
		$request = simplexml_load_string($result->ExecuteStoredProcedureResult->any);
		//var_dump($request);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function AddRecord($userID, $table, $pk, array $fields)
	{

		/**
		 * @AddRecord
		 *
		 * $userID -> authenticated MP User's ID. Pass 0 if anonymous
		 * $table -> The table to which you're adding a record
		 * $pk -> Primary Key of the table
		 * $fields -> Array of Field=>Value pairings to be added to the new record
		 *
		 * USE CASE
		 * $NewID = $this->AddRecord($userID, $table, $pk, $fields);
		 *
		**/

		$requestString = $this->ConvertToString($fields);
		$params = array(
			'GUID'				=> $this->guid,
			'Password'			=> $this->pw,
			'UserID'			=> $userID,
			'TableName'			=> $table,
			'PrimaryKeyField'	=> $pk,
			'RequestString'		=> $requestString
		);

		$request = $this->API_Call('AddRecord', $params);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function UpdateRecord($userID, $table, $pk, array $fields)
	{

		$requestString = $this->ConvertToString($fields);

		$params = array(
			'GUID'				=> $this->guid,
			'Password'			=> $this->pw,
			'UserID'			=> $userID,
			'TableName'			=> $table,
			'PrimaryKeyField'	=> $pk,
			'RequestString'		=> $requestString
		);

		$request = $this->API_Call('UpdateRecord', $params);

		// -1 means the record was updated successfully
		return $request; // If there are errors, they will be noted in the $request->Errors node

		unset($request);
	}

	function FindOrCreateUserAccount($array)
	{
		/*
		 **
		 ** this is the array passed into the method
		 **

			$array = array (
				'FirstName'			=> "Bobby",
				'LastName'			=> "Fischer",
				'MobilePhone'		=> "999-389-0300",
				'EmailAddress'		=> "qa8@thinkministry.com"
			);

		*/

		$params = array(
			'GUID'				=> $this->guid,
			'Password'			=> $this->pw,
			'FirstName'			=> $array['FirstName'],
			'LastName'			=> $array['LastName'],
			'MobilePhone'		=> $array['MobilePhone'],
			'EmailAddress'		=> $array['EmailAddress']
		);

		$request = $this->API_Call('FindOrCreateUserAccount', $params);

		// var_dump($request);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function GetUserInfo($userID)
	{

		# get basic data about the user

		/*
		 **
		 ** this is the array passed into the method
		 **
		*/

		$params = array(
			'GUID'				=> $this->guid,
			'Password'			=> $this->pw,
			'UserID'			=> $userID
		);

		$request = $this->API_Call('GetUserInfo', $params);

		// var_dump($request);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function UpdateUserAccount($array)
	{
		/*
		 **
		 ** this is an example of all possible values passed into the method
		 ** # if you don't want to update a field, don't include it in the array.
		 ** # just make sure you include the User ID so the correct record is updated.
		 **
				$array = array (
					'UserID'				=> 101,
					'FirstName'				=> "Kenneth",
					'LastName'				=> "Mulford",
					'MobilePhone'			=> "555-555-5555",
					'EmailAddress'			=> "ken@thinkministry.com",
					'NewPassword'			=> "itsasecret",
					'MiddleName'			=> "C",
					'NickName'				=> "Ken",
					'PrefixID'				=> 1,				// Use GetUserInfo to confirm this ID value
					'SuffixID'				=> 1,				// Use GetUserInfo to confirm this ID value
					'DOB'					=> "11/17/1982",
					'GenderID'				=> 1,				// Use GetUserInfo to confirm this ID value
					'MaritalStatusID'		=> 1				// Use GetUserInfo to confirm this ID value
				);

		*/

		$api_params = array(
			'GUID'					=> $this->guid,
			'Password'				=> $this->pw,
		);

		// merge arrays, prioritizing the GUID/Password
		$params = $api_params + $array;

		$request = $this->API_Call('UpdateUserAccount', $params);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function ResetPassword($array)
	{

		/*
		 **
		 **
				$array = array (
					'FirstName'				=> "Kenneth",
					'EmailAddress'			=> "ken@thinkministry.com"
				);

		*/

		$params = array(
			'GUID'				=> $this->guid,
			'Password'			=> $this->pw,
			'FirstName'			=> $array['FirstName'],
			'EmailAddress'		=> $array['EmailAddress']
		);

		$request = $this->API_Call('ResetPassword', $params);


		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function UpdateUserPassword($array)
	{
		/*
				$array = array (
					'UserID'				=> 101,
					'Password'				=> 'mynewpass'
				);

		*/

		$params = array(
			'GUID'					=> $this->guid,
			'Password'				=> $this->pw,
			'UserID'				=> $array['UserID'],
			'NewPassword'			=> $array['Password']
		);

		$request = $this->API_Call('UpdateUserPassword', $params);

		return $request; // If there are errors, they will be noted in the $request->Errors node
		unset($request);
	}

	function AttachFile($binarydata, $filename, $pageID, $recordID, $description=null, $isImage=false, $pixels=0)
	{

		$params = array(
			'GUID'						=> $this->guid,
			'Password'					=> $this->pw,
			'FileContents'				=> $binarydata, // byte array of binary data
			'FileName'					=> $filename, //filename including extensions
			'PageID'					=> $pageID, // MinistryPlatform page ID where this record is viewed
			'RecordID'					=> $recordID, // the ID of the record being updated
			'FileDescription'			=> $description, // 2000 character limit
			'IsImage'					=> $isImage, // true or false only: is the file an image
			'ResizeLongestDimension'	=> $pixels	// the number of pixels to resize the longest side of an *image*. 0=no resizing
		);

		$request = $this->API_Call('AttachFile', $params);

		return $request; // {uploaded file name | error code | return message}
		unset($request);
	}

	function UpdateDefaultImage($photoname, $pageID, $recordID)
	{

		$params = array(
			'GUID'						=> $this->guid,
			'Password'					=> $this->pw,
			'UniqueName'				=> $photoname, //file GUID name *excluding* extension
			'PageID'					=> $pageID, // MinistryPlatform page ID where this record is viewed
			'RecordID'					=> $recordID // the ID of the record being updated
		);


		$request = $this->API_Call('UpdateDefaultImage', $params);
		return $request; // {uploaded file name | error code | return message}
		unset($request);
	}
}