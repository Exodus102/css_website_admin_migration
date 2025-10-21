<?php
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

// Determine the campus to display. Prioritize GET parameter, then session, then default to null.
$selected_campus = $_GET['campus'] ?? $_SESSION['user_campus'] ?? null;

$all_campuses = [];

$pending_ncar_count = 0;
$respondents_count = 0;
$office_labels = [];
$office_data = [];
$pie_labels = [];
$pie_data = [];
$pie_chart_legend_data = [];
$active_survey_version = 'N/A';

try {
    // Fetch all campuses for the dropdown
    $stmt_all_campuses = $pdo->query("SELECT campus_name FROM tbl_campus ORDER BY campus_name ASC");
    $all_campuses = $stmt_all_campuses->fetchAll(PDO::FETCH_COLUMN);

    if ($selected_campus) {
        // Sanitize the campus name to match the format used in the NCAR file paths
        $safe_campus_name = preg_replace('/[\s\/\\?%*:|"<>]+/', '-', $selected_campus);
        $pattern = 'upload/pdf/ncar-report_' . $safe_campus_name . '_%';

        // Count NCARs for the user's campus that are not 'Resolved'
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_ncar WHERE file_path LIKE ? AND status != 'Resolved'");
        $stmt->execute([$pattern]);
        $pending_ncar_count = $stmt->fetchColumn();

        // Count unique respondents for the user's campus
        $stmt_respondents = $pdo->prepare("
            SELECT COUNT(DISTINCT response_id) 
            FROM tbl_responses 
            WHERE question_id = -1 AND response = ?
        ");
        $stmt_respondents->execute([$selected_campus]);
        $respondents_count = $stmt_respondents->fetchColumn();

        // Fetch monthly response data per office for the bar chart
        $stmt_chart = $pdo->prepare("
            SELECT 
                u.unit_name, 
                COUNT(DISTINCT r.response_id) as response_count
            FROM tbl_unit u
            LEFT JOIN tbl_responses r ON u.unit_name = r.response 
                AND r.question_id = -3 
                AND YEAR(r.timestamp) = YEAR(CURDATE())
                AND MONTH(r.timestamp) = MONTH(CURDATE())
            WHERE u.campus_name = :selected_campus
            GROUP BY u.id, u.unit_name
            ORDER BY u.unit_name ASC
        ");
        $stmt_chart->execute([':selected_campus' => $selected_campus]);
        while ($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)) {
            $office_labels[] = $row['unit_name'];
            $office_data[] = (int)$row['response_count'];
        }

        // Fetch user type data for the pie chart
        $pie_colors = ['#064089', '#6497B1', '#B3CDE0', '#324D3E', '#8EA48B', '#BECFBC'];
        $stmt_pie = $pdo->prepare("
            SELECT type, COUNT(*) as user_count 
            FROM credentials 
            WHERE campus = :selected_campus 
            GROUP BY type
            ORDER BY type ASC
        ");
        $stmt_pie->execute([':selected_campus' => $selected_campus]);
        $user_types = $stmt_pie->fetchAll(PDO::FETCH_ASSOC);

        $color_index = 0;
        foreach ($user_types as $type) {
            $pie_labels[] = $type['type'];
            $pie_data[] = (int)$type['user_count'];
            $pie_chart_legend_data[] = [
                'label' => $type['type'],
                'color' => $pie_colors[$color_index % count($pie_colors)]
            ];
            $color_index++;
        }
    }
    // Fetch active survey version (this is not campus-dependent)
    $stmt_survey = $pdo->query("SELECT question_survey FROM tbl_questionaire WHERE status = 1 LIMIT 1");
    $survey_name = $stmt_survey->fetchColumn();
    if ($survey_name) {
        // Try to extract a version number like 'v6.0' from the full name
        if (preg_match('/(v\d+(\.\d+)*)/', $survey_name, $matches)) {
            $active_survey_version = $matches[0];
        } else {
            // If no version pattern is found, use the full name as a fallback
            $active_survey_version = $survey_name;
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching dashboard counts: " . $e->getMessage());
}
?>
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
    <!-- Main Dashboard Content -->
    <div class="w-full">
        <!-- Welcome Section -->
        <div class="">
            <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['user_first_name'] ?? 'User'); ?>!</h1>
            <p class="">Gain real-time insights, track system status, and monitor key metrics to ensure total satisfaction.</p>
        </div>

        <!-- Key Metrics Cards and Charts -->
        <div class="flex flex-col lg:flex-row gap-6 shadow-around mt-6 lg:w-2/3">

            <!-- Left Column: Metrics and Bar Char -->
            <div class="flex flex-col w-full">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">Pending NCAR</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold mt-2"><?php echo htmlspecialchars($pending_ncar_count); ?></p>
                    </div>
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">CSS Respondents</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <p class="text-4xl font-bold mt-2"><?php echo htmlspecialchars(number_format($respondents_count)); ?></p>
                    </div>
                    <div class="bg-[#CFD8E5] rounded-lg p-4 shadow-2xl flex flex-col justify-between">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg">Survey Version</h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <span class="inline-block text-4xl font-bold px-3 py-1 mt-2 rounded-lg"><?php echo htmlspecialchars($active_survey_version); ?></span>
                    </div>
                </div>

                <!-- Monthly Responses Chart -->
                <div class="bg-[#CFD8E5] rounded-lg p-6 shadow-2xl w-full">
                    <div class="flex justify-between items-center">
                        <h2 class="text-3xl mb-2">Monthly Responses</h2>
                        <form id="campus-filter-form" method="GET">
                            <input type="hidden" name="page" value="dashboard-css-head">
                            <select name="campus" id="campus-filter" class="border border-gray-400 rounded-md p-1 text-sm">
                                <option value="">Select Campus</option>
                                <?php foreach ($all_campuses as $campus_option) : ?>
                                    <option value="<?php echo htmlspecialchars($campus_option); ?>" <?php echo ($selected_campus == $campus_option) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($campus_option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <div id="scroll-container" class="overflow-x-auto w-full no-scrollbar cursor-grab active:cursor-grabbing">
                        <div class="relative h-64" id="barChartContainer">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: User Types Pie Chart -->
            <div class="lg:w-2/5 bg-[#CFD8E5] rounded-lg p-6 shadow-2xl flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold ">User Types</h2>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
                <?php
                // Set the timezone to Philippine time
                date_default_timezone_set('Asia/Manila');
                // Display the current date and time
                ?>
                <p class="text-xs mb-4">As of <?php echo date('F j, Y \a\t h:i A'); ?></p>
                <div class="flex-grow flex items-center justify-center min-h-0">
                    <div class="relative w-64 h-64 lg:w-28 lg:h-28 xl:w-64 xl:h-64">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                <!-- Legend for Pie Chart (you'd generate this dynamically with Chart.js) -->
                <?php if (!empty($pie_chart_legend_data)) : ?>
                    <div class="mt-4 text-sm text-gray-600 flex justify-center gap-6">
                        <?php
                        // Split the legend data into two columns for better layout
                        $midpoint = ceil(count($pie_chart_legend_data) / 2);
                        $columns = array_chunk($pie_chart_legend_data, $midpoint);
                        ?>
                        <?php foreach ($columns as $column) : ?>
                            <div>
                                <?php foreach ($column as $item) : ?>
                                    <div class="flex items-center mb-1">
                                        <span class="w-3 h-3 rounded-full mr-2" style="background-color: <?php echo htmlspecialchars($item['color']); ?>"></span>
                                        <?php echo htmlspecialchars($item['label']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Data from PHP ---
        const officeLabels = <?php echo json_encode($office_labels); ?>;
        const officeData = <?php echo json_encode($office_data); ?>;
        const pieLabels = <?php echo json_encode($pie_labels); ?>;
        const pieData = <?php echo json_encode($pie_data); ?>;

        // --- Campus Filter Logic ---
        const campusFilter = document.getElementById('campus-filter');
        if (campusFilter) {
            campusFilter.addEventListener('change', () => {
                document.getElementById('campus-filter-form').submit();
            });
        }

        // Bar Chart for Monthly Responses
        const barCtx = document.getElementById('barChart');
        if (barCtx) {
            const officeLabels = <?php echo json_encode($office_labels); ?>;
            const officeData = <?php echo json_encode($office_data); ?>;
            const barChartContainer = document.getElementById('barChartContainer');

            const barCount = officeLabels.length;
            const barAndGapWidth = 300; // pixels per bar (controls scroll amount)
            const chartWidth = Math.max(barCount * barAndGapWidth, barChartContainer.parentElement.clientWidth);
            barChartContainer.style.width = `${chartWidth}px`;

            new Chart(barCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: officeLabels,
                    datasets: [{
                        label: 'Responses',
                        data: officeData,
                        backgroundColor: '#064089',
                        borderColor: '#064089',
                        borderWidth: 1,
                        barPercentage: 0.5, // A more standard bar width
                        categoryPercentage: 0.7 // A more standard gap
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0,
                                color: '#1E1E1E',
                                font: {
                                    size: 12,
                                    family: 'Arial'
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            ticks: {
                                stepSize: 10,
                                color: '#1E1E1E'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }


        // Pie Chart for User Types
        const pieCtx = document.getElementById('pieChart');
        if (pieCtx) {
            new Chart(pieCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        label: 'User Types',
                        data: pieData,
                        backgroundColor: [
                            '#064089', // bg-blue-700
                            '#6497B1', // bg-blue-400
                            '#B3CDE0', // bg-green-500
                            '#324D3E', // bg-purple-500
                            '#8EA48B', // bg-yellow-500
                            '#BECFBC' // bg-red-500
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true, // Chart will fill the container
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // The legend is manually created in HTML
                        }
                    }
                }
            });
        }

        // --- Drag-to-scroll for Bar Chart ---
        const slider = document.getElementById('scroll-container');
        if (slider) {
            let isDown = false;
            let startX;
            let scrollLeft;

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                // No need to add/remove classes if using active:cursor-grabbing
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });

            slider.addEventListener('mouseleave', () => {
                isDown = false;
            });

            slider.addEventListener('mouseup', () => {
                isDown = false;
            });

            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault(); // Prevent text selection while dragging
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2; // The '2' is a speed multiplier, adjust as needed
                slider.scrollLeft = scrollLeft - walk;
            });
        }
    });
</script>