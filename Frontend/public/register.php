<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
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
        $username = $_POST["username"];
        $email = $_POST["email"];
        $hasErrors = false;

        if($password !== $confirm) {
            echo "<script type='text/javascript'>alert('Passwords need to match');</script>";
            $hasErrors = true;
        }
    
        if(!$hasErrors) {
            echo "<script type='text/javascript'>alert('Success!');</script>";
        }        
    }

?>

<?php include __DIR__ . "/../partials/footer.php"; ?>