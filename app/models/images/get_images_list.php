<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$id = intval($_GET['id']);
	$packet = get_setting_value($dbc, 'gallery_list_packet');

	$query = 'SELECT images.*, users.login AS owner_name FROM images' . 
	'         INNER JOIN users ON users.id = images.owner_id' .
	'         WHERE images.id > :id' .
	'         ORDER BY images.id' .
	'         LIMIT ' . $packet;

	$statement = $dbc->prepare($query);

	$statement->bindValue(':id', $id, PDO::PARAM_INT);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
