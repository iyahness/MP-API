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

$this->wsdl = "https://ministryplatform.example.com/ministryplatform/public/api.asmx?WSDL";
$this->guid = "";
$this->pw = "";
$this->servername = "ministryplatform.example.com";
$this->params = array(
    'trace'				=> true,
    'exceptions'		=> 1
);

/* no ending ?> on purpose */