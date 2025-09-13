<div class="">
    <!-- Main Dashboard Content -->
    <div class="">
        <!-- Welcome Section -->
        <div class="">
            <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['user_first_name'] ?? 'User'); ?>!</h1>
            <p class="">Gain real-time insights, track system status, and monitor key metrics to ensure total satisfaction.</p>
        </div>

        <!-- Key Metrics Cards and Charts -->
        <div class="flex flex-col lg:flex-row gap-6 shadow-around mt-6">

            <!-- Left Column: Metrics and Bar Char -->
            <div class="flex-1 ">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">Pending NCAR</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold mt-2">13</p>
                    </div>
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">CSS Respondents</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold mt-2">1,989</p>
                    </div>
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">Survey Version</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <span class="inline-block  text-4xl font-bold px-3 py-1 mt-2 rounded-lg">v6.0</span>
                    </div>
                </div>

                <!-- Monthly Responses Chart -->
                <div class="bg-[#CFD8E5] rounded-lg p-6 shadow-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold ">Monthly Responses</h2>
                        <select class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option>Campus</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>
                    <canvas id="barChart" class="h-64"></canvas>
                </div>
            </div>

            <!-- Right Column: User Types Pie Chart -->
            <div class="lg:w-1/3 bg-[#CFD8E5] rounded-lg p-6 shadow-2xl flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold ">User Types</h2>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <p class="text-xs mb-4">As of May 25, 2024 at 10:07 PM</p>
                <div class="flex-grow flex items-center justify-center">
                    <canvas id="pieChart" class="h-64 w-64"></canvas>
                </div>
                <!-- Legend for Pie Chart (you'd generate this dynamically with Chart.js) -->
                <div class="mt-4 text-sm text-gray-600">
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-blue-700 mr-2"></span> Unit Head</div>
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-blue-400 mr-2"></span> HIMS</div>
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> CSS Coordinator</div>
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-purple-500 mr-2"></span> Dean/UA</div>
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span> CSS Head</div>
                    <div class="flex items-center mb-1"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> DQO</div>
                </div>
            </div>
        </div>
    </div>
</div>