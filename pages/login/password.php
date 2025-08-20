<div class="min-h-screen flex flex-col md:flex-row bg-[#f2f7fa]">

  <?php include 'pages/login/header_sec.php'; ?>

  <!-- reduced padding -->
  <div class="md:w-1/2 flex flex-col justify-center items-center bg-transparent p-4 md:p-6">
    <div class="w-full max-w-sm">
      
      <?php include 'pages/login/logo.php'; ?>

      <!-- Avatar above welcome -->
    <div class="flex justify-center mb-3">
  <div class="w-16 h-16 rounded-full bg-[#064089] flex items-center justify-center overflow-hidden">
    <img src="resources/svg/oikawa.svg" alt="Login Avatar" class="w-3/4 h-3/4 object-contain">
  </div>
</div>

      <!-- Title -->
      <h3 class="text-2xl font-bold text-[#064089] text-center mb-1">Welcome, Neil</h3>
      <p class="text-sm text-center mb-4">
        <a href="email.php" class="text-gray-600 underline hover:text-[#064089]">not you?</a>
      </p>

      <!-- Email-only form -->
      <form action="#" method="post" class="space-y-3">

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
          <a href="forgot_password.php" class="text-[#064089] hover:underline">Forgot password?</a>
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
