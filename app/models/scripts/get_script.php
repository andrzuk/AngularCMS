<?php

include dirname(__FILE__) . '/../../db/config.php';
include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$scripts_file_name = JS_DIR . 'app.js';

	// wczytuje plik z serwera:
	
	if (file_exists($scripts_file_name))
	{
		$scripts_data = file_get_contents($scripts_file_name);
	}

	$result = array('file_contents' => $scripts_data);
}

echo json_encode($result);

?>
