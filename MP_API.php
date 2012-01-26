<?php

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


class MP_API {

/**
 * API Methods for MinistryPlatform
 */

	include_once("mp_config.php");
	public $client;
	public $context = stream_context_create(array('http' => array('header' => "Connection: close")));
	public $params = array(
		'trace'				=> true,
		'exceptions'		=> 1,
		'stream_context'	=> $context;
	);


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


	function __construct($wsdl, $guid, $pw, $servername, $context, $params) {
		$this->wsdl = $wsdl;
		$this->guid = $guid;
		$this->pw = $pw;
		$this->servername = $servername;
		$this->context = $context;
		$this->params = $params;
	}

	private function ConvertToString($array) {
		$temp = new array();
		foreach($array as $k=>$v) {
			$temp[] .= "$k=$v";
		}
		$string = implode( "&", $temp);
		return $string;
	}


	function API_Call($fn, array $parameters) {
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

	function MP_ExecuteSP($sp, array $request) {

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

	function MP_AddRecord($userID, $table, $pk, $fields) {

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
}
?>