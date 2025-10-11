<?php
require_once '../../function/_databaseConfig/_dbConfig.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$divisions = [];
$units_by_name = [];
$units = [];
$years = [];
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
<div class="p-4 w-full h-full">
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
    <div class="flex items-end mb-6 w-full justify-between">
        <div class="flex items-end gap-1">
            <span class="font-semibold text-gray-700">FILTERS:</span>

            <div class="w-72">
                <label for="filter_division" class="block text-xs font-medium text-[#48494A]">DIVISION</label>
                <select name="filter_division" id="filter_division" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC]">
                    <option value="">All Divisions</option>
                    <?php foreach ($divisions as $division) : ?>
                        <option value="<?php echo htmlspecialchars($division['id']); ?>"><?php echo htmlspecialchars($division['division_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-72">
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

        <!-- Upload CSV button -->
        <button id="upload-csv-btn" class="bg-[#D6D7DC] border border-[#1E1E1E] px-4 py-2 rounded shadow-sm text-sm flex items-center h-7">
            <img src="../../resources/svg/upload-data-window.svg" alt="" srcset="">
            Upload CSV
        </button>
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
    <dialog id="upload-csv-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
        <form id="upload-csv-form" method="POST" class="space-y-4">
            <h3 class="font-bold text-lg mb-4">Upload CSV File</h3>
            <p class="text-sm text-gray-600">Select a CSV file containing response data. Ensure the columns match the required format and order.</p>
            <div>
                <label for="csv-file-input" class="block text-sm font-medium text-gray-700">CSV File</label>
                <input type="file" id="csv-file-input" name="csv_file" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100" accept=".csv" required>
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <button type="button" id="cancel-upload-csv" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2 disabled:opacity-50">
                    <span id="upload-submit-text">Upload</span>
                </button>
            </div>
        </form>
    </dialog>

    <script>
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
            const csvFileInput = document.getElementById('csv-file-input');

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

            if (uploadCsvForm) {
                uploadCsvForm.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (!csvFileInput.files || csvFileInput.files.length === 0) {
                        alert('Please select a CSV file to upload.');
                        return;
                    }

                    const submitButton = uploadCsvForm.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.querySelector('#upload-submit-text').textContent = 'Uploading...';

                    const formData = new FormData(uploadCsvForm);

                    try {
                        const response = await fetch('../../function/_dataResponse/_uploadCsv.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        alert(result.message);
                        if (result.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        alert('An error occurred during the upload process. Please check the console for details.');
                        console.error('Upload Error:', error);
                    } finally {
                        submitButton.disabled = false;
                        submitButton.querySelector('#upload-submit-text').textContent = 'Upload';
                        uploadCsvDialog.close();
                    }
                });
            }
        });
    </script>