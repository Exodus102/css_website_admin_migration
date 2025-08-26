<?php
session_start();

// Ensure the user is in a pending authenticated state
if (!isset($_SESSION['user_authenticated_pending']) || !$_SESSION['user_authenticated_pending']) {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification</title>
</head>

<body>
    <h2>2-Step Verification</h2>
    <p>A verification code has been sent to your email.</p>
    <form action="../../function/_auth/_authenticationCode.php" method="POST">
        <label for="code">Enter the 6-digit code:</label><br>
        <input type="text" id="code" name="code" required><br><br>
        <button type="submit">Verify Code</button>
    </form>
    <?php
    if (isset($_SESSION['login_error'])) {
        echo '<p style="color:red;">' . $_SESSION['login_error'] . '</p>';
        unset($_SESSION['login_error']);
    }
    ?>
</body>

</html>