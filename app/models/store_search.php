<?php

include dirname(__FILE__) . '/../db/connection.php';

$ip = $_SERVER['REMOTE_ADDR'];
$search_text = trim($_GET['search']);
$result_count = intval($_GET['count']);

if (!empty($search_text))
{
	$db_connection = connect();

	$query = 	'INSERT INTO searches' .
				' (user_ip, search_text, results, search_date) VALUES' .
				' (:user_ip, :search_text, :result_count, NOW())';

	$statement = $db_connection->prepare($query);

	$statement->bindValue(':user_ip', $ip, PDO::PARAM_STR); 
	$statement->bindValue(':search_text', $search_text, PDO::PARAM_STR); 
	$statement->bindValue(':result_count', $result_count, PDO::PARAM_INT); 

	$statement->execute();
}

?>
