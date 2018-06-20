<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$mode = intval($_GET['mode']);
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	if ($mode == 0) // wszystkie
	{
		$query = 'SELECT * FROM messages ORDER BY id LIMIT '. $start .', '. $rows;
	}
	if ($mode == 1) // oczekujące
	{
		$query = 'SELECT * FROM messages WHERE requested = 1 ORDER BY id LIMIT '. $start .', '. $rows;
	}
	if ($mode == 2) // zatwierdzone
	{
		$query = 'SELECT * FROM messages WHERE requested = 0 ORDER BY id LIMIT '. $start .', '. $rows;
	}

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
