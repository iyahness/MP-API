<?php

/**
 * This class extension should be used for the various Stored Procedure calls
 * that return large datasets.
 */

if (!defined('BASEPATH')) exit('No direct script access allowed'); // use for CodeIgniter

class mp_data extends mp {

	function __construct()
	{
		parent::__construct();
	}

/**
 * \\ Misc API Calls //
 */


	function GetCongregations()
	{
		$parameters = array ();
		$request = parent::ExecuteSP('api_Common_GetCongregations', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

	function GetAddressesToGeocode()
	{
		$parameters = array ();
		$request = parent::ExecuteSP('api_ken_GetAddressesToValidate', $parameters);
		return $request; // If there are errors, they will be noted in the $request->Errors node
	}

}