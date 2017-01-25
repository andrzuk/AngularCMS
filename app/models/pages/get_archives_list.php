<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT id, modified AS label FROM archives WHERE page_id = :page_id ORDER BY id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':page_id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
