<?php
session_start();

include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/nav.php';
require_once __DIR__ . '/../lib/functions.php';
require_once __DIR__ . '/../rabbitmq/path.inc';
require_once __DIR__ . '/../rabbitmq/get_host_info.inc';
require_once __DIR__ . '/../rabbitmq/rabbitMQLib.inc';

$responseMessage = '';
// Prefer GET for initial user_id, fall back to session if needed
$user_id = $_GET['user_id'] ?? $_SESSION['user_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $code    = $_POST['code'];

    // Build and send the RPC request
    $request = [
        'type'              => 'verify_2fa',
        'user_id'           => $user_id,
        'code' => $code
    ];
    $response = rabbitConnect($request);
    $responseMessage = $response['message'] ?? '';

    // On success, mark verified and go to profile
    if (($response['status'] ?? '') === 'success') {
        $_SESSION['verified'] = true;
        header("Location: profile.php");
        exit();
    }
}
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h4 class="mb-0">Two-Factor Verification</h4>
        </div>
        <div class="card-body">
          <?php if ($responseMessage): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($responseMessage) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="verify_code.php?user_id=<?= urlencode($user_id) ?>">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
            <div class="mb-3">
              <label for="code" class="form-label">6-Digit Code</label>
              <input
                type="text"
                class="form-control"
                id="code"
                name="code"
                placeholder="Enter verification code"
                required
              >
            </div>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
