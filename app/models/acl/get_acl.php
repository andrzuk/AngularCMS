<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT * FROM access_levels WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$result['mask_a'] = $result['mask_a'] ? true : false;
	$result['mask_o'] = $result['mask_o'] ? true : false;
	$result['mask_u'] = $result['mask_u'] ? true : false;
	$result['mask_g'] = $result['mask_g'] ? true : false;
}

echo json_encode($result);

?>
