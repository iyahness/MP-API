<?php

require_once('lib/mp/MP_API.php');

$API = new MP_API();

$user = $_POST['username'];
$userpassword = $_POST['password'];

$request = $API->authenticate_user($user, $userpassword);

$userID = $request->UserID;
$displayname = $request->DisplayName;

if ($userID > 0) { // success
	$response['success'] = true;
	$response['message'] = "Successfully authenticated $displayname.";
}

else { // failed

	$response['success'] = false;
	$response['message'] = "Your username or password was not recognized.";
}

$response = json_encode($response);
print_r($response);

/* no ending ?> on purpose */