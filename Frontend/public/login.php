<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (is_logged_in()) {
    die(header("Location: profile.php"));
}

?>

<form action="login.php" method="POST">
    <div class="form-group">
        <div class="col-md-4">
            <label for="user">Login </label>
            <input type="text" name="user" class="form-control" id="user" placeholder="Enter Username">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
</form>

<?php 
    if (isset($_POST["user"]) && isset($_POST["password"])) {
        $user = $_POST["user"];
        $password = $_POST["password"];
        $request = array();
        $request['type'] = 'login';
        $request['user'] = $user;
        $request['password'] = $password;
        $request['session_id'] = session_id();
        $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
        $response = $client->send_request($request);
        if ($response === 'success') {
            die(header("Location: profile.php"));
        }

        else {
            echo $response . 'Login failed, please try again';
        }
    }

?>

<?php include __DIR__ . "/../partials/footer.php"; ?>