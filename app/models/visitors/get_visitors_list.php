<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$rows = intval($_GET['rows']);
	$page = intval($_GET['page']) - 1;
	$start = $page * $rows;

	$visitors_period = get_setting_value($dbc, 'visitors_period');
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');
	$visitors_referer = get_setting_value($dbc, 'visitors_referer');
	$visitors_referer = '%' . $visitors_referer . '%';

	$date_period = date("Y-m-d", strtotime($visitors_period));

	$query = 'SELECT * FROM visitors' . 
	'         WHERE visited > :visited AND http_referer LIKE :referer AND visitor_ip NOT IN ('. $visitors_excluded .')' .
	'         ORDER BY id DESC LIMIT '. $start .', '. $rows;

	$statement = $dbc->prepare($query);

	$statement->bindValue(':visited', $date_period, PDO::PARAM_STR);
	$statement->bindValue(':referer', $visitors_referer, PDO::PARAM_STR);

	$statement->execute();

	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($result);

?>
