<?php

header("Access-Control-Allow-Origin: *");

include dirname(__FILE__) . '/../../config/config.php';

function connect()
{
	try
	{
		$connection = new PDO(
			'mysql:host='.DB_HOST.';dbname='.DB_NAME.';', DB_USER, DB_PASS
		);
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection->exec('set names utf8');
		
		return $connection;
	}
	catch (PDOException $e)
	{
		die ($e->getMessage());
	}
}

?>
