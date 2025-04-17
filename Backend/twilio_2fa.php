<?php
session_start();

require_once 'vendor/autoload.php';
use Twilio\Rest\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$account_sid = $_ENV['TWILIO_ACCOUNT_SID'];
$auth_token  = $_ENV['TWILIO_AUTH_TOKEN'];
$verify_sid  = $_ENV['TWILIO_VERIFY_SID'];

$twilio = new Client($account_sid, $auth_token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['phone'])) {
        $_SESSION['phone'] = $_POST['phone'];

        try {
            $verification = $twilio->verify->v2->services($verify_sid)
                ->verifications
                ->create($_SESSION['phone'], "sms");

            echo "<p>Verification code sent to {$_SESSION['phone']}</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Error sending verification: " . $e->getMessage() . "</p>";
            session_destroy();
            exit;
        }
    }

    if (isset($_POST['code'])) {
        try {
            $verification_check = $twilio->verify->v2->services($verify_sid)
                ->verificationChecks
                ->create([
                    'to' => $_SESSION['phone'],
                    'code' => $_POST['code']
                ]);

            if ($verification_check->status === "approved") {
                echo "<p style='color:green;'>Verification successful. You're authenticated!</p>";
                session_destroy();
                exit;
            } else {
                echo "<p style='color:red;'>Invalid code. Try again.</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color:red;'>Error verifying code: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<h2>Enter your phone number</h2>
<form method="POST">
    <input type="text" name="phone" placeholder="+1234567890" required>
    <button type="submit">Send Code</button>
</form>

<h2>Enter your verification code</h2>
<form method="POST">
    <input type="text" name="code" placeholder="123456" required>
    <button type="submit">Verify</button>
</form>
