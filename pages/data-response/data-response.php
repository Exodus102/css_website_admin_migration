<div class="min-h-screen bg-gray-100 p-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-black-800">Data Responses</h1>
        <p class="text-black-600">
            You are viewing the responses from the survey questionnaire currently in use.
        </p>
    </div>

    <!-- Filters -->
    <div class="flex items-end justify-between mb-6">
        <div class="flex items-end gap-6">
            <span class="font-semibold text-gray-700">FILTERS:</span>

            <div class="flex-grow">
                <label for="filter_division" class="block text-xs font-medium text-[#48494A]">DIVISION</label>
                <select name="filter_division" id="filter_division" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC] ">
                    <option value="" hidden>Division</option>
                </select>
            </div>
             <div class="flex-grow">
                <label for="filter_unit" class="block text-xs font-medium text-[#48494A]">OFFICE</label>
                <select name="filter_unit" id="filter_dunit" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC] ">
                    <option value="" hidden>Office</option>
                </select>
            </div>
             <div class="flex-grow">
                <label for="filter_year" class="block text-xs font-medium text-[#48494A]">YEAR</label>
                <select name="filter_year" id="filter_year" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC] ">
                    <option value="" hidden>Year</option>
                </select>
            </div>
              <div class="flex-grow">
                <label for="filter_quarter" class="block text-xs font-medium text-[#48494A]">QUARTER</label>
                <select name="filter_quarter" id="filter_quarter" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC] ">
                    <option value="" >1st Quarter</option>
                    <option value="" >2nd Quarter</option>
                </select>
            </div>
        </div>

        <!-- Upload CSV button -->
        <button class="bg-transparent border border-[#1E1E1E] px-4 py-2 rounded shadow-sm text-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v16h16V4H4zm4 8h8m-4-4v8"/>
            </svg>
            Upload CSV
        </button>
    </div>

<!-- Table -->
<div class="overflow-x-auto bg-white border border-gray-300 rounded-lg overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-blue-900 text-white text-sm font-semibold">
                <th class="px-4 py-2 border border-gray-300">ID</th>
                <th class="px-4 py-2 border border-gray-300">Timestamp</th>
                <th class="px-4 py-2 border border-gray-300">Name (Optional)</th>
                <th class="px-4 py-2 border border-gray-300">Contact Number</th>
                <th class="px-4 py-2 border border-gray-300">Customer Type</th>
                <th class="px-4 py-2 border border-gray-300">Division</th>
                <th class="px-4 py-2 border border-gray-300">Office/Unit Being Assessed</th>
                <th class="px-4 py-2 border border-gray-300">Purpose of Visit</th>
                <th class="px-4 py-2 border border-gray-300">Comments & Suggestions</th>
                <th class="px-4 py-2 border border-gray-300">
                    <select class="bg-blue-900 text-white text-sm font-semibold focus:outline-none">
                        <option>Analysis ▼</option>
                        <option>Good</option>
                        <option>Neutral</option>
                        <option>Bad</option>
                    </select>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-4 py-2 border border-gray-300 text-center">01</td>
                <td class="px-4 py-2 border border-gray-300 text-center">10/19/2024 18:08:53</td>
                <td class="px-4 py-2 border border-gray-300 text-center">Wjames</td>
                <td class="px-4 py-2 border border-gray-300 text-center"></td>
                <td class="px-4 py-2 border border-gray-300 text-center">Student</td>
                <td class="px-4 py-2 border border-gray-300 text-center">Academic Affairs</td>
                <td class="px-4 py-2 border border-gray-300 text-center">College of Accountancy</td>
                <td class="px-4 py-2 border border-gray-300 text-center">Consult</td>
                <td class="px-4 py-2 border border-gray-300 text-center">v helpful</td>
                <td class="px-4 py-2 border border-gray-300 text-center">
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-500 text-white">
                        Good
                    </span>
                </td>
            </tr>
            <!-- Add Row with full borders -->
            <tr class="bg-gray-100 text-sm">
                <td class="px-4 py-2 border border-gray-300 text-black-600 cursor-pointer font-medium">+ Add Row</td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
                <td class="px-4 py-2 border border-gray-300"></td>
            </tr>
        </tbody>
    </table>
</div>


    <!-- Pagination -->
<div class="flex items-end gap-4 mt-4 text-sm">
    <!-- Previous -->
    <div>
        <button class="border border-[#1E1E1E] py-1 px-3 rounded bg-[#E6E7EC] text-gray-500" disabled>
            &lt; Previous
        </button>
    </div>

    <!-- Current Page -->
    <div>
        <span class="inline-block text-center border border-[#1E1E1E] py-1 px-4 rounded bg-white">
            01
        </span>
    </div>

    <!-- Next -->
    <div>
        <button class="border border-[#1E1E1E] py-1 px-3 rounded bg-[#E6E7EC] text-gray-700">
            Next Page &gt;
        </button>
    </div>
</div>

