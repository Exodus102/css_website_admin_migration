<?php
require_once '../../function/_databaseConfig/_dbConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$divisions = [];
$units_by_name = [];
$units = [];
$years = [];
$active_questions = [];
$customer_types = [];
$user_campus = $_SESSION['user_campus'] ?? null;

try {
    // Fetch all divisions
    $stmtDivisions = $pdo->query("SELECT id, division_name FROM tbl_division ORDER BY division_name ASC");
    $divisions = $stmtDivisions->fetchAll(PDO::FETCH_ASSOC);

    // Fetch units for the user's campus, along with their division ID
    if ($user_campus) {
        $stmtUnits = $pdo->prepare("
            SELECT u.id, u.unit_name, d.id as division_id 
            FROM tbl_unit u 
            LEFT JOIN tbl_division d ON u.division_name = d.division_name
            WHERE u.campus_name = ? 
            ORDER BY u.unit_name ASC
        ");
        $stmtUnits->execute([$user_campus]);
        $units = $stmtUnits->fetchAll(PDO::FETCH_ASSOC);
        foreach ($units as $unit) {
            // Create a lookup map by unit name for efficient processing later
            $units_by_name[$unit['unit_name']] = $unit;
        }
    }

    // Fetch all customer types for the dropdown
    $stmtCustomerTypes = $pdo->query("SELECT customer_type FROM tbl_customer_type ORDER BY customer_type ASC");
    $customer_types = $stmtCustomerTypes->fetchAll(PDO::FETCH_COLUMN);

    // Fetch active, answerable questions for the "Add Response" dialog.
    // Includes Multiple Choice, Dropdown, and Text types, but excludes Description.
    $stmt_active_questions = $pdo->prepare("
        SELECT question_id, question 
        FROM tbl_questionaire 
        WHERE status = 1 AND question_type != 'Description' AND question_id != 1
        ORDER BY question_id ASC");
    $stmt_active_questions->execute();
    $active_questions = $stmt_active_questions->fetchAll(PDO::FETCH_ASSOC);

    // Step 1: Fetch raw response data, filtered by the user's campus.
    if ($user_campus) {
        // Fetch only the response_ids associated with the user's campus.
        $stmtResponses = $pdo->prepare("
            SELECT * FROM tbl_responses 
            WHERE response_id IN (
                SELECT response_id FROM tbl_responses WHERE question_id = -1 AND response = ?
            )
            ORDER BY response_id, id ASC
        ");
        $stmtResponses->execute([$user_campus]);
    } else {
        // Fallback for users without a specific campus (e.g., super admin) - fetch all.
        $stmtResponses = $pdo->query("SELECT * FROM tbl_responses WHERE response_id IS NOT NULL ORDER BY response_id, id ASC");
    }
    $responses_raw = $stmtResponses->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Process the raw data in PHP to correctly group and interpret it.
    $grouped_responses = [];
    foreach ($responses_raw as $row) {
        $response_id = $row['response_id'];
        // Initialize the group if it's the first time we see this response_id
        if (!isset($grouped_responses[$response_id])) {
            $grouped_responses[$response_id] = [
                'id' => $response_id,
                'timestamp' => $row['timestamp'],
                'comment' => $row['comment'],
                'analysis' => $row['analysis'],
                'campus' => null,
                'division_name' => null,
                'unit_name' => null,
                'customer_type' => null,
                'unit_id' => null, // Will be determined from metadata
                'division_id' => null, // Will be determined from metadata
                'responses' => [],
            ];
        }

        if ($row['question_id'] > 0) {
            // This is a real answer to a question
            $grouped_responses[$response_id]['responses'][] = $row['response'];
        } else {
            // This is metadata, so we assign it to the correct property
            switch ($row['question_id']) {
                case -1:
                    $grouped_responses[$response_id]['campus'] = $row['response'];
                    break;
                case -2:
                    $grouped_responses[$response_id]['division_name'] = $row['response'];
                    break;
                case -3:
                    $unit_name = $row['response'];
                    $grouped_responses[$response_id]['unit_name'] = $unit_name;
                    if (isset($units_by_name[$unit_name])) {
                        $grouped_responses[$response_id]['unit_id'] = $units_by_name[$unit_name]['id'];
                        $grouped_responses[$response_id]['division_id'] = $units_by_name[$unit_name]['division_id'];
                    }
                    break;
                case -4:
                    $grouped_responses[$response_id]['customer_type'] = $row['response'];
                    break;
            }
        }
    }

    // Get unique years from the data for the year filter
    $years = [];
    if (!empty($grouped_responses)) {
        $timestamps = array_column($grouped_responses, 'timestamp');
        $years = array_unique(array_map(function ($ts) {
            return date('Y', strtotime($ts));
        }, $timestamps));
        rsort($years); // Sort years in descending order (e.g., 2025, 2024)
    }

    $max_responses = 0;
    if (!empty($grouped_responses)) {
        // Find the maximum number of responses in any group to create table headers dynamically
        $max_responses = max(array_map('count', array_column($grouped_responses, 'responses')));
    }
} catch (PDOException $e) {
    error_log("Error fetching data for data-response: " . $e->getMessage());
}
?>
<div class="p-4 w-full lg:h-full">
    <script>
        // Apply saved font size on every page load
        (function() {
            const savedSize = localStorage.getItem('user_font_size');
            if (savedSize) {
                document.documentElement.style.fontSize = savedSize;
            }
        })();
    </script>
    <!-- Header -->
    <div class="mb-6 w-full">
        <h1 class="text-4xl font-bold text-[#1E1E1E]">Data Responses</h1>
        <p class="text-[#1E1E1E]">
            You are viewing the responses from the survey questionnaire currently in use.
        </p>
    </div>

    <!-- Filters -->
    <div class="flex xl:items-end mb-6 w-full justify-between flex-col xl:flex-row gap-4 xl:gap-0">
        <div class="flex lg:items-end gap-1 lg:flex-row flex-col">
            <span class="font-semibold text-gray-700">FILTERS:</span>

            <div class="lg:w-72 w-full">
                <label for="filter_division" class="block text-xs font-medium text-[#48494A]">DIVISION</label>
                <select name="filter_division" id="filter_division" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC]">
                    <option value="">All Divisions</option>
                    <?php foreach ($divisions as $division) : ?>
                        <option value="<?php echo htmlspecialchars($division['id']); ?>"><?php echo htmlspecialchars($division['division_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="lg:w-72 w-full">
                <label for="filter_unit" class="block text-xs font-medium text-[#48494A]">OFFICE</label>
                <select name="filter_unit" id="filter_unit" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC]">
                    <option value="">All Offices</option>
                    <?php foreach ($units as $unit) : ?>
                        <option value="<?php echo htmlspecialchars($unit['id']); ?>" data-division-id="<?php echo htmlspecialchars($unit['division_id'] ?? ''); ?>"><?php echo htmlspecialchars($unit['unit_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex-grow">
                <label for="filter_year" class="block text-xs font-medium text-[#48494A]">YEAR</label>
                <select name="filter_year" id="filter_year" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC]">
                    <option value="">All Years</option>
                    <?php foreach ($years as $year) : ?>
                        <option value="<?php echo htmlspecialchars($year); ?>"><?php echo htmlspecialchars($year); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex-grow">
                <label for="filter_quarter" class="block text-xs font-medium text-[#48494A]">QUARTER</label>
                <select name="filter_quarter" id="filter_quarter" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC]">
                    <option value="">All Quarters</option>
                    <option value="1">1st Quarter</option>
                    <option value="2">2nd Quarter</option>
                    <option value="3">3rd Quarter</option>
                    <option value="4">4th Quarter</option>
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Add Response button -->
            <button id="add-response-btn" class="bg-[#D6D7DC] border border-[#1E1E1E] px-4 py-2 rounded shadow-sm text-sm flex items-center h-7">
                Add Response
            </button>
            <!-- Upload CSV button -->
            <button id="upload-csv-btn" class="bg-[#D6D7DC] border border-[#1E1E1E] px-4 py-2 rounded shadow-sm text-sm flex items-center h-7">
                <img src="../../resources/svg/upload-data-window.svg" alt="Upload CSV" class="mr-2">
                Upload CSV
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-300 rounded-lg w-full overflow-x-auto">
        <table class="border-collapse w-full">
            <thead>
                <tr class="bg-[#064089] text-[#F1F7F9] text-sm font-semibold">
                    <th class="px-4 py-2 border border-gray-300">ID</th>
                    <th class="px-4 py-2 border border-gray-300">Timestamp</th>
                    <th class="px-4 py-2 border border-gray-300">Campus</th>
                    <th class="px-4 py-2 border border-gray-300">Division</th>
                    <th class="px-4 py-2 border border-gray-300">Office</th>
                    <th class="px-4 py-2 border border-gray-300">Customer Type</th>
                    <?php for ($i = 1; $i <= $max_responses; $i++) : ?>
                        <th class="px-4 py-2 border border-gray-300">Response <?php echo $i; ?></th>
                    <?php endfor; ?>
                    <th class="px-4 py-2 border border-gray-300">Comments & Suggestions</th>
                    <th class="px-4 py-2 border border-gray-300">Analysis</th>
                </tr>
            </thead>
            <tbody id="response-table-body">
                <?php if (empty($grouped_responses)) : ?>
                    <tr class="no-results-row">
                        <td colspan="<?php echo $max_responses + 8; ?>" class="px-4 py-3 border border-gray-300 text-center text-gray-500">No responses found.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($grouped_responses as $group) : ?>
                        <?php
                        $unit_id = $group['unit_id'] ?? null;
                        $division_id = $group['division_id'] ?? '';
                        ?>
                        <tr class="response-row" data-unit-id="<?php echo htmlspecialchars($unit_id); ?>" data-division-id="<?php echo htmlspecialchars($division_id); ?>" data-year="<?php echo date('Y', strtotime($group['timestamp'])); ?>" data-quarter="<?php echo ceil(date('n', strtotime($group['timestamp'])) / 3); ?>">
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['id']); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars(date('m/d/Y H:i:s', strtotime($group['timestamp']))); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['campus'] ?? ''); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['division_name'] ?? ''); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['unit_name'] ?? ''); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['customer_type'] ?? ''); ?></td>
                            <?php for ($i = 0; $i < $max_responses; $i++) : ?>
                                <td class="px-4 py-2 border border-gray-300 text-center">
                                    <?php echo isset($group['responses'][$i]) ? htmlspecialchars($group['responses'][$i]) : ''; ?>
                                </td>
                            <?php endfor; ?>
                            <td class="px-4 py-2 border border-gray-300 text-center"><?php echo htmlspecialchars($group['comment']); ?></td>
                            <td class="px-4 py-2 border border-gray-300 text-center">
                                <?php
                                $analysis = htmlspecialchars($group['analysis']);
                                $colorClass = 'bg-gray-400'; // Neutral/Default
                                if ($analysis === 'Positive' || $analysis === 'positive') $colorClass = 'bg-green-500';
                                if ($analysis === 'Negative' || $analysis === 'negative') $colorClass = 'bg-red-500';
                                ?>
                                <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo $colorClass; ?> text-white">
                                    <?php echo $analysis; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr class="no-results-row" style="display: none;">
                    <td colspan="<?php echo $max_responses + 8; ?>" class="px-4 py-3 border border-gray-300 text-center text-gray-500">No matching responses found.</td>
                </tr>
            </tbody>
        </table>
    </div>


    <!-- Pagination -->
    <div class="flex items-end gap-4 mt-4 text-sm">
        <!-- Previous -->
        <div>
            <button id="pagination-prev" class="border border-[#1E1E1E] py-1 px-3 rounded bg-[#E6E7EC] text-gray-500" disabled>
                &lt; Previous
            </button>
        </div>

        <!-- Current Page -->
        <div>
            <span id="pagination-current-page" class="inline-block text-center border border-[#1E1E1E] py-1 px-4 rounded bg-white">
                1
            </span>
        </div>

        <!-- Next -->
        <div>
            <button id="pagination-next" class="border border-[#1E1E1E] py-1 px-3 rounded bg-[#E6E7EC] text-gray-700">
                Next Page &gt;
            </button>
        </div>


    </div>

    <!-- Upload CSV Dialog -->
    <dialog id="upload-csv-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md bg-[#F1F7F9]">
        <form id="upload-csv-form" method="POST" class="space-y-4">
            <h3 class="font-bold text-lg mb-4 text-center">Upload CSV File</h3>
            <p class="text-sm text-gray-600 text-center">Select a CSV file containing response data. Ensure the columns match the required format and order.</p>

            <!-- Download Excel Template Link -->
            <div class="text-center">
                <a href="../../upload/csv-template/csv-template.xlsx" download="csv-template.xlsx" class="text-sm text-blue-600 hover:underline font-medium inline-flex items-center gap-1">
                    <img src="../../resources/svg/download-outline.svg" alt="Download" class="w-4 h-4">
                    Download Excel Template
                </a>
            </div>

            <div id="csv-drop-zone" class="flex flex-col items-center justify-center w-full">
                <label for="csv-file-input" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors bg-[#749DC8]/20">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                        </svg>
                        <p id="csv-drop-zone-text" class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                        <p class="text-xs text-gray-500">CSV files (MAX. 5MB)</p>
                    </div>
                    <input type="file" id="csv-file-input" name="csv_file" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                </label>
            </div>
            <!-- Buttons removed for automatic upload -->
            <button type="button" id="cancel-upload-csv" class="hidden">Cancel</button> <!-- Hidden but kept for programmatic closing if needed -->
        </form>
    </dialog>

    <!-- Add Response Dialog -->
    <dialog id="add-response-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-7xl bg-[#F1F7F9]">
        <form id="add-response-form" method="POST" class="space-y-4">
            <h3 class="font-bold text-lg mb-4 text-center">Add New Response</h3>
            <div class="max-h-[60vh] overflow-x-auto p-1 border rounded-md bg-white">
                <table class="min-w-full border-collapse">
                    <thead class="bg-gray-100 sticky top-0 z-10">
                        <tr id="add-response-header">
                            <th class="p-2 text-left font-semibold text-gray-700 border whitespace-nowrap min-w-[200px] break-words align-top">Division</th>
                            <th class="p-2 text-left font-semibold text-gray-700 border whitespace-nowrap min-w-[200px] break-words align-top">Office</th>
                            <th class="p-2 text-left font-semibold text-gray-700 border whitespace-nowrap min-w-[200px] break-words align-top">Customer Type</th>
                            <th class="p-2 text-left font-semibold text-gray-700 border whitespace-nowrap min-w-[200px] break-words align-top">Purpose of Visit</th>
                            <?php foreach ($active_questions as $question) : ?>
                                <th class="p-2 text-left font-semibold text-gray-700 border whitespace-nowrap min-w-[200px] break-words align-top"><?php echo htmlspecialchars($question['question']); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="add-response-body">
                        <?php if (empty($active_questions)) : ?>
                            <tr>
                                <td class="p-4 text-center text-gray-500">No active, answerable questions found to create a response.</td>
                            </tr>
                        <?php else : ?>
                            <tr class="response-entry-row">
                                <td class="p-1 border align-top">
                                    <select name="answers[0][-2]" class="response-division-select w-full rounded px-2 py-1 h-full bg-white border border-gray-300" required>
                                        <option value="" hidden>Select Division</option>
                                        <?php foreach ($divisions as $division) : ?>
                                            <option value="<?php echo htmlspecialchars($division['division_name']); ?>"><?php echo htmlspecialchars($division['division_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-1 border align-top">
                                    <select name="answers[0][-3]" class="response-office-select w-full rounded px-2 py-1 h-full bg-white border border-gray-300" required>
                                        <option value="" hidden>Select Office</option>
                                    </select>
                                </td>
                                <td class="p-1 border align-top">
                                    <select name="answers[0][-4]" class="w-full rounded px-2 py-1 h-full bg-white border border-gray-300" required>
                                        <option value="" hidden>Select Customer Type</option>
                                        <?php foreach ($customer_types as $type) : ?>
                                            <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="p-1 border align-top">
                                    <input type="text" name="answers[0][1]" class="w-full border-gray-300 rounded px-2 py-1 h-full">
                                </td>
                                <?php foreach ($active_questions as $question) : ?>
                                    <td class="p-1 border align-top"><input type="text" name="answers[0][<?php echo $question['question_id']; ?>]" class="w-full border-gray-300 rounded px-2 py-1 h-full"></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-between items-center">
                <button type="button" id="add-new-response-row" class="px-4 py-2 bg-green-100 text-green-800 rounded shadow-sm text-sm hover:bg-green-200 font-semibold" <?php echo empty($active_questions) ? 'disabled' : ''; ?>>+ Add New Row</button>
                <div>
                    <button type="button" id="cancel-add-response" class="px-4 py-2 bg-[#D6D7DC] border border-[#1E1E1E] rounded shadow-sm text-sm hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-[#064089] text-white rounded shadow-sm text-sm hover:bg-blue-700" <?php echo empty($active_questions) ? 'disabled' : ''; ?>>Save Response</button>
                </div>
            </div>
        </form>
    </dialog>

    <script>
        // Expose PHP data to JavaScript
        const allUnitsForAddResponse = <?php echo json_encode($units_by_name); ?>;

        document.addEventListener('DOMContentLoaded', () => {
            const divisionFilter = document.getElementById('filter_division');
            const unitFilter = document.getElementById('filter_unit');
            const yearFilter = document.getElementById('filter_year');
            const quarterFilter = document.getElementById('filter_quarter');
            const tableBody = document.getElementById('response-table-body');

            // Pagination elements
            const prevButton = document.getElementById('pagination-prev');
            const nextButton = document.getElementById('pagination-next');
            const pageSpan = document.getElementById('pagination-current-page');
            let currentPage = 1;
            const rowsPerPage = 9;
            let filteredRows = [];

            // Store all original unit options to avoid re-querying the DOM
            const allUnitOptions = Array.from(unitFilter.querySelectorAll('option'));

            // --- CSV Upload Logic ---
            const uploadCsvBtn = document.getElementById('upload-csv-btn');
            const uploadCsvDialog = document.getElementById('upload-csv-dialog');
            const uploadCsvForm = document.getElementById('upload-csv-form');
            const cancelUploadCsvBtn = document.getElementById('cancel-upload-csv');
            const csvDropZone = document.getElementById('csv-drop-zone');
            const csvDropZoneText = document.getElementById('csv-drop-zone-text');
            const csvFileInput = document.getElementById('csv-file-input');

            // --- Add Response Logic ---
            const addResponseBtn = document.getElementById('add-response-btn');
            const addResponseDialog = document.getElementById('add-response-dialog');
            const cancelAddResponseBtn = document.getElementById('cancel-add-response');
            const addResponseForm = document.getElementById('add-response-form');
            const addNewRowBtn = document.getElementById('add-new-response-row');

            /**
             * Filters the Office dropdown based on the selected Division.
             */
            const filterOfficeDropdown = () => {
                const selectedDivisionId = divisionFilter.value;

                // Reset the office filter selection
                unitFilter.value = '';

                // Show/hide options in the office dropdown
                allUnitOptions.forEach(option => {
                    // Always show the placeholder "All Offices" option
                    if (!option.value) {
                        option.style.display = '';
                        return;
                    }

                    const optionDivisionId = option.dataset.divisionId;
                    // Show if no division is selected or if the division matches
                    option.style.display = (!selectedDivisionId || optionDivisionId === selectedDivisionId) ? '' : 'none';
                });
            };

            const updatePaginationControls = () => {
                const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
                pageSpan.textContent = String(currentPage).padStart(2, '0');

                prevButton.disabled = currentPage === 1;
                nextButton.disabled = currentPage === pageCount || pageCount === 0;

                // Style disabled buttons
                prevButton.classList.toggle('text-gray-500', prevButton.disabled);
                prevButton.classList.toggle('text-gray-700', !prevButton.disabled);
                nextButton.classList.toggle('text-gray-500', nextButton.disabled);
                nextButton.classList.toggle('text-gray-700', !nextButton.disabled);
            };

            const displayPage = (page) => {
                const allRows = tableBody.querySelectorAll('tr.response-row');
                allRows.forEach(row => row.style.display = 'none');

                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const pageRows = filteredRows.slice(startIndex, endIndex);

                pageRows.forEach(row => {
                    row.style.display = '';
                });

                updatePaginationControls();
            };

            /**
             * Filters the main response table based on all active filters.
             */
            const filterTable = () => {
                const selectedDivisionId = divisionFilter.value;
                const selectedUnitId = unitFilter.value;
                const selectedYear = yearFilter.value;
                const selectedQuarter = quarterFilter.value;

                const allRows = tableBody.querySelectorAll('tr.response-row');
                const noResultsRow = tableBody.querySelector('tr.no-results-row');

                filteredRows = []; // Reset the list of filtered rows

                allRows.forEach(row => {
                    const rowDivisionId = row.dataset.divisionId;
                    const rowUnitId = row.dataset.unitId;
                    const rowYear = row.dataset.year;
                    const rowQuarter = row.dataset.quarter;

                    const divisionMatch = !selectedDivisionId || rowDivisionId === selectedDivisionId;
                    const unitMatch = !selectedUnitId || rowUnitId === selectedUnitId;
                    const yearMatch = !selectedYear || rowYear === selectedYear;
                    const quarterMatch = !selectedQuarter || rowQuarter === selectedQuarter;

                    if (divisionMatch && unitMatch && yearMatch && quarterMatch) {
                        filteredRows.push(row);
                    }
                });

                noResultsRow.style.display = filteredRows.length === 0 ? '' : 'none';

                currentPage = 1; // Reset to the first page after filtering
                displayPage(currentPage);
            };

            // --- Event Listeners ---
            divisionFilter.addEventListener('change', () => {
                filterOfficeDropdown();
                filterTable();
            });
            [unitFilter, yearFilter, quarterFilter].forEach(el => el.addEventListener('change', filterTable));

            nextButton.addEventListener('click', () => {
                const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
                if (currentPage < pageCount) {
                    currentPage++;
                    displayPage(currentPage);
                }
            });

            prevButton.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayPage(currentPage);
                }
            });

            // Initial load
            filterTable();

            // --- CSV Upload Event Listeners ---
            if (uploadCsvBtn) {
                uploadCsvBtn.addEventListener('click', () => uploadCsvDialog.showModal());
            }
            if (cancelUploadCsvBtn) {
                cancelUploadCsvBtn.addEventListener('click', () => uploadCsvDialog.close());
            }
            if (uploadCsvDialog) {
                uploadCsvDialog.addEventListener('click', (e) => {
                    if (e.target === uploadCsvDialog) {
                        uploadCsvDialog.close();
                    }
                });
            }

            const handleCsvUpload = async () => {
                if (!csvFileInput.files || csvFileInput.files.length === 0) {
                    // This case should ideally not be hit with auto-upload, but it's good practice.
                    alert('Please select a CSV file to upload.');
                    return;
                }

                // Update UI to show upload is in progress
                csvDropZoneText.innerHTML = `<span class="font-semibold text-blue-600">Uploading ${csvFileInput.files[0].name}...</span>`;

                const formData = new FormData(uploadCsvForm);

                try {
                    const response = await fetch('../../function/_dataResponse/_uploadCsv.php', {
                        method: 'POST',
                        body: formData
                    });
                    const result = await response.json();
                    alert(result.message); // Show success or error message from server
                    if (result.success) {
                        window.location.reload(); // Reload the page on successful upload
                    }
                } catch (error) {
                    alert('An error occurred during the upload process. Please check the console for details.');
                    console.error('Upload Error:', error);
                    // Reset the text on error
                    csvDropZoneText.innerHTML = `<span class="font-semibold">Click to upload</span> or drag and drop`;
                } finally {
                    // Close the dialog regardless of outcome
                    uploadCsvDialog.close();
                }
            };


            // --- Drag and Drop Logic ---
            if (csvDropZone && csvFileInput && csvDropZoneText) {
                const preventDefaults = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                };

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    csvDropZone.addEventListener(eventName, preventDefaults, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    csvDropZone.addEventListener(eventName, () => {
                        csvDropZone.querySelector('label').classList.add('border-blue-500', 'bg-blue-50');
                    }, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    csvDropZone.addEventListener(eventName, () => {
                        csvDropZone.querySelector('label').classList.remove('border-blue-500', 'bg-blue-50');
                    }, false);
                });

                csvDropZone.addEventListener('drop', (e) => {
                    const dt = e.dataTransfer;
                    const files = dt.files;
                    if (files.length > 0) {
                        csvFileInput.files = files;
                        // Manually trigger change event for any listeners
                        csvFileInput.dispatchEvent(new Event('change'));
                    }
                }, false);

                csvFileInput.addEventListener('change', () => {
                    if (csvFileInput.files.length > 0) {
                        csvDropZoneText.innerHTML = `<span class="font-semibold text-green-600">${csvFileInput.files[0].name}</span> selected`;
                        // Automatically trigger the upload
                        handleCsvUpload();
                    } else {
                        csvDropZoneText.innerHTML = `<span class="font-semibold">Click to upload</span> or drag and drop`;
                    }
                });
            }

            // --- Add Response Event Listeners ---
            if (addResponseBtn) {
                addResponseBtn.addEventListener('click', () => addResponseDialog.showModal());
            }
            if (cancelAddResponseBtn) {
                cancelAddResponseBtn.addEventListener('click', () => addResponseDialog.close());
            }
            if (addResponseDialog) {
                addResponseDialog.addEventListener('click', (e) => {
                    if (e.target === addResponseDialog) {
                        addResponseDialog.close();
                    }
                });
            }

            // --- Dynamic Office Dropdown for "Add Response" Dialog ---
            const addResponseBody = document.getElementById('add-response-body');
            if (addResponseBody) {
                addResponseBody.addEventListener('change', (e) => {
                    if (e.target && e.target.classList.contains('response-division-select')) {
                        const selectedDivisionName = e.target.value;
                        const row = e.target.closest('tr');
                        const officeSelect = row.querySelector('.response-office-select');

                        // Clear existing options
                        officeSelect.innerHTML = '<option value="" hidden>Select Office</option>';

                        // Populate with units that match the selected division
                        for (const unitName in allUnitsForAddResponse) {
                            const unitData = allUnitsForAddResponse[unitName];
                            // Find the division name from the main divisions array
                            const divisionInfo = <?php echo json_encode($divisions); ?>.find(d => d.id === unitData.division_id);
                            if (divisionInfo && divisionInfo.division_name === selectedDivisionName) {
                                const option = new Option(unitName, unitName);
                                officeSelect.appendChild(option);
                            }
                        }
                    }
                });
            }

            // --- Add New Row for Response ---
            if (addNewRowBtn) {
                addNewRowBtn.addEventListener('click', () => {
                    const tableBody = document.getElementById('add-response-body');
                    const firstRow = tableBody.querySelector('tr.response-entry-row');
                    if (!firstRow) return;

                    const newRow = firstRow.cloneNode(true);
                    const newIndex = tableBody.querySelectorAll('tr.response-entry-row').length;

                    // Reset office dropdown
                    const officeSelect = newRow.querySelector('.response-office-select');
                    if (officeSelect) {
                        officeSelect.innerHTML = '<option value="" hidden>Select Office</option>';
                    }

                    // Clear input/select values and update names for the new row
                    newRow.querySelectorAll('input, select').forEach(input => {
                        if (input.type !== 'select-one') input.value = '';
                        input.value = '';
                        // Update the name attribute to reflect the new row index
                        input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                    });

                    tableBody.appendChild(newRow);
                });
            }

            if (addResponseForm) {
                addResponseForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const rowCount = addResponseForm.querySelectorAll('tbody tr').length;
                    if (!confirm(`Are you sure you want to add these ${rowCount} response(s)?`)) {
                        return;
                    }

                    const responseRows = addResponseForm.querySelectorAll('tbody tr.response-entry-row');

                    try {
                        // Process each row as a separate response, sending one request per row.
                        for (const row of responseRows) {
                            const rowData = new FormData();
                            const fields = row.querySelectorAll('input[name^="answers"], select[name^="answers"]');

                            // Manually add the campus data, which isn't in the form grid.
                            rowData.append('answers[-1]', '<?php echo $user_campus; ?>');

                            // Collect all answers for the current row
                            fields.forEach(field => {
                                // The name is "answers[ROW_INDEX][QUESTION_ID]"
                                const match = field.name.match(/\[\d+\]\[(-?\d+)\]/);
                                if (match && match[1]) {
                                    const questionId = match[1];
                                    const answer = field.value;
                                    // Append to FormData using array syntax for PHP
                                    rowData.append(`answers[${questionId}]`, answer);
                                }
                            });

                            // Send one request for the entire row
                            const response = await fetch('../../function/_dataResponse/_addManualResponse.php', {
                                method: 'POST',
                                body: rowData
                            });

                            if (!response.ok) {
                                // If any row fails, stop and report the error.
                                throw new Error(`Server responded with status: ${response.status}`);
                            }
                        }

                        alert(`${rowCount} response(s) added successfully!`);
                        window.location.reload();
                    } catch (error) {
                        alert('An error occurred while saving the responses. Please check the console.');
                        console.error('Add Response Error:', error);
                    }
                });
            }
        });
    </script>