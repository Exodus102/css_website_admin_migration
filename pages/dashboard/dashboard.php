<div class="relative h-64">
    
    
    <h1 class="text-3xl font-bold mb-2">Welcome, #user101</h1>
    <p>Gain real-time insights, track system status, and monitor key metrics to ensure total satisfaction.</p>
    <div class="flex flex-col lg:flex-row gap-6">

        <div class="p-6 bg-gray-100 min-h-screenflex flex-col lg:flex-row gap-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-[#749DC833] shadow-2xl rounded-lg p-4">
                    <h1 class="text-xl">Pending NCAR</h1>
                    <p class="text-2xl font-semibold text-gray-800">13</p>
                </div>
                <div class="bg-[#749DC833] shadow-2xl rounded-lg p-4">
                    <h1 class="text-xl">CSS Respondents</h1>
                    <p class="text-2xl font-semibold text-gray-800">1,989</p>
                </div>
                <div class="bg-[#749DC833] shadow-2xl rounded-lg p-4">
                    <h1 class="text-xl">Survey Version</h1>
                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full">v6.0</span>
                </div>
                

                <div class="flex-1 w-full">
                    <div class="bg-[#749DC833] shadow-2xl rounded-lg p-6">
                        <div class="flex-1 justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold">Monthly Responses</h2>
                            <select class="border rounded px-2 py-1 text-sm">
                            <option>Campus</option>
                            </select>
                        </div>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

            
            <div class="flex lg:w-1/3 bg-[#749DC833] p-6 rounded shadow-2xl">
                <h2 class="text-lg font-semibold mb-4">User Types</h2>
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
    
    
    

</div>