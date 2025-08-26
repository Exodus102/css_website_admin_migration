<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification</title>
</head>

<body>
    <h2>Verify Code</h2>
    <p>A verification code has been sent to your email.</p>

    <form action="../../function/_auth/_authenticationCodeForgotPassword.php" method="POST">
        <label for="code">Enter the 6-digit code:</label><br>
        <input type="text" name="code" id="code" required><br><br>
        <button type="submit">Verify Code</button>
    </form>

    <?php
    session_start();
    if (isset($_SESSION['reset_error'])) {
        echo '<p style="color:red;">' . $_SESSION['reset_error'] . '</p>';
        unset($_SESSION['reset_error']);
    }
    ?>
</body>

</html>