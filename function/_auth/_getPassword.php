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
    // 🔧 ensure session data is written before redirect
    session_write_close();
    header("Location: ../../index.php");
    exit();
}

$email_from_session = $_SESSION['login_username'];

// 🔹 Added for lockout system
if (!isset($_SESSION['attempts'])) $_SESSION['attempts'] = 0;
if (!isset($_SESSION['lockout_time'])) $_SESSION['lockout_time'] = 0;

$max_attempts = 3;
$lockout_seconds = 60;

// 🔧 Debug log of current session state (check PHP error log)
error_log("[getPassword] session start for {$email_from_session} | attempts={$_SESSION['attempts']} | lockout_time={$_SESSION['lockout_time']}");

// Check if still locked
if (intval($_SESSION['lockout_time']) > time()) {
    $_SESSION['login_error'] = "Too many failed attempts. Please wait 1 minute.";
    // 🔧 write session and redirect
    session_write_close();
    error_log("[getPassword] Locked out (still within lockout). Redirecting back.");
    header("Location: ../../pages/login/password.php");
    exit();
}

// Reset if lockout expired
if (intval($_SESSION['lockout_time']) != 0 && intval($_SESSION['lockout_time']) <= time()) {
    $_SESSION['attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
    unset($_SESSION['login_error']);
    // 🔧 log the reset
    error_log("[getPassword] Lockout expired. Reset attempts/lockout_time for {$email_from_session}.");
}

// --- Process Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the password input is set and not empty
    if (isset($_POST['pass']) && !empty($_POST['pass'])) {
        $input_password = trim($_POST['pass']);

        // Prepare a statement to fetch the password and user details for the given email
        $stmt = $pdo->prepare("SELECT user_id, password, first_name, last_name, middle_name, email, type, status, dp, contact_number, campus, unit FROM credentials WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email_from_session, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user_credentials = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_status = $user_credentials['status'];
            $user_id = $user_credentials['user_id'];
            $stored_password = $user_credentials['password']; // plain-text password
            $user_first_name = $user_credentials['first_name'];
            $user_last_name = $user_credentials['last_name'];
            $user_email = $user_credentials['email'];
            $user_type = $user_credentials['type'];
            $user_dp = $user_credentials['dp'];
            $user_campus = $user_credentials['campus'];
            $user_contact_number = $user_credentials['contact_number'];
            $user_unit = $user_credentials['unit'];
            $user_middle_name = $user_credentials['middle_name'];

            // --- Password Verification (direct string comparison) ---
            if ($input_password === $stored_password) {
                // ✅ Correct password: reset attempts
                $_SESSION['attempts'] = 0;
                $_SESSION['lockout_time'] = 0;
                unset($_SESSION['login_error']);

                // Check if the user's account is active
                if ($user_status === 'Inactive') {
                    header("Location: ../../pages/login/inactive.php");
                    exit();
                }

                // 🔧 log success
                error_log("[getPassword] Successful login for {$email_from_session}. Resetting attempts.");

                // Generate and store the 2FA code.
                $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                // Clear any old codes
                $clearStmt = $pdo->prepare("DELETE FROM two_factor_codes WHERE user_id = ?");
                $clearStmt->execute([$user_id]);

                // Insert new code
                $insertStmt = $pdo->prepare("INSERT INTO two_factor_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
                $insertStmt->execute([$user_id, $verificationCode, $expiresAt]);

                // Send email
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

                    // Session for verification
                    $_SESSION['user_authenticated_pending'] = true;
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['user_first_name'] = $user_first_name;
                    $_SESSION['user_last_name'] = $user_last_name;
                    $_SESSION['user_dp'] = $user_dp;
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['user_campus'] = $user_campus;
                    $_SESSION['user_contact_number'] = $user_contact_number;
                    $_SESSION['user_unit'] = $user_unit;
                    $_SESSION['user_password'] = $stored_password;
                    $_SESSION['user_middle_name'] = $user_middle_name;

                    // 🔧 ensure session is written before redirect
                    session_write_close();
                    header("Location: ../../pages/login/two-factor-authentication.php");
                    exit();
                } catch (Exception $e) {
                    $_SESSION['login_error'] = "Could not send verification email: {$mail->ErrorInfo}";
                    // 🔧 write session & log
                    session_write_close();
                    error_log("[getPassword] PHPMailer error for {$email_from_session}: {$mail->ErrorInfo}");
                    header("Location: ../../pages/login/password.php");
                    exit();
                }
            } else {
                // ❌ Wrong password
                $_SESSION['attempts']++;

                if ($_SESSION['attempts'] >= $max_attempts) {
                    $_SESSION['lockout_time'] = time() + $lockout_seconds;
                    $_SESSION['login_error'] = "Too many failed attempts. Please wait 1 minute.";
                    // 🔧 log lockout
                    error_log("[getPassword] Locking out {$email_from_session}. attempts={$_SESSION['attempts']} lockout_time={$_SESSION['lockout_time']}");
                } else {
                    $_SESSION['login_error'] = "Incorrect password.";
                    error_log("[getPassword] Incorrect password for {$email_from_session}. attempts={$_SESSION['attempts']}");
                }

                // 🔧 ensure session writes and redirect
                session_write_close();
                header("Location: ../../pages/login/password.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = "User not found or credentials mismatch.";
            // 🔧 write and log
            session_write_close();
            error_log("[getPassword] User not found for email: {$email_from_session}");
            header("Location: ../../pages/login/password.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "Please enter your password.";
        // 🔧 write and redirect
        session_write_close();
        error_log("[getPassword] Empty password submitted for {$email_from_session}");
        header("Location: ../../pages/login/password.php");
        exit();
    }
} else {
    session_write_close();
    header("Location: ../../index.php");
    exit();
}
