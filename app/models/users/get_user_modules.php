<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT *, module, description FROM access_users' .
	'         INNER JOIN access_modules ON access_modules.id = access_users.module_id' .
	'         WHERE user_id = :user_id' .
	'         ORDER BY access_users.module_id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
