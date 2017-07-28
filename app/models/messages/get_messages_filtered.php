<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$search = trim($_GET['search']);
	$search_mask = '%'. $search .'%';
	
	if (!empty($search))
	{
		$query = 'SELECT * FROM messages' .
		'         WHERE client_ip LIKE :client_ip' .
		'         OR client_name LIKE :client_name' .
		'         OR client_email LIKE :client_email' .
		'         OR message_content LIKE :message_content' .
		'         ORDER BY id';

		$statement = $dbc->prepare($query);

		$statement->bindParam(':client_ip', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':client_name', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':client_email', $search_mask, PDO::PARAM_STR); 
		$statement->bindParam(':message_content', $search_mask, PDO::PARAM_STR); 

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
