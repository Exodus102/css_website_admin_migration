<div class="min-h-screen flex flex-col md:flex-row bg-[#f2f7fa]">

  <?php include 'pages/login/header_sec.php'; ?>

  <div class="md:w-1/2 flex flex-col justify-center items-center bg-transparent p-8 md:p-12">
    <div class="w-full max-w-sm">
      
      <?php include 'pages/login/logo.php'; ?>

      <!-- Title -->
      <h3 class="text-2xl font-bold text-[#003087] text-center mb-1">Log in</h3>
      <p class="text-sm text-gray-600 text-center mb-6">
        Please enter your password
      </p>

      <!-- Password form -->
      <form action="../dashboard.php" method="post" class="space-y-4">

        <!-- Floating Label Password Input -->
        <div class="relative">
          <input type="password" name="password" id="password" required
                 class="peer w-full px-3 pt-5 pb-2 border border-[#003087] rounded-md 
                        focus:outline-none focus:ring-0 focus:border-[#003087]"
                 placeholder=" " />
          <label for="password"
                 class="absolute left-2 -top-2 px-1 bg-[#f2f7fa] text-gray-600 text-sm transition-all
                        peer-placeholder-shown:top-3 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-base
                        peer-focus:-top-2 peer-focus:text-sm peer-focus:text-[#003087]">
            Password
          </label>
        </div>

        <!-- Right-aligned Login Button -->
        <div class="flex justify-end">
          <button type="submit"
                  class="w-fit bg-[#003087] text-white font-semibold px-8 py-2 rounded-md shadow-md hover:bg-[#002266]">
            Login
          </button>
        </div>
      </form>

      <!-- Footer Text -->
      <p class="text-xs text-gray-600 mt-10 text-center">
        Trouble logging in? 
        <a href="#" class="text-[#003087] hover:underline">Get help</a>
      </p>
    </div>
  </div>
</div>
