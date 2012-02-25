<?php

require_once('lib/mp/MP_API.php');

$API = new MP_API();

$userID = 0; // anonymous
$table = "Contacts";
$pk = "Contact_ID";

$request = $API->AddRecord($userID, $table, $pk, $_POST);

echo "The new Contact's ID is <b>" . $request. "</b>";

/* no ending ?> on purpose */
