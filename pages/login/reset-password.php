<?php
session_start();
// Check if the user is authorized to be on this page
if (!isset($_SESSION['authorized_to_reset']) || !$_SESSION['authorized_to_reset']) {
    $_SESSION['reset_error'] = "You are not authorized to access this page.";
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>
    <h2>Reset Password</h2>
    <form action="../../function/_auth/_updatePassword.php" method="POST">
        <label for="new_password">New Password:</label><br>
        <input type="password" name="new_password" id="new_password" required><br><br>
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" name="confirm_password" id="confirm_password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>