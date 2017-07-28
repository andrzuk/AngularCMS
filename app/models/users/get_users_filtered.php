<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

define ('ADMIN_ROLE', 1);

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$author = intval($_GET['author']);
	$search = trim($_GET['search']);
	$search_mask = '%'. $search .'%';
	
	if (!empty($search))
	{
		// check author status - if admin then can get all users list, else only his own:

		$query = 'SELECT role FROM users WHERE id = :id';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':id', $author, PDO::PARAM_INT); 

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$restrict = $result['role'] != ADMIN_ROLE ? ' id = '. $author .' AND ' : NULL;
		
		$query = 'SELECT * FROM users' .
		'         WHERE ' . $restrict . 
		'         (login LIKE :login OR email LIKE :email)' .
		'         ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':login', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':email', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
