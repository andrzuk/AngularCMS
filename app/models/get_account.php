<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/token.php';

$dbc = connect();

$token = get_token();

$result = array();
$access_table = array();

$profile = array(1 => 'admin', 2 => 'operator', 3 => 'user');

try
{
	$query = 'SELECT * FROM users WHERE token = :token';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':token', $token, PDO::PARAM_STR);

	$statement->execute();

	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	$query = 'SELECT module, access FROM access_users' .
	'         INNER JOIN access_modules ON access_modules.id = access_users.module_id' .
	'         WHERE user_id = :user_id';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':user_id', $row_item['id'], PDO::PARAM_INT);

	$statement->execute();

	$data_table = $statement->fetchAll(PDO::FETCH_ASSOC);
    
	foreach ($data_table as $key => $value)
	{
		$access_table[$value['module']] = $value['access'] ? true : false;
	}

	if (is_array($row_item))
	{
		$result = array(
			'id' => $row_item['id'],
			'name' => $row_item['login'],
			'role' => $profile[$row_item['role']],
			'email' => $row_item['email'],
			'logged_in' => $row_item['logged_in'],
			'isLoggedIn' => intval($row_item['id']) > 0 ? true : false,
			'access_table' => $access_table,
		);
	}    
}
catch (PDOException $e)
{
	$result = array(
		'id' => 0,
		'name' => NULL,
		'role' => NULL,
		'email' => NULL,
		'logged_in' => 0,
		'isLoggedIn' => false,
		'access_table' => $access_table,
	);
}

echo json_encode($result);

?>
