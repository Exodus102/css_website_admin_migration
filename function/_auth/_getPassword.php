<?php
// Enable detailed error reporting for debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// --- Database Configuration ---
require_once '../../function/_databaseConfig/_dbConfig.php';

// Include PHPMailer files
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../../PHPMailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../PHPMailer/vendor/phpmailer/phpmailer/src/SMTP.php';

// Check if the email is available in the session. If not, redirect.
if (!isset($_SESSION['login_username'])) {
    $_SESSION['login_error'] = "No email provided for login.";
    header("Location: ../../index.php");
    exit();
}

$email_from_session = $_SESSION['login_username'];

// --- Process Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the password input is set and not empty
    if (isset($_POST['pass']) && !empty($_POST['pass'])) {
        $input_password = trim($_POST['pass']);

        // Prepare a statement to fetch the password and user details for the given email
        $stmt = $pdo->prepare("SELECT user_id, password, first_name, email, type FROM credentials WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email_from_session, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user_credentials = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user_credentials['user_id'];
            $stored_password = $user_credentials['password']; // This is now a plain-text password
            $user_first_name = $user_credentials['first_name'];
            $user_email = $user_credentials['email'];
            $user_type = $user_credentials['type'];

            // --- Password Verification (direct string comparison) ---
            if ($input_password === $stored_password) {
                // Password is correct. Generate and store the 2FA code.
                $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                // Clear any old codes for this user before inserting the new one
                $clearStmt = $pdo->prepare("DELETE FROM two_factor_codes WHERE user_id = ?");
                $clearStmt->execute([$user_id]);

                // Insert the new code and expiration time
                $insertStmt = $pdo->prepare("INSERT INTO two_factor_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
                $insertStmt->execute([$user_id, $verificationCode, $expiresAt]);

                // Send the email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'dlhor65@gmail.com';
                    $mail->Password   = 'mqvt lbsn naoe fgze';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('ursmain@urs.edu.ph', 'Customer Satisfaction Survey System');
                    $mail->addAddress($user_email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your 2-Step Verification Code';
                    $mail->Body    = "Hello {$user_first_name}, <br><br>Your verification code is: <b>{$verificationCode}</b><br><br>This code is valid for 10 minutes.";
                    $mail->AltBody = "Hello {$user_first_name}, Your verification code is: {$verificationCode}. This code is valid for 10 minutes.";

                    $mail->send();

                    // Set session variables for verification and redirect
                    $_SESSION['user_authenticated_pending'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_first_name'] = $user_first_name;
                    $_SESSION['user_type'] = $user_type;

                    // Redirect to the 2FA verification page
                    header("Location: ../../pages/login/two-factor-authentication.php");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['login_error'] = "Could not send verification email: {$mail->ErrorInfo}";
                    header("Location: ../../pages/login/password.php");
                    exit();
                }
            } else {
                $_SESSION['login_error'] = "Incorrect password.";
                header("Location: ../../pages/login/password.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "User not found or credentials mismatch.";
            header("Location: ../../pages/login/password.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Please enter your password.";
        header("Location: ../../pages/login/password.php");
        exit();
    }
} else {
    header("Location: ../../index.php");
    exit();
}
