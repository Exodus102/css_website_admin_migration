<?php
$quarters = [
    // Using quarter number as key for easier use in JavaScript
    1 => '1st Quarter',
    2 => '2nd Quarter',
    3 => '3rd Quarter',
    4 => '4th Quarter'
];
?>
<!-- Main container for the list of quarters -->
<div id="tally-list-container" class="p-4">
    <div>
        <span class="text-4xl font-bold font-sfpro">Tally Reports</span><br>
        <span>You are viewing the generated reports of available offices for this period.</span>
    </div>

    <?php include "filter.php"; ?>

    <div class="mt-4 overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-[#064089] text-white font-normal">
                <tr>
                    <th class="border border-[#1E1E1ECC] font-normal w-2/3">Quarter</th>
                    <th class="border border-[#1E1E1ECC] font-normal">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quarters as $q_num => $q_name) : ?>
                    <tr class="bg-white">
                        <td class="border border-[#1E1E1ECC] p-3"><?php echo htmlspecialchars($q_name); ?></td>
                        <td class="border border-[#1E1E1ECC] p-3 text-center flex justify-center gap-2">
                            <button data-quarter="<?php echo $q_num; ?>" class="view-report-btn bg-[#D9E2EC] flex gap-1 p-1 rounded-full w-24 justify-center text-[#064089] hover:bg-[#c2ccd6]"><img src="../../resources/svg/eye-icon.svg" alt="" srcset="">View</button>
                            <button class="download-report-btn bg-[#D9E2EC] flex gap-1 p-1 rounded-full w-28 justify-center text-[#064089] hover:bg-[#c2ccd6]"><img src="../../resources/svg/download-outline.svg" alt="" srcset="">Download</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Container for the dynamically loaded report, hidden by default -->
<div id="report-view-container" class="hidden p-4">
    <div class="mb-4 flex items-center">
        <button id="back-to-tally-list-btn" class="">
            <img src="../../resources/svg/back-arrow-rounded.svg" alt="Back" srcset="">
        </button>
        <div class="ml-4">
            <span id="report-title" class="text-2xl font-bold font-sfpro">Tally Report</span><br>
            <span id="report-period-text" class="font-normal text-base"></span>
        </div>
    </div>
    <div id="report-content" class="">
        <!-- Content from generate-report-tally.php will be loaded here -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tallyListContainer = document.getElementById('tally-list-container');
        const reportViewContainer = document.getElementById('report-view-container');
        const reportContent = document.getElementById('report-content');
        const backBtn = document.getElementById('back-to-tally-list-btn');
        const viewButtons = document.querySelectorAll('.view-report-btn');
        const yearFilter = document.getElementById('filter_year');

        viewButtons.forEach(button => {
            button.addEventListener('click', async (event) => {
                const quarter = event.currentTarget.dataset.quarter;
                const year = yearFilter.value;

                if (!year) {
                    alert('Please select a year from the filter first.');
                    yearFilter.focus();
                    return;
                }

                // Fetch the content from generate-report-tally.php
                const response = await fetch(`../../pages/tally-report/generate-report-tally.php?year=${year}&quarter=${quarter}`);
                const html = await response.text();
                reportContent.innerHTML = html;

                // Update the header text and switch views
                document.getElementById('report-period-text').textContent = `Viewing report for ${year}, Quarter ${quarter}`;
                tallyListContainer.classList.add('hidden');
                reportViewContainer.classList.remove('hidden');
            });
        });

        backBtn.addEventListener('click', () => {
            // Switch back to the list view
            reportViewContainer.classList.add('hidden');
            tallyListContainer.classList.remove('hidden');
            reportContent.innerHTML = ''; // Clear the content
        });
    });
</script>