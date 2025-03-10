<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/home/yoenjunkim/vendor/autoload.php'; // Adjust based on your installation method
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

session_start();
$request = array();
$request['type'] = 'get_email';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
$email = $response;

$request = array();
$request['type'] = 'get_top_movie';
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
$movie = $response;

$mail = new PHPMailer(true); // Enable exceptions

// SMTP Configuration
$mail->isSMTP();
$mail->Host = 'live.smtp.mailtrap.io'; // Your SMTP server
$mail->SMTPAuth = true;
$mail->Username = 'api'; // Your Mailtrap username
$mail->Password = 'da51fc5f3d51d427394784a673e63162'; // Your Mailtrap password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// Sender and recipient settings
$mail->setFrom('hello@demomailtrap.com', 'Admin');
$mail->addAddress($email, 'Recipient');

// Sending plain text email
$mail->isHTML(false); // Set email format to plain text
$mail->Subject = 'Here is the top movie!!!';
$mail->Body    = 'Have a look: https://www.themoviedb.org/movie/' . $movie;

// Send the email
if(!$mail->send()){
    echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
    header('Location: browse.php');
    exit;
}


?>