<?php
/********************************************************************
 * UCS Director Catalog Re-skin
 * Copyright (c) 2015 Cisco Systems and Matt Day
 *
 * This file is licensed under the MIT license. See LICENSE for more
 * information.
 *
 * smarty.php
 * This file contains all the smarty template engine configuration
 * and functions
 *******************************************************************/


include_once 'config.php';
$smarty = '';

function get_smarty() {
	$smarty = $GLOBALS['smarty'];
	if ($smarty == '') {
		$smarty = new Smarty();
		$smarty->setTemplateDir('templates');
		$smarty->setCompileDir('templates_c');
		$smarty->setCacheDir('cache');
		$smarty->setConfigDir('configs');
		$GLOBALS['smarty'] = $smarty;
		# Add some other things
		if (isset($_SESSION['_ucsd_username'])) {
			$smarty->assign('username', $_SESSION['_ucsd_username']);
		}
		else {
			$smarty->assign('username', 'not logged in');
		}
	}
	return $smarty;
}

?>
