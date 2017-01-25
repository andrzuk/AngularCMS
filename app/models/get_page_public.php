<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT COUNT(*) AS counter FROM pages' .
	'         WHERE id = :id AND visible = 1';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':id', $id, PDO::PARAM_INT);
	
	$statement->execute();
	
	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	if (is_array($row_item))
	{
		if ($row_item['counter'] == 0)
		{
			$result['type'] = 'empty';
			$result['label'] = 'Nie znaleziono';
		}
		if ($row_item['counter'] == 1)
		{
			$query = 'SELECT pages.*, categories.caption AS category_name, users.login AS author_name FROM pages ' .
			'         LEFT JOIN categories ON categories.id = pages.category_id' .
			'         INNER JOIN users ON users.id = pages.author_id' .
			'         WHERE pages.id = :id AND pages.visible = 1';

			$statement = $dbc->prepare($query);

			$statement->bindValue(':id', $id, PDO::PARAM_INT); 

			$statement->execute();

			$result = $statement->fetch(PDO::FETCH_ASSOC);

			$result['main_page'] = $result['main_page'] ? true : false;
			$result['contact_page'] = $result['contact_page'] ? true : false;
			$result['visible'] = $result['visible'] ? true : false;
			$result['category_id'] = intval($result['category_id']);

			$result['type'] = 'item';
			$result['label'] = !empty($result['category_name']) ? $result['category_name'] : 'System';
		}
	}
}

echo json_encode($result);

?>
