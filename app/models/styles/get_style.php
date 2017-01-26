<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$styles_file_name = CSS_DIR . 'app.css';

	// wczytuje plik z serwera:
	
	if (file_exists($styles_file_name))
	{
		$styles_data = file_get_contents($styles_file_name);
	}

	$result = array('file_contents' => $styles_data);
}

echo json_encode($result);

?>
