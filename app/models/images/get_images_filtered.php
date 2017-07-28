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
		$query = 'SELECT images.*, users.login AS owner_name FROM images' . 
		'         INNER JOIN users ON users.id = images.owner_id' .
		'         WHERE file_name LIKE :file_name' .
		'         ORDER BY images.id';

		$statement = $dbc->prepare($query);
		
		$statement->bindParam(':file_name', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
