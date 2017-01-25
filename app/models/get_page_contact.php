<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';

$result = array();

$dbc = connect();

try
{
	if (check_access($dbc)) // if user rights are sufficient, get database content
	{
		$query = 'SELECT * FROM pages WHERE contact_page = 1 AND visible = 1 ORDER BY id DESC LIMIT 1';

		$statement = $dbc->prepare($query);

		$statement->execute();

		$result = $statement->fetch(PDO::FETCH_ASSOC);

		$result['main_page'] = $result['main_page'] ? true : false;
		$result['contact_page'] = $result['contact_page'] ? true : false;
		$result['visible'] = $result['visible'] ? true : false;
		$result['category_id'] = intval($result['category_id']);
	}
}
catch (PDOException $e)
{
	$result = array(
		'id' => 2, 
		'main_page' => 0, 
		'contact_page' => 1, 
		'category_id' => 0, 
		'title' => 'Angular CMS - Contact', 
		'description' => 'Angular CMS - Contact Page',
		'contents' => '<h1>Angular CMS - Contact</h1>',
		'author_id' => 1,
		'visible' => 1,
		'modified' => '2001-01-01 01:01:01',
	);
}

echo json_encode($result);

?>
