<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
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
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<?php 
    if (isset($_POST["user"]) && isset($_POST["password"])) {
        $user = $_POST["user"];
        $password = $_POST["password"];
        echo "Success!";
    }

?>

<?php include __DIR__ . "/../partials/footer.php"; ?>