<?php

# Be sure to edit/create your config file:
require_once('config.php');

# UCS Director uses magic numbers for status, so let's add them here:
$status[0] = "Not started"; $style[0] = "not_started";
$status[1] = "In Progress"; $style[1] = "in_progress";
$status[2] = "Failed"; $style[2] = "failed";
$status[3] = "Completed"; $style[3] = "completed";
$status[4] = "Completed with Warning"; $style[4] = "completed_warn";
$status[5] = "Cancelled"; $style[5] = "cancelled";
$status[6] = "Paused"; $style[6] = "paused";
$status[7] = "Skipped"; $style[7] = "skipped";

// Build query
$path = '/app/api/rest?formatType=json&opName=userAPISubmitVAppServiceRequest&opData=';
$ram = $_GET['r'];
$cpu = $_GET['c'];
$os = $_GET['t'];
$catalog = 1;
// Sanity check inputs:
if (!is_numeric($ram)) {
	print "Error - RAM must be a number!";
	exit(1);
}
else {
	$ram = $ram * 1024;
	if (($ram > 16384) || ($ram < 512)) {
		print "Error - RAM must be between 0.5 and 16Gb!";
		exit(1);
	}
}
if (!is_numeric($cpu)) {
	print "Error - CPU must be a number!";
	exit(1);
}
else if (($cpu < 1) || ($cpu > 9)) {
	print "Error - CPU must be between 1 and 9";
	exit(1);
}
if ($os == '') {
	print "Error - must specify OS type!";
	exit(1);
}
else {
	$os = strtolower($os);
}
switch ($os) {
	case 'centos':
		$name = 'CentOS-VM';
		$catalog = 1;
		goto end;
	case 'debian':
		$name = 'Debian-VM';
		$catalog = 17;
		goto end;
	default:
		print "Error - OS type not recognised!";
		exit(1);
	end:
};
if (@$_GET['n'] != '') { 
	$name = $_GET['n'];
}

if ($name == '') {
	$name = $os;
}

if (!is_numeric(@$_GET['id'])) {
	print "Error - invalid request ID!";
	exit(1);
}
else {
	$id = $_GET['id'];
	$id_file = '/var/local/run/ucsd/web-id/'.$id;
	if (file_exists($id_file)) {
		$srid = implode('', file($id_file));
		$url = 'status?s='.$srid.'&c='.$cpu.'&o='.$os.'&r='.$ram.'&n='.$name.'&rb=f#current';
		header('Location: '.$url);

		exit(0);
	}
}

$query['param0'] = 'Build VM from Spark Request';
$query['param1'] = array(
	'list' => array(
		array('name'=>'Catalog', 'value'=>$catalog),
		array('name'=>'Name', 'value'=>$name),
		array('name'=>'Cores', 'value'=>$cpu),
		array('name'=>'Memory', 'value'=>$ram),
	),
);
$url = $ucsd_ip.$path.urlencode(json_encode($query));
// Get cURL resource
$ch = curl_init();
// Set url
curl_setopt($ch, CURLOPT_URL, $url);
// Set method
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// Set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// Set headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Cloupia-Request-Key: ".$ucsd_api_key,
]
);
// Send the request & save response to $resp
$resp = curl_exec($ch);
if (!$resp) {
	print "Invalid response from UCSD";
	exit(1);
}

$resp = json_decode($resp);

$id = $_GET['id'];
$id_file = '/var/local/run/ucsd/web-id/'.$id;
$fp = fopen($id_file, 'w');
fwrite($fp, $resp->{'serviceResult'});
fclose($fp);

$url = 'status?s='.$resp->{'serviceResult'}.'&c='.$cpu.'&o='.$os.'&r='.$ram.'&n='.$name.'&rb=f#current';
header('Location: '.$url);

?>
