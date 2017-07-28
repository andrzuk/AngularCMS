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
		$query = 'SELECT * FROM settings' .
		'         WHERE key_name LIKE :key_name' .
		'         OR key_value LIKE :key_value' .
		'         OR meaning LIKE :meaning' .
		'         ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':key_name', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':key_value', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':meaning', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
