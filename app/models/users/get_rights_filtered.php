<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);
	$mode = intval($_GET['mode']);
	$search = trim($_GET['search']);
	$search_mask = '%'. $search .'%';

	if (!empty($search) || $mode > 0)
	{
		if ($mode == 0) // wszystkie
		{
			$query = 'SELECT *, resource, description FROM access_rights' .
			'         INNER JOIN access_levels ON access_levels.id = access_rights.resource_id' .
			'         WHERE (resource LIKE :resource OR description LIKE :description)' .
			'         AND user_id = :user_id' .
			'         ORDER BY access_rights.id';
		}
		if ($mode == 1) // aktywne
		{
			$query = 'SELECT *, resource, description FROM access_rights' .
			'         INNER JOIN access_levels ON access_levels.id = access_rights.resource_id' .
			'         WHERE (resource LIKE :resource OR description LIKE :description)' .
			'         AND user_id = :user_id AND access = 1' .
			'         ORDER BY access_rights.id';
		}
		if ($mode == 2) // nieaktywne
		{
			$query = 'SELECT *, resource, description FROM access_rights' .
			'         INNER JOIN access_levels ON access_levels.id = access_rights.resource_id' .
			'         WHERE (resource LIKE :resource OR description LIKE :description)' .
			'         AND user_id = :user_id AND access = 0' .
			'         ORDER BY access_rights.id';
		}

		$statement = $dbc->prepare($query);

		$statement->bindValue(':user_id', $id, PDO::PARAM_INT); 
		$statement->bindParam(':resource', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':description', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
