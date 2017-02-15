<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	$query = 'SELECT *, resource, description FROM access_rights' .
	'         INNER JOIN access_levels ON access_levels.id = access_rights.resource_id' .
	'         WHERE user_id = :user_id' .
	'         ORDER BY access_rights.id LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
