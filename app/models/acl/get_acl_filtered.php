<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$search = trim($_GET['search']);
	$search_mask = '%'. $search .'%';
	
	if (!empty($search))
	{
		$query = 'SELECT * FROM access_levels' .
		'         WHERE resource LIKE :resource' .
		'         OR description LIKE :description' .
		'         ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':resource', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':description', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
