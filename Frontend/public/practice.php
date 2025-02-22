<?php
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . "/../rabbitmq/testRabbitMQClient.php"); ?>

<form action="practice.php" method="POST">
        <label for="session_id">Enter Session ID:</label>
        <input type="text" id="session_id" name="session_id" required>
        <button type="submit">Submit</button>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["session_id"])) {
    $session_id = $_POST["session_id"]; 
    $request = array();
    $request['type'] = 'getUserID';
    $request['session_id'] = $session_id;
    try {
    
        $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
        

        $response = $client->send_request($request);

        
        if (!empty($response) && is_string($response)) {
            
            echo "User ID: " . htmlspecialchars($response);  
        } else {
            echo "Failed to retrieve user ID. Response: " . var_export($response, true);
        }
    } catch (Exception $e) {
        
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request. Please submit a session ID.";
}
?>


