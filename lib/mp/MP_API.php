<?php

# class mp { // name used for my codeigniter library
class MP_API {

	public $wsdl;
	public $guid;
	public $pw;
	public $servername;
	public $client;
	public $params;

	function __construct() {
		include('mp_config.php');
	}

	private function ConvertToString($array) {
		$temp = array();
		foreach($array as $k=>$v) {
			$temp[] = $k . "=" . $v;
		}
		$string = implode( "&", $temp);
		return $string;
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

	private function API_Call($fn, array $parameters) {
		try {
			$this->client = @new SoapClient($this->wsdl, $this->params);
		}
		catch(SoapFault $soap_error) {
			//echo $soap_error->faultstring;
			$request->ApiError = "There was an error connecting to the API.";
			return $request;
			exit;
		}

		try {
			$request = $this->client->__soapCall($fn, array('parameters' => $parameters));
		}
		catch(SoapFault $soap_error) {
			echo $soap_error->faultstring;
			$request->ApiError = "Error retrieving data.";
			return $request;
			exit;
		}

		return $request;
		unset($request);
	}

	function getFunctionList() {
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
 * $MP = new MP_API();
 * $stored_procedure_results = $MP->ExecuteSP($proc_name, $parameters);
 *
 * $stored_procedure_results would contain the data table returned from the call.
 * The benefit is that programmers do not need to spend the time writing out the SOAP
 * calls or other functions to actually process and handle the data. That is done for
 * you. You just need to understand what type each argument should be in order to
 * correctly process the API call.
 *
**/


	function authenticate_user( $user, $password ) {

		$fields = array(
			'UserName' 		=> $user,
			'Password' 		=> $password,
			'ServerName' 	=> $this->servername
		);
		$request = $this->API_Call('AuthenticateUser', $fields);
		if($request != NULL) {
			return $request;
		}
		unset($request);
	}

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
 * USE CASE
 * $myResults = $this->MP_ExecuteSP($sp, $request);
 *
**/

	function ExecuteSP($sp, array $request) {

		$requestString = $this->ConvertToString($request);

		$params = array(
			'GUID' => $this->guid,
			'Password' => $this->pw,
			'StoredProcedureName' => $sp,
			'RequestString' => $requestString
		);
		$result = $this->API_Call('ExecuteStoredProcedure', $params);
		//var_dump($result);
		$request = simplexml_load_string($result->ExecuteStoredProcedureResult->any);
		//var_dump($response);
		if($request != NULL) {
			return $request;
		}
		unset($request);
	}

/**
 * @MP_AddRecord
 *
 * $userID -> authenticated MP User's ID. Pass 0 if anonymous
 * $table -> The table to which you're adding a record
 * $pk -> Primary Key of the table
 * $fields -> Array of Field=>Value pairings to be added to the new record
 *
 * USE CASE
 * $NewID = $this->MP_AddRecord($userID, $table, $pk, $fields);
 *
**/

	function AddRecord($userID, $table, $pk, array $fields) {

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

		if($request != NULL) {
			return $request;
		}
		unset($request);
	}

	function UpdateRecord($userID, $table, $pk, array $fields) {

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
		if($request != NULL) {
			return $request; // -1 means the record was updated successfully
		}
		unset($request);
	}


	function FindOrCreateUserAccount($array) {
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

		if($request != NULL) {
			return $request;
		}
		unset($request);
	}

	function GetUserInfo($userID) {

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

		if($request != NULL) {
			return $request; // -1 means the record was updated successfully
		}
		unset($request);
	}

	function UpdateUserAccount($array) {
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

		// var_dump($request);

		if($request != NULL) {
			return $request; // -1 means the record was updated successfully
		}
		unset($request);
	}

	function ResetPassword($array) {

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

		// var_dump($request);

		if($request != NULL) {
			return $request; // -1 means the record was updated successfully
		}
		unset($request);
	}

}

/* no ending ?> on purpose */