<div class="p-4" id="reports-list-container">
    <span class="text-4xl font-bold font-sfpro">Reports</span><br>
    <span class="">You are viewing the generated reports of available offices for this period.</span>

    <?php include "filters.php"; ?><br>
    <div>
        <table class="w-full">
            <thead class="bg-[#064089] text-white font-normal">
                <tr>
                    <th class="border border-[#1E1E1ECC] font-normal">Office</th>
                    <th class="border border-[#1E1E1ECC] font-normal">Action</th>
                </tr>
            </thead>
            <tbody id="reports-table-body">
                <?php if (!empty($units)) : ?>
                    <?php foreach ($units as $unit) : ?>
                        <tr class="bg-[#F1F7F9] office-row" data-unit-id="<?php echo htmlspecialchars($unit['id']); ?>" data-division-id="<?php echo htmlspecialchars($unit['division_id'] ?? ''); ?>">
                            <td class="border border-[#1E1E1ECC] p-2"><?php echo htmlspecialchars($unit['unit_name']); ?></td>
                            <td class="border border-[#1E1E1ECC] p-2 text-center flex gap-2 justify-center">
                                <button class="view-report-btn bg-[#D9E2EC] flex gap-1 p-1 rounded-full w-24 justify-center text-[#064089]"><img src="../../resources/svg/eye-icon.svg" alt="" srcset="">View</button>
                                <button class="download-report-btn bg-[#D9E2EC] flex gap-1 p-1 rounded-full w-28 justify-center text-[#064089]"><img src="../../resources/svg/download-outline.svg" alt="" srcset="">Download</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr class="bg-[#F1F7F9]">
                        <td colspan="2" class="text-center border border-[#1E1E1ECC] p-2">No offices found for your campus.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for notifications -->
<div id="notification-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full">
                <!-- Icon will be inserted here by JS -->
            </div>
            <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900"></h3>
            <div class="mt-2 px-7 py-3">
                <p id="modal-message" class="text-sm text-gray-500"></p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="modal-close-btn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="report-view-container" class="hidden p-4">
    <div class="mb-4 flex items-center">
        <button id="back-to-reports-list-btn" class="">
            <img src="../../resources/svg/back-arrow-rounded.svg" alt="" srcset="">
        </button>
        <div class="ml-4">
            <span id="report-office-name" class="text-2xl font-bold font-sfpro">Office Name</span><br>
            <span id="report-quarter-text" class="font-normal text-base">2024 4th Quarter CSS Report</span>
        </div>
    </div>
    <div id="report-content"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const divisionFilter = document.getElementById('filter_division');
        const unitFilter = document.getElementById('filter_unit');

        const tableBody = document.getElementById('reports-table-body');
        const allOfficeRows = Array.from(tableBody.querySelectorAll('tr.office-row'));

        const reportsListContainer = document.getElementById('reports-list-container');
        const reportViewContainer = document.getElementById('report-view-container');
        const reportContent = document.getElementById('report-content');
        const backToReportsListBtn = document.getElementById('back-to-reports-list-btn');

        // Modal elements
        const modal = document.getElementById('notification-modal');
        const modalIconContainer = document.getElementById('modal-icon');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalCloseBtn = document.getElementById('modal-close-btn');

        const allUnitOptions = Array.from(unitFilter.querySelectorAll('option'));

        // --- Create a "no results" row for the table ---
        let noResultsRow = tableBody.querySelector('.no-results-row');
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.className = 'no-results-row';
            noResultsRow.style.display = 'none';
            noResultsRow.innerHTML = `<td colspan="2" class="bg-[#F1F7F9] text-center border border-[#1E1E1ECC] p-2">No matching offices found.</td>`;
            tableBody.appendChild(noResultsRow);
        }

        /**
         * Filters the Office dropdown based on the selected Division.
         */
        const filterOfficeDropdown = () => {
            const selectedDivisionId = divisionFilter.value;

            // Reset the office filter selection
            unitFilter.value = '';

            // Show/hide options in the office dropdown
            allUnitOptions.forEach(option => {
                // Always show the placeholder "Office" option
                if (!option.value) {
                    option.style.display = '';
                    return;
                }

                const optionDivisionId = option.dataset.divisionId;
                // Show if no division is selected or if the division matches
                option.style.display = (!selectedDivisionId || optionDivisionId === selectedDivisionId) ? '' : 'none';
            });
        };

        /**
         * Filters the main reports table based on all active filters.
         */
        const filterTable = () => {
            const selectedDivisionId = divisionFilter.value;
            const selectedUnitId = unitFilter.value;
            let visibleRowCount = 0;

            allOfficeRows.forEach(row => {
                const rowDivisionId = row.dataset.divisionId;
                const rowUnitId = row.dataset.unitId;

                const divisionMatch = !selectedDivisionId || rowDivisionId === selectedDivisionId;
                const unitMatch = !selectedUnitId || rowUnitId === selectedUnitId;

                if (divisionMatch && unitMatch) {
                    row.style.display = '';
                    visibleRowCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            noResultsRow.style.display = visibleRowCount === 0 ? '' : 'none';
        };

        // --- Event Listeners ---
        divisionFilter.addEventListener('change', () => {
            filterOfficeDropdown();
            filterTable(); // Also filter the table when division changes
        });

        unitFilter.addEventListener('change', filterTable);

        // --- Modal Logic ---
        const showModal = (isSuccess, message) => {
            if (!modal) return;

            // Clear previous state
            modalIconContainer.innerHTML = '';
            modalIconContainer.classList.remove('bg-green-100', 'bg-red-100');

            if (isSuccess) {
                modalTitle.textContent = 'Success!';
                modalIconContainer.classList.add('bg-green-100');
                modalIconContainer.innerHTML = '<svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            } else {
                modalTitle.textContent = 'Error!';
                modalIconContainer.classList.add('bg-red-100');
                modalIconContainer.innerHTML = '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            }

            modalMessage.textContent = message;
            modal.classList.remove('hidden');
        };

        modalCloseBtn.addEventListener('click', () => {
            if (modal) {
                modal.classList.add('hidden');
            }
        });

        // --- View Report Logic ---
        const loadReportView = async (unitId, officeName, quarter, year, quarterDisplayText) => {
            if (!reportsListContainer || !reportViewContainer || !reportContent || !unitId || !officeName || !quarterDisplayText) return;

            // Construct URL with the correct path from the project root
            const url = `../../pages/reports/view-report.php?unit_id=${unitId}&quarter=${quarter}&year=${year}`;

            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const html = await response.text();
                reportContent.innerHTML = html;

                // Update the office name in the header
                const reportOfficeName = document.getElementById('report-office-name');
                if (reportOfficeName) {
                    reportOfficeName.textContent = officeName;
                }

                // Update the quarter text in the header
                const reportQuarterText = document.getElementById('report-quarter-text');
                if (reportQuarterText) {
                    reportQuarterText.textContent = `${quarterDisplayText} CSS Report`;
                }

                // Switch views
                reportsListContainer.classList.add('hidden');
                reportViewContainer.classList.remove('hidden');
            } catch (error) {
                console.error('Error loading report view:', error);
                reportContent.innerHTML = '<p class="text-red-500">Failed to load the report. Please try again.</p>';
            }
        };

        tableBody.addEventListener('click', async (event) => {
            const viewButton = event.target.closest('.view-report-btn');
            if (viewButton) {
                const row = viewButton.closest('tr.office-row');
                const unitId = row.dataset.unitId;
                const officeName = row.querySelector('td').textContent.trim();

                const quarterFilter = document.getElementById('filter_quarter');
                const year = new Date().getFullYear();
                let quarterValue = quarterFilter.value;
                let quarterDisplay = '';

                if (quarterValue) {
                    // A quarter is selected from the dropdown
                    quarterDisplay = `${year} ${quarterFilter.options[quarterFilter.selectedIndex].text}`;
                } else {
                    // No quarter selected, so we calculate the current one
                    const currentMonth = new Date().getMonth(); // 0-11
                    const currentQuarter = Math.floor(currentMonth / 3) + 1;
                    quarterValue = currentQuarter;

                    let suffix = 'th';
                    if (currentQuarter === 1) suffix = 'st';
                    else if (currentQuarter === 2) suffix = 'nd';
                    else if (currentQuarter === 3) suffix = 'rd';

                    quarterDisplay = `${year} ${currentQuarter}${suffix} Quarter`;
                }

                // --- New Flow: Generate first, then view ---
                try {
                    const generateUrl = `../../pages/reports/generate-report.php?unit_id=${unitId}&quarter=${quarterValue}&year=${year}`;
                    const response = await fetch(generateUrl);
                    const result = await response.json();

                    if (result.success) {
                        // On success, show the success modal and then load the viewer
                        loadReportView(unitId, officeName, quarterValue, year, quarterDisplay);
                    } else {
                        // On failure, show the error modal with the message from the server
                    }
                } catch (error) {
                    console.error('Error during PDF generation request:', error);
                    showModal(false, 'A network error occurred. Could not contact the server.');
                }
            }
        });

        backToReportsListBtn.addEventListener('click', () => {
            reportViewContainer.classList.add('hidden');
            reportsListContainer.classList.remove('hidden');
            reportContent.innerHTML = ''; // Clear content
        });
    });
</script>