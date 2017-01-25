<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$query = 'SELECT * FROM users ORDER BY id';

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
