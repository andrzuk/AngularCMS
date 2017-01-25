<?php

include dirname(__FILE__) . '/../db/connection.php';
include dirname(__FILE__) . '/../db/setting.php';
include dirname(__FILE__) . '/../db/config.php';

$form_data = $_POST;

$message = NULL;
$success = false;

if (!empty($form_data['login']) && !empty($form_data['email']) && !empty($form_data['content'])) 
{
	$client_ip = $_SERVER['REMOTE_ADDR'];
	$client_name = $form_data['login'];
	$client_email = $form_data['email'];
	$message_content = $form_data['content'];
	
	$db_connection = connect();

	$query = ' INSERT INTO messages (client_ip, client_name, client_email, message_content, requested, send_date, close_date)' .
	'          VALUES (:client_ip, :client_name, :client_email, :message_content, 1, NOW(), NOW())';

	$statement = $db_connection->prepare($query);

	$statement->bindParam(':client_ip', $client_ip, PDO::PARAM_STR);
	$statement->bindParam(':client_name', $client_name, PDO::PARAM_STR);
	$statement->bindParam(':client_email', $client_email, PDO::PARAM_STR);
	$statement->bindParam(':message_content', $message_content, PDO::PARAM_STR);

	$statement->execute();

	$send_new_message_report = get_setting_value($db_connection, 'send_new_message_report');
	$email_host = get_setting_value($db_connection, 'email_host');
	$email_port = get_setting_value($db_connection, 'email_port');
	$email_password = get_setting_value($db_connection, 'email_password');
	$email_sender_name = get_setting_value($db_connection, 'email_sender_name');
	$email_sender_address = get_setting_value($db_connection, 'email_sender_address');
	$email_report_subject = get_setting_value($db_connection, 'email_report_subject');
	$email_report_address = get_setting_value($db_connection, 'email_report_address');
	$email_admin_name = get_setting_value($db_connection, 'email_admin_name');
	$email_report_body = get_setting_value($db_connection, 'email_report_body');
	$app_brand = get_setting_value($db_connection, 'app_brand');
	$app_domain = get_setting_value($db_connection, 'app_domain');

	$mail_body = $email_report_body ."<br><br>Użytkownik <b>". $client_name ."</b> (e-mail: <b>". $client_email ."</b>) napisał do serwisu wiadomość:<br><br>\"". $message_content ."\"<br><br>". $app_brand ."<br>". $app_domain ."<br>";

	if ($send_new_message_report == 'true')
	{
		include MAILER_DIR . 'class.phpmailer.php';
		include MAILER_DIR . 'class.smtp.php';

		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->SMTPDebug = 0;
		$mail->SMTPAuth = true;
		$mail->Host = $email_host;
		$mail->Port = $email_port;
		$mail->Username = $email_sender_address;
		$mail->Password = $email_password;
		$mail->Subject = $email_report_subject;
		$mail->Body = $mail_body;
		$mail->AltBody = $mail_body;
		$mail->CharSet = "UTF-8";
		$mail->SetFrom($email_sender_address, $email_sender_name);
		$mail->AddAddress($email_report_address, $email_admin_name);
		$mail->Send();
	}

	$message = 'Wiadomość została pomyślnie wysłana.';
	$success = true;
}
else
{
	$message = 'Nie wprowadzono wymaganych danych.';
	$success = false;
}

$result = array('success' => $success, 'message' => $message);

echo json_encode($result);

?>
