<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/token.php';

$dbc = connect();

$token = get_token();

$result = array();

$profile = array(1 => 'admin', 2 => 'operator', 3 => 'user');

try
{
	$query = 'SELECT * FROM users WHERE token = :token';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':token', $token, PDO::PARAM_STR);

	$statement->execute();

	$row_item = $statement->fetch(PDO::FETCH_ASSOC);

	if (is_array($row_item))
	{
		$result = array(
			'id' => $row_item['id'],
			'name' => $row_item['login'],
			'role' => $profile[$row_item['role']],
			'email' => $row_item['email'],
			'logged_in' => $row_item['logged_in'],
			'isLoggedIn' => intval($row_item['id']) > 0 ? true : false,
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
	);
}

echo json_encode($result);

?>
