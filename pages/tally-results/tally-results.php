<div>
    <h1 class="text-4xl font-bold">Tally Results</h1>
    <P>You are viewing the tally results of available offices for this period.</P>
    <!-- Filters Bar -->
    <div class="bg-[#E6E7EC] p-4  mb-4">
    <div class="flex flex-wrap items-end gap-4">
        <!-- Label -->
        <span class="font-semibold text-gray-700">FILTERS:</span>

        <!-- Review Period -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Review Period</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>Review Period</option>
        </select>
        </div>

        <!-- Campus -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Campus</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>Campus</option>
        </select>
        </div>

        <!-- Division -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Division</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>Division</option>
        </select>
        </div>

        <!-- Year -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Year</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>2024</option>
        </select>
        </div>

        <!-- Quarter -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Quarter</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>4th Quarter</option>
        </select>
        </div>

        <!-- Month -->
        <div class="flex flex-col">
        <label class="text-xs text-gray-500 uppercase mb-1">Month</label>
        <select class="border border-black bg-[#E6E7EC] rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option>November</option>
        </select>
        </div>
    </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="border px-4 py-2 border-[#1E1E1ECC] shadow-lg overflow-hidden">
            <thead class="bg-[#064089] text-white font-normal text-left w-full">
            <tr>
                <th class="border px-4 py-3 border-[#1E1E1ECC]">Office</th>
                <th class="border px-20 py-3 border-[#1E1E1ECC]">Month</th>
                <th class="border px-20 py-3 border-[#1E1E1ECC]">Analysis</th>
                <th class="border px-20 py-3 border-[#1E1E1ECC]">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
            </tbody>
        </table>
    </div>

</div>