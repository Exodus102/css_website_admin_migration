<div class="p-4">
    <script>
        // Apply saved font size on every page load
        (function() {
            const savedSize = localStorage.getItem('user_font_size');
            if (savedSize) {
                document.documentElement.style.fontSize = savedSize;
            }
        })();
    </script>
    <h1 class="text-3xl font-bold mb-2 font-sfpro leading-5">Display</h1>
    <p class="font-sfpro">Customize the appearance and layout of your system display.</p><br>

    <div class="flex flex-col">
        <div class="flex gap-4 mb-4">
            <div class="w-1/3 p-4 bg-[#F1F7F9] border border-gray-200 rounded-lg shadow-md">
                <div class="w-3/4 mx-auto">
                    <h3 class="text-lg font-bold">Logo</h3>
                    <button id="change-logo-btn" class="bg-[#D6D7DC] border border-[#1E1E1E] px-2 py-1 rounded shadow-sm text-sm flex items-center h-7 gap-2 mt-2">
                        <img src="../../resources/svg/change-logo.svg" alt="" srcset="">
                        <p class="font-bold">Change Logo</p>
                    </button>
                </div>
            </div>
            <div class="w-1/3 p-4 bg-[#F1F7F9] border border-gray-200 rounded-lg shadow-md flex flex-col">
                <div class="flex-grow flex flex-col justify-center">
                    <div class="w-3/4 mx-auto">
                        <h3 class="text-lg font-bold mb-2">Font Size</h3>
                        <div id="font-size-slider" class="relative flex items-center justify-between">
                            <div class="absolute left-0 top-1/2 w-full h-0.5 bg-gray-300 transform -translate-y-1/2"></div>
                            <!-- Slider Points -->
                            <div class="font-size-point relative w-2 h-2 bg-gray-500 rounded-full z-10 cursor-pointer" data-size="14px"></div>
                            <div class="font-size-point relative w-3 h-3 bg-gray-500 rounded-full z-10 cursor-pointer" data-size="15px"></div>
                            <div class="font-size-point relative w-4 h-4 bg-[#064089] rounded-full z-10 cursor-pointer shadow-md" data-size="16px"></div>
                            <div class="font-size-point relative w-5 h-5 bg-gray-500 rounded-full z-10 cursor-pointer" data-size="17px"></div>
                            <div class="font-size-point relative w-6 h-6 bg-gray-500 rounded-full z-10 cursor-pointer" data-size="18px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-1/3 p-4 bg-[#F1F7F9] border border-gray-200 rounded-lg shadow-md flex flex-col">
                <div class="flex-grow flex flex-col justify-center">
                    <div class="w-3/4 mx-auto">
                        <h3 class="text-lg font-bold mb-2">Theme</h3>
                        <div class="flex gap-4 justify-center">
                            <button id="" class="flex-1 justify-center bg-[#D6D7DC] border border-[#1E1E1E] py-1 rounded shadow-sm text-sm flex items-center h-7 gap-2">
                                <p class="font-bold">Default</p>
                            </button>
                            <button id="" class="flex-1 justify-center bg-[#0D2442] py-1 rounded shadow-sm text-sm flex items-center h-7 gap-2">
                                <p class="font-bold text-white">Lights Out</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Logo Dialog -->
<dialog id="upload-logo-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <form id="upload-logo-form" method="POST" class="space-y-4">
        <h3 class="font-bold text-lg mb-4">Change System Logo</h3>
        <p class="text-sm text-gray-600">Select an image file (PNG, JPG, GIF). The recommended size is square (e.g., 200x200 pixels). Max file size: 2MB.</p>
        <div>
            <label for="logo-file-input" class="block text-sm font-medium text-gray-700">Logo File</label>
            <input type="file" id="logo-file-input" name="logo_file" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100" accept="image/png, image/jpeg, image/gif" required>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" id="cancel-upload-logo" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2 disabled:opacity-50">Upload</button>
        </div>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderPoints = document.querySelectorAll('.font-size-point');
        const rootElement = document.documentElement;

        // Function to update the visual state of the slider
        const updateSliderVisuals = (activeSize) => {
            sliderPoints.forEach(p => {
                p.classList.remove('bg-[#064089]', 'shadow-md');
                p.classList.add('bg-gray-500');
                if (p.dataset.size === activeSize) {
                    p.classList.add('bg-[#064089]', 'shadow-md');
                    p.classList.remove('bg-gray-500');
                }
            });
        };

        sliderPoints.forEach(point => {
            point.addEventListener('click', function() {
                const newSize = this.dataset.size;
                // Apply the font size to the page
                rootElement.style.fontSize = newSize;
                // Save the setting to localStorage
                localStorage.setItem('user_font_size', newSize);
                // Update the slider to show the new active circle
                updateSliderVisuals(newSize);
            });
        });

        // On page load, check if a font size is saved and update the slider's active state
        const savedSize = localStorage.getItem('user_font_size');
        if (savedSize) {
            sliderPoints.forEach(p => {
                updateSliderVisuals(savedSize);
            });
        }

        // --- Logo Upload Logic ---
        const changeLogoBtn = document.getElementById('change-logo-btn');
        const uploadLogoDialog = document.getElementById('upload-logo-dialog');
        const uploadLogoForm = document.getElementById('upload-logo-form');
        const cancelUploadLogoBtn = document.getElementById('cancel-upload-logo');
        const logoFileInput = document.getElementById('logo-file-input');

        if (changeLogoBtn) {
            changeLogoBtn.addEventListener('click', () => uploadLogoDialog.showModal());
        }

        if (cancelUploadLogoBtn) {
            cancelUploadLogoBtn.addEventListener('click', () => uploadLogoDialog.close());
        }

        if (uploadLogoDialog) {
            uploadLogoDialog.addEventListener('click', (e) => {
                if (e.target === uploadLogoDialog) {
                    uploadLogoDialog.close();
                }
            });
        }

        if (uploadLogoForm) {
            uploadLogoForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (!logoFileInput.files || logoFileInput.files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const submitButton = uploadLogoForm.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.textContent;
                submitButton.disabled = true;
                submitButton.textContent = 'Uploading...';

                const formData = new FormData(uploadLogoForm);

                try {
                    const response = await fetch('../../function/_display/_uploadLogo.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    alert(result.message);
                    if (result.success) {
                        window.location.reload(); // Reload to reflect changes
                    }
                } catch (error) {
                    alert('An error occurred during the upload. Please check the console.');
                    console.error('Upload Error:', error);
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                    uploadLogoDialog.close();
                }
            });
        }
    });
</script>