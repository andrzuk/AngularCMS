<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/access.php';
include dirname(__FILE__) . '/../db/setting.php';

$result = array();

$dbc = connect();

$index_enabled = 'sidebar_index_enabled';
$page_enabled = 'sidebar_page_enabled';
$category_enabled = 'sidebar_category_enabled';
$contact_enabled = 'sidebar_contact_enabled';
$search_enabled = 'sidebar_search_enabled';

try
{
	$query = 'SELECT * FROM settings WHERE key_name IN (:index_enabled, :page_enabled, :category_enabled, :contact_enabled, :search_enabled)';

	$statement = $dbc->prepare($query);

	$statement->bindParam(':index_enabled', $index_enabled, PDO::PARAM_STR);
	$statement->bindParam(':page_enabled', $page_enabled, PDO::PARAM_STR);
	$statement->bindParam(':category_enabled', $category_enabled, PDO::PARAM_STR);
	$statement->bindParam(':contact_enabled', $contact_enabled, PDO::PARAM_STR);
	$statement->bindParam(':search_enabled', $search_enabled, PDO::PARAM_STR);

	$statement->execute();

	$context_list = $statement->fetchAll(PDO::FETCH_ASSOC);

	$result['context_list'] = $context_list;

}
catch (PDOException $e)
{
	$result = array('context_list' => NULL);
}

echo json_encode($result);

?>
