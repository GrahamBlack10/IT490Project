<?php
session_start();

require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

$responseMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['code'])) {
        $user_id = $_POST['user_id'];
        $code = $_POST['code'];


        $request = [
            'type' => 'verify_2fa',
            'user_id' => $user_id,
            'code' => $code
        ];

        $response = rabbitConnect($request);
        $responseMessage = $response['message'];
        
        if ($response['status'] === 'success') {
            $_SESSION['verified'] = true;
            header("Location: profile.php");
            exit;
        }
        
        }
    }

?>

<h2>Enter 2FA Verification Code</h2>
<form method="POST">
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_GET['user_id'] ?? ''); ?>">
    <input type="text" name="code" placeholder="Enter 6-digit code" required>
    <button type="submit">Verify</button>
</form>

<?php if (!empty($responseMessage)) : ?>
    <p style="margin-top:1em; font-weight:bold;"><?php echo htmlspecialchars($responseMessage); ?></p>
<?php endif; ?>
