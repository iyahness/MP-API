<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['wsdl'] = "https://******/ministryplatform/public/api.asmx?WSDL";
$config['servername'] = "******";
$config['guid'] = "******";
$config['pw'] = "******";
$config['params'] = array(
    'trace'				=> true
    ,'exceptions'		=> 1
);

/* custom configuration options */

$config['portal_path'] = "https://" . $config['servername'] . "/portal/";
$config['Default_Contact_Status_ID'] = 1;
$config['User_Account_From_Email'] = "******";

/* Wufoo Integration */

$config['WufooHandshake'] = '';

$config['DefaultContactID'] 	= 0; // default contact
$config['DefaultParticipantID']	= 0; // participant ID of default contact

// Participant Config values
$config['DefaultParticipantTypeID'] = 0; // new from web
$config['DefaultCongregationID'] = 0; // North Campus

// Group Participant Config values
$config['GroupRoleID'] = 0; // class attendee

// Event Participant Config values
$config['DefaultParticipationStatusID'] = 0; // registered

/* End Wufoo */


/* End of file ministryplatform.php */
/* Location: ./application/config/ministryplatform.php */