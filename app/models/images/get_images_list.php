<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	$query = 'SELECT images.*, users.login AS owner_name FROM images' . 
	'         INNER JOIN users ON users.id = images.owner_id' .
	'         ORDER BY images.id' .
	'         LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
