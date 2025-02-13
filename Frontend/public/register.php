<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');
?>

<form action="register.php" method="POST">
    <div class="form-group">
        <div class="col-md-4">
            <label for="user">Register </label>
            <input type="text" name="user" class="form-control" id="user" placeholder="Enter Username">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter Email">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <input type="password" name="password" class="form-control" id="password" placeholder="Password">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4">
            <input type="password" name="confirm" class="form-control" id="confirm" placeholder="Confirm Password">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<?php 
    if (isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["user"]) && isset($_POST["email"])) {
        $password = $_POST["password"];
        $confirm = $_POST["confirm"];
        $user = $_POST["user"];
        $email = $_POST["email"];
        $hasErrors = false;

        if($password !== $confirm) {
            echo "<script type='text/javascript'>alert('Passwords need to match');</script>";
            $hasErrors = true;
        }
    
        if(!$hasErrors) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            try {
                $request = array();
                $request['type'] = 'registration';
                $request['user'] = $user;
                $request['password'] = $hash;
                $request['email'] = $email;
                var_dump($request);
                $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
                $response = $client->send_request($request);
                print_r($response);
                //if(isset($response['type']) && $response['type'] === 'registration_response') {
                //    if($response['registration_status'] === 'success') {
                //        echo "<script type='text/javascript'>alert('Registration Success!');</script>";
                //    } else {
                //        echo "<script type='text/javascript'>alert('Fuck you');</script>";
                //    } 
                //}
            }
            catch (Exception $e) {
                echo $e;
            }        
        }
    }
?>

<?php include __DIR__ . "/../partials/footer.php"; ?>