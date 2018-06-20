<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$mode = intval($_GET['mode']);
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

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

	$query = 'SELECT * FROM logins' . 
	'         WHERE user_ip NOT IN ('. $visitors_excluded .')' . $condition .
	'         ORDER BY id DESC LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
