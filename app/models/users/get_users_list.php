<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

define ('ADMIN_ROLE', 1);

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$author = intval($_GET['author']);
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	// check author status - if admin then can get all users list, else only his own:

	$query = 'SELECT role FROM users WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $author, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$restrict = $result['role'] != ADMIN_ROLE ? ' WHERE id = '. $author : NULL;

	$query = 'SELECT * FROM users '. $restrict .' ORDER BY id LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
