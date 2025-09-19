<?php
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

$years = [];

try {
    // Fetch distinct years from responses, ordered from newest to oldest
    $stmtYears = $pdo->query("SELECT DISTINCT YEAR(timestamp) as response_year FROM tbl_responses WHERE YEAR(timestamp) IS NOT NULL ORDER BY response_year DESC");
    $years = $stmtYears->fetchAll(PDO::FETCH_COLUMN);
    if (empty($years)) {
        $years[] = date('Y'); // Default to current year if no responses exist
    }
} catch (PDOException $e) {
    // You can log the error for debugging if needed
    // error_log("Error fetching years for tally report filter: " . $e->getMessage());
}
?>
<div class="flex items-center gap-1 mt-3 w-3/4">
    <span class="font-semibold">FILTERS:</span>
    <div class="flex-groww w-28">
        <label for="filter_year" class="block text-xs font-medium text-[#48494A]">YEAR</label>
        <select name="filter_year" id="filter_year" class="border border-[#1E1E1E] py-1 px-2 rounded w-full bg-[#E6E7EC] ">
            <option value="" hidden>Year</option>
            <?php foreach ($years as $year) : ?>
                <option value="<?php echo htmlspecialchars($year); ?>" <?php echo ($year == date('Y')) ? 'selected' : ''; ?>><?php echo htmlspecialchars($year); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>