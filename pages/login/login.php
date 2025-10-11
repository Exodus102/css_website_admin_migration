<div class="h-screen flex flex-col md:flex-row bg-[#f2f7fa]">

  <?php include 'pages/login/login_header.php'; ?>

  <!-- reduced padding -->
  <div class="md:w-1/2">
    <div class="w-full h-full flex flex-col items-center justify-around">
      <!-- Logo -->
      <div class="flex items-center gap-3">
        <?php
        include 'pages/login/logo.php';
        ?>
        <div class="text-left">
          <h2 class="font-bold text-blue-800">URSatisfaction</h2>
          <p class="text-xs text-gray-500">We comply so URSatisfied</p>
        </div>
      </div>

      <!-- Title -->
      <div class="flex flex-col items-center w-full">
        <h3 class="text-2xl font-bold text-[#064089] text-center mb-1">Log in</h3>
        <p class="text-sm text-gray-600 text-center mb-4">
          Using your URS email account or username
        </p>

        <!-- Email-only form -->
        <form action="function/_auth/_getEmail.php" method="post" class="space-y-3 w-full px-28">

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

          <!-- Forgot email trigger (button instead of link) -->
          <div>
            <button type="button" id="forgotEmailBtn"
              class="text-sm text-[#064089] hover:underline focus:outline-none">
              Forgot email?
            </button>
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

      <footer class="mt-6 text-center text-xs text-gray-600 max-w-xs mx-auto">
        <p>
          You are agreeing to the
          <a href="#" class="text-blue-700 font-semibold hover:text-blue-900">Terms of Services</a>
          and
          <a href="#" class="text-blue-700 font-semibold hover:text-blue-900">Privacy Policy</a>.
        </p>
      </footer>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="forgotEmailModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
  <div class="bg-white rounded-lg shadow-lg w-80 p-6">
    <!-- Changed from max-w-md to w-80 -->
    <h2 class="text-lg font-bold text-[#064089] mb-3">Forgot your email?</h2>
    <p class="text-gray-700 mb-4 text-sm">
      Please contact the University MIS office to recover your account credentials.
    </p>
    <div class="flex justify-end">
      <button id="closeModalBtn"
        class="px-4 py-2 bg-[#064089] text-white rounded-md hover:bg-[#002266] text-sm">
        OK
      </button>
    </div>
  </div>
</div>


<!-- Script -->
<script>
  const modal = document.getElementById("forgotEmailModal");
  const openBtn = document.getElementById("forgotEmailBtn");
  const closeBtn = document.getElementById("closeModalBtn");

  openBtn.addEventListener("click", () => {
    modal.classList.remove("hidden");
  });

  closeBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
  });

  // Close modal when clicking outside
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.classList.add("hidden");
    }
  });
</script>