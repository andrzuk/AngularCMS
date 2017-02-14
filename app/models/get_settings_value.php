<?php

include dirname(__FILE__) . '/../db/connection.php';

$result = array();

$dbc = connect();

try
{
	$key_name = isset($_GET['key']) ? $_GET['key'] : NULL;
	
	$query = 'SELECT key_value AS value FROM settings WHERE key_name = :key_name';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':key_name', $key_name, PDO::PARAM_STR);

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
}
catch (PDOException $e)
{
}

echo json_encode($result);

?>
