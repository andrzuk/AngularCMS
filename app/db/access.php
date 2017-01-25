<?php

function check_access($dbc)
{
	include_once dirname(__FILE__) . '/token.php';
	
	$token = get_token();

	$result = false;
	
	// get user by token -> get role -> compare role with serviced resource:
	
	$path = explode('/', $_SERVER['REQUEST_URI']);
	$service = $path[count($path) - 1];
	$resource = str_replace('.php', NULL, $service);
	$is_query_string = stripos($resource, '?');
	if ($is_query_string !== false)
	{
		$resource = substr($resource, 0, $is_query_string);
	}
	
	if ($token) // zalogowany
	{
		$query = " SELECT access_rights.access * ( " .
				" access_levels.mask_a * roles.mask_a + " .
				" access_levels.mask_o * roles.mask_o + " .
				" access_levels.mask_u * roles.mask_u + " .
				" access_levels.mask_g * roles.mask_g ) " .
				" AS access " .
				" FROM access_levels, access_rights, roles, users " .
				" WHERE users.role = roles.id " .
				" AND access_rights.user_id = users.id" .
				" AND access_rights.resource_id = access_levels.id" .
				" AND users.token = :token " .
				" AND access_levels.resource = :resource";

		$statement = $dbc->prepare($query);

		$statement->bindValue(':token', $token, PDO::PARAM_STR); 
		$statement->bindValue(':resource', $resource, PDO::PARAM_STR); 

		$statement->execute();

		$row_item = $statement->fetch(PDO::FETCH_ASSOC);
	
		if (is_array($row_item))
			if (array_key_exists('access', $row_item))
				$result = $row_item['access'];
	}

	if (!$result) // brak dostępu do chronionych zasobów
	{
		$query = " SELECT mask_g AS access " .
				" FROM access_levels " .
				" WHERE resource = :resource";

		$statement = $dbc->prepare($query);

		$statement->bindValue(':resource', $resource, PDO::PARAM_STR); 

		$statement->execute();

		$row_item = $statement->fetch(PDO::FETCH_ASSOC);

		if (is_array($row_item))
			if (array_key_exists('access', $row_item))
				$result = $row_item['access'];
	}

	return $result;
}

function get_user_by_token($dbc)
{
	include_once dirname(__FILE__) . '/token.php';

	$token = get_token();

	$result = false;
	
	// get user by token:
	
	$query = " SELECT * FROM users WHERE token = :token ";

	$statement = $dbc->prepare($query);

	$statement->bindValue(':token', $token, PDO::PARAM_STR); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	return $result;
}

?>

