<?php
session_start();


include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');



$responseMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['phone'])) {
        $user_id = $_POST['user_id'];
        $phone = $_POST['phone'];

        $client = new RabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");

        $request = [
            'type' => 'generate_2fa',
            'user_id' => $user_id,
            'phone' => $phone
        ];

        $response = $client->send_request($request);
        $responseMessage = $response['message'] ?? 'No response received.';
    }
}
?>

<h2>Two-Factor Authentication</h2>
<form method="POST">
    <label>User ID:</label>
    <input type="text" name="user_id" required><br><br>

    <label>Phone Number:</label>
    <input type="text" name="phone" placeholder="+1234567890" required><br><br>

    <button type="submit">Send 2FA Code</button>
</form>

<?php if (!empty($responseMessage)) : ?>
    <p style="margin-top:1em; font-weight:bold;"><?php echo htmlspecialchars($responseMessage); ?></p>
<?php endif;


//$tmbd_id = '402431'; //Movie test

//$request = array();
//$request['type'] = 'get_movies';
//$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
//$response = $client->send_request($request);
//echo "Movie: $response" . PHP_EOL; //FOR GETTING MOVIES ONLY
//var_dump($response);


//$request = array();
//$request['type'] = 'get_movie_details';
//$request['movie_id'] = $tmbd_id;
//$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
//$response = $client->send_request($request);
//echo "Movie Details: $response"; //FOR GETTING MOVIE DETAILS ONLY
//var_dump($response);

echo "Testing to see if I can get Username and User ID: " . PHP_EOL;

$session_key = session_id();
echo $session_key;
?>

