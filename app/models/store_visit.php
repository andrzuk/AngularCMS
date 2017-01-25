<?php

include dirname(__FILE__) . '/../db/connection.php';

$ip = $_SERVER['REMOTE_ADDR'];
$ref = $_POST['referer'];
$uri = $_POST['url'];

$db_connection = connect();

try
{
	$query = 	'INSERT INTO visitors' .
				' (visitor_ip, http_referer, request_uri, visited) VALUES' .
				' (:visitor_ip, :http_referer, :request_uri, NOW())';

	$statement = $db_connection->prepare($query);

	$statement->bindValue(':visitor_ip', $ip, PDO::PARAM_STR); 
	$statement->bindValue(':http_referer', $ref, PDO::PARAM_STR); 
	$statement->bindValue(':request_uri', $uri, PDO::PARAM_STR); 

	$statement->execute();
}
catch (PDOException $e)
{
}

?>

