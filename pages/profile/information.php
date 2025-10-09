<div class="w-full lg:w-3/4 bg-[#F1F7F9] p-6 rounded-lg shadow-md">
    <form id="user-info-form" class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-[#1E1E1E]">User Information</h2>
            <button type="submit" id="save-profile-btn" class="w-28 justify-center bg-[#0D2442] py-2 rounded shadow-sm text-sm flex items-center h-7 gap-2">
                <p class="font-bold text-white">Save</p>
            </button>
        </div>
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-[#48494A]/50">CAMPUS</label>
            <div class="mt-1 relative">
                <input type="text" name="email" id="email" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none sm:text-sm bg-gray-100 cursor-not-allowed" value="<?php echo htmlspecialchars($_SESSION['user_campus'] ?? ''); ?>" readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <img src="../../resources/svg/lock.svg" class="h-5 w-5 text-gray-400" alt="Email Icon">
                </div>
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-[#48494A]/50">UNIT</label>
            <div class="mt-1 relative">
                <input type="text" name="email" id="email" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none sm:text-sm bg-gray-100 cursor-not-allowed" value="<?php echo htmlspecialchars($_SESSION['user_unit'] ?? ''); ?>" readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <img src="../../resources/svg/lock.svg" class="h-5 w-5 text-gray-400" alt="Email Icon">
                </div>
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-[#48494A]/50">USER TYPE</label>
            <div class="mt-1 relative">
                <input type="text" name="email" id="email" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none sm:text-sm bg-gray-100 cursor-not-allowed" value="<?php echo htmlspecialchars($_SESSION['user_type'] ?? ''); ?>" readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <img src="../../resources/svg/lock.svg" class="h-5 w-5 text-gray-400" alt="Email Icon">
                </div>
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-[#48494A]/50">FIRST NAME</label>
            <div class="mt-1 relative">
                <input type="text" name="first_name" id="first_name" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-[#F1F7F9]" value="<?php echo htmlspecialchars($_SESSION['user_first_name'] ?? ''); ?>">
            </div>
        </div>
        <div>
            <label for="middle_name" class="block text-sm font-medium text-[#48494A]/50">MIDDLE NAME</label>
            <div class="mt-1 relative">
                <input type="text" name="middle_name" id="middle_name" class="block w-full pl-3 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-[#F1F7F9]" value="<?php echo htmlspecialchars($_SESSION['user_middle_name'] ?? ''); ?>">
            </div>
        </div>
        <div>
            <label for="last_name" class="block text-sm font-medium text-[#48494A]/50">LAST NAME</label>
            <div class="mt-1 relative">
                <input type="text" name="last_name" id="last_name" class="block w-full pl-3 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-[#F1F7F9]" value="<?php echo htmlspecialchars($_SESSION['user_last_name'] ?? ''); ?>">
            </div>
        </div>
        <div>
            <label for="contact_number" class="block text-sm font-medium text-[#48494A]/50">CONTACT NUMBER</label>
            <div class="mt-1 relative">
                <input type="text" name="contact_number" id="contact_number" class="block w-full pl-3 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-[#F1F7F9]" value="<?php echo htmlspecialchars($_SESSION['user_contact_number'] ?? ''); ?>">
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-[#48494A]/50">EMAIL</label>
            <div class="mt-1 relative">
                <input type="email" name="email" id="email" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none sm:text-sm bg-gray-100 cursor-not-allowed" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" readonly>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <img src="../../resources/svg/lock.svg" class="h-5 w-5 text-gray-400" alt="Email Icon">
                </div>
            </div>
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-[#48494A]/50">PASSWORD</label>
            <div class="mt-1 relative">
                <input type="text" name="password" id="password" class="block w-full pl-3 pr-10 py-2 border border-[#1E1E1E] rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-[#F1F7F9]" value="<?php echo htmlspecialchars($_SESSION['user_password'] ?? ''); ?>">
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const userInfoForm = document.getElementById('user-info-form');
        userInfoForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(userInfoForm);
            const response = await fetch('../../function/_profile/_updateProfile.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) {
                window.location.reload();
            }
        });
    });
</script>