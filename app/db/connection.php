<?php

header("Access-Control-Allow-Origin: *");

define ('DB_HOST', '');
define ('DB_NAME', '');
define ('DB_USER', '');
define ('DB_PASS', '');

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
