<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');

	$query = 'SELECT * FROM searches' . 
	'         WHERE user_ip NOT IN ('. $visitors_excluded .')' .
	'         ORDER BY id DESC';

	$statement = $dbc->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
