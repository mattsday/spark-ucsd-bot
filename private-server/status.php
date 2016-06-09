<?php
include 'api/ucsd_api.php';
include 'api/smarty.php';

# UCS Director uses magic numbers for status, so let's add them here:
$status[0] = "Not started"; $style[0] = "not_started";
$status[1] = "In Progress"; $style[1] = "in_progress";
$status[2] = "Failed"; $style[2] = "failed";
$status[3] = "Completed"; $style[3] = "completed";
$status[4] = "Completed with Warning"; $style[4] = "completed_warn";
$status[5] = "Cancelled"; $style[5] = "cancelled";
$status[6] = "Paused"; $style[6] = "paused";
$status[7] = "Skipped"; $style[7] = "skipped";

$ram = $_GET['r'];
$cpu = $_GET['c'];
$os = $_GET['o'];
$name = $_GET['n'];
#$started = $_GET['as'];

# Initialise template engine:
$smarty = get_smarty();
$smarty->assign('request', $_GET['s']);

# Send the request:
$response = ucsd_api_call_admin('userAPIGetServiceRequestWorkFlow', '{param0:'.$_GET['s'].'}');

//print_r($response);
//exit();
# Build up a variable array for templating engine - one index per status line
$i = 0;
$anchor = false;
foreach ($response->{'serviceResult'}->{'entries'} as $entry) {
	$steps[$i]['Name'] = $entry->{'stepId'};
	$steps[$i]['Status'] = $status[$entry->{'executionStatus'}];
	$steps[$i]['Style'] = $style[$entry->{'executionStatus'}];
	$steps[$i]['Number'] = $i + 1;
	if ($entry->{'executionStatus'} == 1) {
		$anchor = true;
		$steps[$i]['Anchor'] = 'current';
	}
	else if ((count($response->{'serviceResult'}->{'entries'}) == $i + 1) && ($anchor == false)) {
		# If it's not started stick us back at the top, else go to the bottom:
		if ($entry->{'executionStatus'} == 0) {
			$steps[0]['Anchor'] = 'current';
		}
		else {
			$steps[$i]['Anchor'] = 'current';
		}
	}
	else {
		$steps[$i]['Anchor'] = 'step'.$i;
	}
	$i++;
}
$smarty->assign('steps', $steps);
$smarty->assign('os', $os);
$smarty->assign('cores', $cpu);
$smarty->assign('ram', $ram / 1024);
$smarty->assign('name', $name);

# Output to template engine:
if ($_GET['rb'] == 't') {
	$smarty->display('rollback.tpl');
}
else {
	$smarty->display('req.tpl');
}
?>
