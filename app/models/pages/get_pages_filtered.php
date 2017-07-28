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
		$query = 'SELECT pages.*, categories.caption AS category_name, users.login AS author_name FROM pages' .
		'         LEFT JOIN categories ON categories.id = pages.category_id' .
		'         INNER JOIN users ON users.id = pages.author_id' .
		'         WHERE title LIKE :title' .
		'         OR caption LIKE :caption' .
		'         OR description LIKE :description' .
		'         OR contents LIKE :contents' .
		'         ORDER BY pages.id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':title', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':caption', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':description', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':contents', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
