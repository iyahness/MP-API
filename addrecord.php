<?php

//print_r($_POST);

require_once('lib/mp/MP_API.php');

$API = new MP_API();

$userID = 0; // anonymous
$table = "Contacts";
$pk = "Contact_ID";

$request = $API->AddRecord($userID, $table, $pk, $_POST);
$data = $request->AddRecordResult;
$data = explode("|",$data); // separates the pipe delimited response string into an array

if ($data[0] > 0) { // success; ID of 0 means no new record created
	$response['success'] = true;
	$response['message'] = "Successfully created a record with ID of " . $data[0];
}

else { // failed

	$response['success'] = false;
	$response['message'] = "Your record was not created.";
}

$response = json_encode($response);
print_r($response);


/* no ending ?> on purpose */
