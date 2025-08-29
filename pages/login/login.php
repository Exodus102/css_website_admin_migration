<div class="min-h-screen flex flex-col md:flex-row bg-[#f2f7fa]">

  <?php include 'pages/login/2fa.php'; ?>

  <!-- reduced padding -->
  <div class="md:w-1/2 flex flex-col justify-center items-center bg-transparent p-4 md:p-6">
    <div class="w-full max-w-sm">



      <!-- Title -->
      <h3 class="text-2xl font-bold text-[#064089] text-center mb-1">Log in</h3>
      <p class="text-sm text-gray-600 text-center mb-4">
        Using your URS email account or username
      </p>

      <!-- Email-only form -->
      <form action="function/_auth/_getEmail.php" method="post" class="space-y-3">

        <!-- Floating Label Input -->
        <div class="relative">
          <input type="email" name="username" id="username" required
            class="peer w-full px-3 pt-3 pb-1 border border-[#064089] rounded-md 
                        focus:outline-none focus:ring-0 focus:border-[#064089]"
            placeholder=" " />
          <label for="username"
            class="absolute left-3 -top-2 bg-white px-1 text-gray-600 text-sm transition-all
                        peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-gray-400 peer-placeholder-shown:text-base peer-placeholder-shown:bg-transparent
                        peer-focus:-top-2 peer-focus:text-sm peer-focus:text-[#064089] peer-focus:bg-[#F1F7F9]">
            Email or Username
          </label>
        </div>

        <!-- Forgot email link -->
        <div>
          <a href="#" class="text-sm text-[#064089]">Forgot email?</a>
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