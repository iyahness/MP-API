<?php

class MP_API {

/**
 * API Methods for MinistryPlatform
 */

	/**
	 * @wsdl
	 * The absolute URL to your MinistryPlatform API file. Default is:
	 * <your server>/ministryplatform/public/api.asmx?WSDL
	 *
	 * @guid
	 * Your API GUID is located in the web.config file for any application that uses the API,
	 * such as the Portal, Check-In, or CoreTools.
	 *
	 * @pw
	 * Your API password is located below your API GUID.
	 *
	 * @servername
	 * This is the server name that you're connecting to. Usually this will be a
	 * piece of the WSDL url listed above.
	 *
	**/

	public $wsdl;
	public $guid;
	public $pw;
	public $servername;
	public $client;
	public $params;

	function __construct($wsdl, $guid, $pw, $servername, $params) {
		$this->wsdl = "https://church.example.com/ministryplatform/public/";
		$this->guid = "";
		$this->pw = "";
		$this->servername = "church.example.net";
		$this->params = array(
			'trace'				=> true,
			'exceptions'		=> 1
		);
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
			echo $soap_error->faultstring;
		}

		$request = $this->client->__soapCall($fn, array('parameters' => $parameters));
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
		return $request;
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

		$request = $this->API_Call('ExecuteStoredProcedure', $fields);
		$response = simplexml_load_string($response->ExecuteStoredProcedureResult->any->NewDataSet);
		return $response;
		unset($response);
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

		$request = $this->API_Call('AddRecord', array('parameters' => $params));
		$response = $request->AddRecordResult;
		$response = explode("|",$response); // separates the pipe delimited response string into an array
		return $response[0]; // new record ID
		unset($response);
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

		$request = $this->API_Call('UpdateRecord', array('parameters' => $params));
		$response = $request->UpdateRecordResult;
		$response = explode("|",$response); // separates the pipe delimited response string into an array
		return $response[0]; // -1 means the record was updated successfully
		unset($response);
		unset($request);
	}
}

/* no ending ?> on purpose */