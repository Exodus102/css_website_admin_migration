<div class="w-full max-w-7xl bg-transparent rounded-lg shadow-sm p-6 mx-auto">

    <!-- Title -->
    <h1 class="text-2xl font-bold mb-1">Backup & Restore</h1>
    <p class="text-gray-600 mb-6">Maintain data security with backup and restoration options.</p>

    <!-- Data Restore Section -->
    <div class="bg-transparent border border-black rounded-md mb-6">
        <div class="p-4 border-b border-black">
            <h2 class="text-lg font-semibold">Data Restore</h2>
            <p class="text-gray-600 text-sm">Recover data from saved backups.</p>
        </div>

        <div class="p-4 overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-blue-100 text-gray-800">
                        <th class="px-3 py-2 w-10 border border-black">Select</th>
                        <th class="px-3 py-2 border border-black">#</th>
                        <th class="px-3 py-2 border border-black">Timestamp</th>
                        <th class="px-3 py-2 border border-black">Available Backups</th>
                        <th class="px-3 py-2 border border-black">Version</th>
                        <th class="px-3 py-2 border border-black">Size</th>
                        <th class="px-3 py-2 border border-black">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-3 py-2 border border-black"><input type="checkbox"></td>
                        <td class="px-3 py-2 border border-black">1</td>
                        <td class="px-3 py-2 border border-black">2024/05/04 14:23</td>
                        <td class="px-3 py-2 text-blue-700 border border-black">URS_CSS_v1_2024_05_04.zip</td>
                        <td class="px-3 py-2 border border-black">1.0</td>
                        <td class="px-3 py-2 border border-black">1.5 GB</td>
                        <td class="px-3 py-2 border border-black">
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 border border-black"><input type="checkbox"></td>
                        <td class="px-3 py-2 border border-black">2</td>
                        <td class="px-3 py-2 border border-black">2024/06/12 10:14</td>
                        <td class="px-3 py-2 text-blue-700 border border-black">URS_CSS_v1_2024_06_12.zip</td>
                        <td class="px-3 py-2 border border-black">2.0</td>
                        <td class="px-3 py-2 border border-black">1.8 GB</td>
                        <td class="px-3 py-2 border border-black">
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 border border-black"><input type="checkbox"></td>
                        <td class="px-3 py-2 border border-black">3</td>
                        <td class="px-3 py-2 border border-black">2024/08/14 03:15</td>
                        <td class="px-3 py-2 text-blue-700 border border-black">URS_CSS_v2_2024_08_14.zip</td>
                        <td class="px-3 py-2 border border-black">3.0</td>
                        <td class="px-3 py-2 border border-black">1.7 GB</td>
                        <td class="px-3 py-2 border border-black">
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">🗑</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 border border-black"><input type="checkbox"></td>
                        <td class="px-3 py-2 border border-black">4</td>
                        <td class="px-3 py-2 border border-black">2024/11/09 12:13</td>
                        <td class="px-3 py-2 text-blue-700 border border-black">URS_CSS_v2_2024_11_09.zip</td>
                        <td class="px-3 py-2 border border-black">4.0</td>
                        <td class="px-3 py-2 border border-black">1.6 GB</td>
                        <td class="px-3 py-2 border border-black">
                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">🗑</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Restore Button -->
        <div class="p-4 border-t border-black">
            <button class="bg-white-200 hover:bg-gray-300 px-4 py-2 rounded shadow-sm">
                Restore
            </button>
        </div>
    </div>

    <!-- Data Backup Section -->
    <div class="bg-transparent border border-black rounded-md">
        <div class="p-4 border-b border-black">
            <h2 class="text-lg font-semibold">Data Backup</h2>
            <p class="text-gray-600 text-sm">Create a new backup file of this system’s current version.</p>
        </div>

        <div class="p-4">
            <button class="bg-white-200 hover:bg-gray-300 px-4 py-2 rounded shadow-sm">
                Backup
            </button>
        </div>
    </div>

</div>
