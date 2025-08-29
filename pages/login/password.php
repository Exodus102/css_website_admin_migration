<?php
session_start(); // Always start the session at the top

// Check if the username is available in the session
// If not, redirect back to the login page (or an error page)
if (!isset($_SESSION['login_username'])) {
  header("Location: ../../index.php"); // Adjust path to your main login/email input page if necessary
  exit();
}

// Retrieve the username from the session
$username_for_password = $_SESSION['login_username'];
// NEW: Retrieve the first name from the session. Use a default if not set to prevent errors.
$first_name_for_display = $_SESSION['login_first_name'] ?? 'User';
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

    <?php include 'header-sec-password.php'; ?>

    <!-- reduced padding -->
    <div class="md:w-1/2 flex flex-col justify-center items-center bg-transparent p-4 md:p-6">
      <div class="w-full max-w-sm">

        <?php include 'logo-password.php'; ?>

        <!-- Avatar above welcome -->
        <div class="flex justify-center mb-3">
          <div class="w-16 h-16 rounded-full bg-[#064089] flex items-center justify-center overflow-hidden">
            <img src="/resources/svg/oikawa.svg" alt="Login Avatar" class="w-3/4 h-3/4 object-contain">
          </div>
        </div>

        <!-- Title -->
        <!-- MODIFIED: Display the first name from the session -->
        <h3 class="text-2xl font-bold text-[#064089] text-center mb-1">Welcome, <?php echo htmlspecialchars($first_name_for_display); ?></h3>
        <p class="text-sm text-center mb-4">
          <a href="email.php" class="text-gray-600 underline hover:text-[#064089]">not you?</a>
        </p>

        <!-- Email-only form -->
        <form action="../../function/_auth/_getPassword.php" method="post" class="space-y-3">

          <!-- Floating Label Input -->
          <div class="relative">
            <input type="password" name="pass" id="pass" required
              class="peer w-full px-3 pt-3 pb-1 border border-[#064089] rounded-md 
                         focus:outline-none focus:ring-0 focus:border-[#064089]"
              placeholder=" " />
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
              <input type="checkbox" id="showPass" onclick="togglePassword()" class="cursor-pointer">
              <label for="showPass" class="cursor-pointer">Show password</label>
            </div>
            <a href="../../function/_auth/_sedPasswordResetCode.php?email=<?php echo urlencode($username_for_password); ?>" class="text-[#064089] hover:underline">Forgot password?</a>
          </div>

          <!-- Next Button -->
          <div class="flex justify-end">
            <button type="submit"
              class="w-fit bg-[#064089] text-white font-semibold px-6 py-2 rounded-md shadow-md hover:bg-[#002266]">
              Next
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- JS for toggle password -->
  <script>
    function togglePassword() {
      const passInput = document.getElementById("pass");
      passInput.type = passInput.type === "password" ? "text" : "password";
    }
  </script>
</body>

</html>