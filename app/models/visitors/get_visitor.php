<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT visitors.*, hosts.server_name AS host_name' .
	'         FROM visitors' .
	'         INNER JOIN hosts ON hosts.server_ip = visitors.visitor_ip' .
	'         WHERE visitors.id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
