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
                    <button id="" class="bg-[#D6D7DC] border border-[#1E1E1E] px-2 py-1 rounded shadow-sm text-sm flex items-center h-7 gap-2 mt-2">
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

        <div>

        </div>
    </div>
</div>

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
    });
</script>