<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

try
{
	if (check_access($dbc)) // if user rights are sufficient, get database content
	{
		$query = 'SELECT * FROM categories WHERE visible = 1 ORDER BY item_order';

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}
catch (PDOException $e)
{
}

echo json_encode($result);

?>
