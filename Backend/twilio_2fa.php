<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;

session_start();

// Twilio Credentials (replace with yours)
$account_sid = 'ACc41c1f3bdb4a9fc30faf1685a35eb2df';
$auth_token = 'a335d47a5f7cd30d8c479cd49bace158';
$twilio_number = '+12017745715'; // e.g. "+1234567890"

$twilio = new Client($account_sid, $auth_token);

// If user hasn't submitted their phone number yet
if (!isset($_SESSION['2fa_sent']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<form method="POST">
            <label>Phone Number (+1XXX...):</label><br>
            <input type="text" name="phone" required>
            <button type="submit">Send Code</button>
          </form>';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['2fa_sent'])) {
    $phone = $_POST['phone'];
    $code = rand(100000, 999999); // Generate a 6-digit code

    // Save info in session
    $_SESSION['phone'] = $phone;
    $_SESSION['2fa_code'] = $code;
    $_SESSION['2fa_sent'] = true;

    // Send code via Twilio
    try {
        $twilio->messages->create(
            $phone,
            [
                'from' => $twilio_number,
                'body' => "Your verification code is: $code"
            ]
        );
        echo "<p>Verification code sent to $phone</p>";
        echo '<form method="POST">
                <label>Enter Code:</label><br>
                <input type="text" name="entered_code" required>
                <button type="submit">Verify</button>
              </form>';
    } catch (Exception $e) {
        echo "Error sending SMS: " . $e->getMessage();
        session_destroy();
    }
} elseif (isset($_SESSION['2fa_sent']) && isset($_POST['entered_code'])) {
    $entered = $_POST['entered_code'];
    $correct = $_SESSION['2fa_code'];

    if ($entered == $correct) {
        echo "<p style='color:green;'>✔ Code correct. You're logged in!</p>";
        session_destroy(); // end session
    } else {
        echo "<p style='color:red;'>✘ Invalid code. Try again.</p>";
        echo '<form method="POST">
                <label>Enter Code:</label><br>
                <input type="text" name="entered_code" required>
                <button type="submit">Verify</button>
              </form>';
    }
}
?>
