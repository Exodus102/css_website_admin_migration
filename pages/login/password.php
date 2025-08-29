<?php
session_start();

// Check if the username is available in the session
if (!isset($_SESSION['login_username'])) {
  header("Location: ../../index.php");
  exit();
}

$username_for_password = $_SESSION['login_username'];
$first_name_for_display = $_SESSION['login_first_name'] ?? 'User';

// 🔹 Error, attempts & lockout values
$error = $_SESSION['login_error'] ?? '';
$attempts = $_SESSION['attempts'] ?? 0;
$max_attempts = 3;
$lockout_time = $_SESSION['lockout_time'] ?? 0;
$remaining = max(0, $lockout_time - time());

// ✅ Reset everything after lockout time expires
if ($remaining <= 0 && $lockout_time > 0) {
  unset($_SESSION['login_error']);
  unset($_SESSION['lockout_time']);
  $_SESSION['attempts'] = 0;   // reset attempts
  $error = '';
  $attempts = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../../Tailwind/src/output.css">
  <title>Customer Satisfaction</title>
</head>

<body>
  <div class="min-h-screen flex flex-col md:flex-row bg-[#f2f7fa]">

    <?php include 'password_header.php'; ?>

    <!-- reduced padding -->
    <div class="md:w-1/2 flex flex-col justify-center items-center bg-transparent p-4 md:p-6">
      <div class="w-full max-w-sm">

        <!-- Avatar -->
        <div class="flex justify-center mb-3">
          <div class="w-16 h-16 rounded-full bg-[#064089] flex items-center justify-center overflow-hidden">
            <img src="/resources/svg/oikawa.svg" alt="Login Avatar" class="w-3/4 h-3/4 object-contain">
          </div>
        </div>

        <!-- Title -->
        <h3 class="text-2xl font-bold text-[#064089] text-center mb-1">
          Welcome, <?php echo htmlspecialchars($first_name_for_display); ?>
        </h3>
        <p class="text-sm text-center mb-4">
          <a href="javascript:history.back()" class="text-gray-600 underline hover:text-[#064089]">not you?</a>
        </p>

        <!-- 🔹 Error message -->
        <?php if ($error): ?>
          <div class="text-center mb-3">
            <p class="text-sm text-[#8B0000]" id="error-msg">
              <?php echo htmlspecialchars($error); ?>
            </p>
          </div>
        <?php endif; ?>

        <!-- 🔹 Attempts indicator -->
          <?php if ($attempts > 0 && $remaining <= 0): ?>
            <div class="text-center mb-3">
              <p class="text-sm font-semibold text-red-600">
                Attempts: <?php echo min($attempts, $max_attempts) . '/' . $max_attempts; ?>
              </p>
            </div>
          <?php endif; ?>

        <!-- 🔹 Lockout timer -->
        <?php if ($remaining > 0): ?>
          <div class="text-center mb-3">
            <p class="text-sm text-[#8B0000]">
              Please wait <span id="timer"><?php echo $remaining; ?></span> seconds before retrying.
            </p>
          </div>
        <?php endif; ?>

        <!-- Password form -->
        <form action="../../function/_auth/_getPassword.php" method="post" class="space-y-3">
          <div class="relative">
            <input type="password" name="pass" id="pass" required
              class="peer w-full px-3 pt-3 pb-1 border rounded-md 
                        focus:outline-none focus:ring-0 
                        <?php echo $wrong_password ? 'border-red-600 focus:border-red-600' : 'border-[#064089] focus:border-[#064089]'; ?>"
              placeholder=" " <?php echo $remaining > 0 ? 'disabled' : ''; ?> />

            <label for="pass"
              class="absolute left-3 -top-2 bg-white px-1 text-gray-600 text-sm transition-all
                         peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-base peer-placeholder-shown:bg-transparent
                         peer-focus:-top-2 peer-focus:text-sm peer-focus:text-[#064089] peer-focus:bg-[#F1F7F9]">
              Password
            </label>
          </div>

          <!-- Show password + Forgot password -->
          <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2 text-gray-700">
              <input type="checkbox" id="showPass" onclick="togglePassword()" class="cursor-pointer" <?php echo $remaining > 0 ? 'disabled' : ''; ?>>
              <label for="showPass" class="cursor-pointer">Show password</label>
            </div>
            <a href="../../function/_auth/_sendPasswordResetCode.php?email=<?php echo urlencode($username_for_password); ?>" class="text-[#064089] hover:underline">Forgot password?</a>
          </div>

          <!-- Next Button -->
          <div class="flex justify-end">
            <button type="submit"
              class="w-fit bg-[#064089] text-white font-semibold px-6 py-2 rounded-md shadow-md hover:bg-[#002266] "
              <?php echo $remaining > 0 ? 'disabled' : ''; ?>>
              Next
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

 <script>
  function togglePassword() {
    const passInput = document.getElementById("pass");
    passInput.type = passInput.type === "password" ? "text" : "password";
  }

  // 🔹 Countdown timer
  let timeLeft = <?php echo $remaining; ?>;
  if (timeLeft > 0) {
    const timerEl = document.getElementById("timer");
    const btn = document.querySelector("button[type='submit']");
    const passInput = document.getElementById("pass");
    const errorEl = document.getElementById("error-msg");
    const attemptsEl = document.querySelector("p.font-semibold.text-red-600"); // Attempts indicator

    const interval = setInterval(() => {
      timeLeft--;
      if (timerEl) timerEl.textContent = timeLeft;

      if (timeLeft <= 0) {
        clearInterval(interval);

        // ✅ Enable inputs again
        if (btn) btn.disabled = false;
        if (passInput) passInput.disabled = false;

        // ✅ Clean up messages
        if (errorEl) errorEl.textContent = "";
        if (attemptsEl) attemptsEl.remove(); // remove attempts 3/3
        if (timerEl) timerEl.parentElement.innerHTML = "You may now try again.";
      }
    }, 1000);
  }
</script>

</body>
</html>
