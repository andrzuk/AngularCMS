<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	// ustalamy kategorię dla danej strony:

	$query = 'SELECT category_id FROM pages WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$row = $statement->fetch(PDO::FETCH_ASSOC);

	$category_id = $row['category_id'];

	// ustalamy wszystkie strony dla znalezionej kategorii:

	if ($category_id)
	{
		$query = 'SELECT id, title FROM pages WHERE category_id = :category_id AND visible = 1 ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':category_id', $category_id, PDO::PARAM_INT); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
