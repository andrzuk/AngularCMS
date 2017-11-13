<?php

include dirname(__FILE__) . '/../../db/connection.php';
include dirname(__FILE__) . '/../../db/access.php';
include dirname(__FILE__) . '/../../db/setting.php';

$result = array();

$dbc = connect();

if (check_access($dbc)) // if user rights are sufficient, get database content
{
	$search = trim($_GET['search']);
	$search_mask = '%'. $search .'%';
	
	$visitors_period = get_setting_value($dbc, 'visitors_period');
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');
	$visitors_referer = get_setting_value($dbc, 'visitors_referer');
	$visitors_referer = '%' . $visitors_referer . '%';

	$date_period = date("Y-m-d", strtotime($visitors_period));

	if (!empty($search))
	{
		$query = 'SELECT visitors.*, hosts.server_name AS host_name FROM visitors' . 
		'         INNER JOIN hosts ON hosts.server_ip = visitors.visitor_ip' .
		'         WHERE visited > :visited AND http_referer LIKE :referer AND visitor_ip NOT IN ('. $visitors_excluded .')' .
		'         AND (visitor_ip LIKE :visitor_ip OR http_referer LIKE :http_referer OR request_uri LIKE :request_uri OR visited LIKE :visitor_date OR server_name LIKE :server_name)' .
		'         ORDER BY id DESC';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':visited', $date_period, PDO::PARAM_STR);
		$statement->bindValue(':referer', $visitors_referer, PDO::PARAM_STR);
		$statement->bindValue(':visitor_ip', $search_mask, PDO::PARAM_STR);
		$statement->bindValue(':http_referer', $search_mask, PDO::PARAM_STR);
		$statement->bindValue(':request_uri', $search_mask, PDO::PARAM_STR);
		$statement->bindValue(':visitor_date', $search_mask, PDO::PARAM_STR);
		$statement->bindValue(':server_name', $search_mask, PDO::PARAM_STR);

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
