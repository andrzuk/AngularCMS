<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);

	$query = 'SELECT * FROM pages WHERE id = :id';

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT); 

	$statement->execute();

	$result = $statement->fetch(PDO::FETCH_ASSOC);

	$result['main_page'] = $result['main_page'] ? true : false;
	$result['contact_page'] = $result['contact_page'] ? true : false;
	$result['visible'] = $result['visible'] ? true : false;
	$result['category_id'] = $result['category_id'];
}

echo json_encode($result);

?>
