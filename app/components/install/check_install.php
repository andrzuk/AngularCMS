<?php

include dirname(__FILE__) . '/../../db/config.php';

$file_found = false;

if (file_exists(INSTALL_SCRIPT)) $file_found = true;

$result = array('install_script' => INSTALL_SCRIPT, 'install_found' => $file_found);
echo json_encode($result);

?>
