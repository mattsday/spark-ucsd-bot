<?php
/********************************************************************
 * UCS Director Catalog Re-skin
 * Copyright (c) 2015 Cisco Systems and Matt Day
 *
 * This file is licensed under the MIT license. See LICENSE for more
 * information.
 *
 * ucsd_api.php
 * This file contains various common tasks such as API calls and
 * UI elements to interact with UCS Director
 *******************************************************************/

# Config file:
require_once('config.php');

# Sends a request as the admin user (from config.php)
function ucsd_api_call_admin ($opName, $opData) {
	return ucsd_api_call_url_admin('http://'.$GLOBALS['ucsd_ip'].'/app/api/rest?opName='.
		urlencode($opName).'&opData='.urlencode($opData), $GLOBALS['ucsd_api_key']);
}

# Sends a request to UCS director with the given API key
function ucsd_api_call_url_admin ($url, $api_key) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	# Set options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	# Set headers
	curl_setopt($ch, CURLOPT_HTTPHEADER, [ "X-Cloupia-Request-Key: ".$api_key,]);
	return json_decode(curl_exec($ch));
}
?>
