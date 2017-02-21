<?php

include dirname(__FILE__) . '/../../db/connection.php';

$result = array();

$dbc = connect();

$mode = intval($_GET['mode']);

try
{
	if ($mode == 0) // wszystkie
	{
		$query = 'SELECT COUNT(*) AS counter FROM messages';
	}
	if ($mode == 1) // oczekujące
	{
		$query = 'SELECT COUNT(*) AS counter FROM messages WHERE requested = 1';
	}
	if ($mode == 2) // zatwierdzone
	{
		$query = 'SELECT COUNT(*) AS counter FROM messages WHERE requested = 0';
	}

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	$result['counter'] = intval($result['counter']);
}
catch (PDOException $e)
{
}

echo json_encode($result);

?>
