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
	
	$visitors_excluded = get_setting_value($dbc, 'visitors_excluded');
	
	if (!empty($search))
	{
		$query = 'SELECT * FROM searches' . 
		'         WHERE user_ip NOT IN ('. $visitors_excluded .')' .
		'         AND (search_text LIKE :search_text OR user_ip LIKE :user_ip)' .
		'         ORDER BY id DESC';

		$statement = $dbc->prepare($query);

		$statement->bindValue(':search_text', $search_mask, PDO::PARAM_STR);
		$statement->bindValue(':user_ip', $search_mask, PDO::PARAM_STR);

		$statement->execute();

		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

echo json_encode($result);

?>
