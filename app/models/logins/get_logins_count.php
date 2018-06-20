<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

$mode = intval($_GET['mode']);

$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

try
{
	if ($mode == 0) // wszystkie
	{
		$condition = NULL;
	}
	if ($mode == 1) // nieudane
	{
		$condition = ' AND user_id = 0';
	}
	if ($mode == 2) // udane
	{
		$condition = ' AND user_id > 0';
	}

	$query = 'SELECT COUNT(*) AS counter FROM logins' . 
	'         WHERE user_ip NOT IN ('. $visitors_excluded .')' . $condition;

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	$result['counter'] = intval($result['counter']);
}
catch (PDOException $e)
{
}

echo json_encode($result);

?>
