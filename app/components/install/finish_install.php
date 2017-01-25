<?php

include dirname(__FILE__) . '/../../db/config.php';

$delete_result = false;

if (file_exists(INSTALL_SCRIPT))
{
	// usuwa skrypt instalacyjny z dysku serwera:
	$delete_result = unlink(INSTALL_SCRIPT);
}

$result = array('install_script' => INSTALL_SCRIPT, 'delete_result' => $delete_result);
echo json_encode($result);

?>
