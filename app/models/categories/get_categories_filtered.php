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
		$query = 'SELECT * FROM categories' .
		'         WHERE caption LIKE :caption' .
		'         OR item_link LIKE :item_link' .
		'         ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':caption', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':item_link', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
