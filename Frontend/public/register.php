<?php
session_start();
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php";
require_once(__DIR__ . "/../lib/functions.php");
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (is_logged_in()) {
    header("Location: profile.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user     = trim($_POST['user']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];
    $phone    = trim($_POST['phone']);

    // basic validation
    if ($password !== $confirm) {
        $error = "Passwords must match.";
    } elseif (!preg_match('/^\+?\d{7,15}$/', $phone)) {
        $error = "Please enter a valid phone number.";
    }

    if (!$error) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $request = [
            'type'     => 'registration',
            'user'     => $user,
            'password' => $hash,
            'email'    => $email,
            'phone'    => $phone     
        ];

        try {
            $response = rabbitConnect($request);
            if (is_array($response) && ($response['status'] ?? '') === 'success') {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = $response['message'] ?? "Registration failed.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">Register</h4>
        </div>
        <div class="card-body">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php elseif ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
          <?php endif; ?>

          <form method="POST" action="register.php">
            <div class="mb-3">
              <label for="user" class="form-label">Username</label>
              <input type="text" class="form-control" id="user" name="user" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phone" name="phone"
                     placeholder="+1234567890" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password"
                     name="password" required>
            </div>
            <div class="mb-3">
              <label for="confirm" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="confirm"
                     name="confirm" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/../partials/footer.php"; ?>
