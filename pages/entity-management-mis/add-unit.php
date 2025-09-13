<?php
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

$units = [];
$campuses = [];
$divisions = [];
try {
    // Fetch all units from tbl_unit_mis
    $stmtUnits = $pdo->query("SELECT id, division_name, unit_name FROM tbl_unit_mis ORDER BY division_name, unit_name ASC");
    $units = $stmtUnits->fetchAll(PDO::FETCH_ASSOC);

    // No need to fetch campuses for this table

    // Fetch all divisions for dropdowns
    $stmtDivisions = $pdo->query("SELECT division_name FROM tbl_division ORDER BY division_name ASC");
    $divisions = $stmtDivisions->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    // Optional: log error for debugging
    // error_log($e->getMessage());
}
?>
<div class="border border-[#1E1E1E] rounded-md">
    <div class="p-4">
        <h2 class="font-bold text-lg">Unit</h2><br>
        <div class="mb-4">
            <select id="division-filter" name="division_filter" class="py-1 px-1 mt-1 block w-full md:w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">All Divisions</option>
                <?php foreach ($divisions as $division) : ?>
                    <option value="<?php echo htmlspecialchars($division); ?>"><?php echo htmlspecialchars($division); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <table class="border border-[#1E1E1ECC] w-full">
            <thead class="bg-[#064089] text-white font-normal">
                <tr>
                    <th class="border border-[#1E1E1ECC] font-normal ">#</th>
                    <th class="border border-[#1E1E1ECC] font-normal ">Unit</th>
                    <th class="border border-[#1E1E1ECC] font-normal ">Actions</th>
                </tr>
            </thead>
            <tbody id="unit-table-body">
                <?php if (empty($units)) : ?>
                    <tr id="no-units-row">
                        <td colspan="3" class="text-center border border-[#1E1E1ECC] p-2 bg-[#F1F7F9]">No units found.</td>
                    </tr>
                <?php else : ?>
                    <?php $count = 1; ?>
                    <?php foreach ($units as $unit) : ?>
                        <tr data-id="<?php echo $unit['id']; ?>" data-division="<?php echo htmlspecialchars($unit['division_name']); ?>" class="bg-[#F1F7F9]">
                            <td class="border border-[#1E1E1ECC] text-center p-2"><?php echo $count++; ?></td>
                            <td class="border border-[#1E1E1ECC] p-2"><?php echo htmlspecialchars($unit['unit_name']); ?></td>
                            <td class="border border-[#1E1E1ECC] p-2">
                                <div class="flex justify-center items-center gap-2">
                                    <button data-id="<?php echo $unit['id']; ?>" data-division="<?php echo htmlspecialchars($unit['division_name']); ?>" data-unit="<?php echo htmlspecialchars($unit['unit_name']); ?>" class="edit-unit-btn flex items-center gap-1 bg-[#D9E2EC] text-[#064089] px-3 py-1 rounded-md text-xs font-semibold transition hover:bg-[#c2ccd6]">
                                        <img src="../../resources/svg/pencil.svg" alt="Edit" class="h-4 w-4">
                                        <span>Edit</span>
                                    </button>
                                    <button data-id="<?php echo $unit['id']; ?>" class="delete-unit-btn bg-[#FEE2E2] text-[#EF4444] px-2 py-1 rounded-md text-xs font-semibold transition hover:bg-[#fecaca]">
                                        <img src="../../resources/svg/trash-bin.svg" alt="Delete" class="h-4 w-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table><br>
        <button id="add-unit-btn" class="bg-[#D6D7DC] border border-[#1E1E1ECC] px-10 inline-block rounded-md">+ <span class="font-bold">Add</span></button>
    </div>
</div>

<!-- Add Unit Dialog -->
<dialog id="add-unit-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <form id="add-unit-form" method="POST" class="space-y-4">
        <h3 class="font-bold text-lg mb-4">Add Unit</h3>
        <div>
            <label for="add-division-name" class="block text-sm font-medium text-gray-700">DVISION</label>
            <select id="add-division-name" name="division_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="" hidden>Name of Division</option>
                <?php foreach ($divisions as $division) : ?>
                    <option value="<?php echo htmlspecialchars($division); ?>"><?php echo htmlspecialchars($division); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="add-unit-name" class="block text-sm font-medium text-gray-700">Unit Name</label>
            <input type="text" id="add-unit-name" name="unit_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" id="cancel-add-unit" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
        </div>
    </form>
</dialog>

<!-- Edit Unit Dialog -->
<dialog id="edit-unit-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50 w-full max-w-md">
    <form id="edit-unit-form" method="POST" class="space-y-4">
        <h3 class="font-bold text-lg mb-4">Edit Unit</h3>
        <input type="hidden" id="edit-unit-id" name="unit_id">
        <div>
            <label for="edit-division-name" class="block text-sm font-medium text-gray-700">DIVISION</label>
            <select id="edit-division-name" name="division_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                <option value="">Name of Division</option>
                <?php foreach ($divisions as $division) : ?>
                    <option value="<?php echo htmlspecialchars($division); ?>"><?php echo htmlspecialchars($division); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="edit-unit-name" class="block text-sm font-medium text-gray-700">Unit Name</label>
            <input type="text" id="edit-unit-name" name="unit_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" id="cancel-edit-unit" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
        </div>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const handleRequest = async (url, formData) => {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) window.location.reload();
        };

        // --- Add Logic ---
        const addDialog = document.getElementById('add-unit-dialog');
        const addForm = document.getElementById('add-unit-form');
        document.getElementById('add-unit-btn').addEventListener('click', () => addDialog.showModal());
        document.getElementById('cancel-add-unit').addEventListener('click', () => addDialog.close());
        addDialog.addEventListener('click', (e) => e.target === addDialog && addDialog.close());
        addForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleRequest('../../function/_entityManagement/_addUnitMis.php', new FormData(addForm));
        });

        // --- Edit Logic ---
        const editDialog = document.getElementById('edit-unit-dialog');
        const editForm = document.getElementById('edit-unit-form');
        document.querySelectorAll('.edit-unit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('edit-unit-id').value = btn.dataset.id;
                document.getElementById('edit-division-name').value = btn.dataset.division;
                document.getElementById('edit-unit-name').value = btn.dataset.unit;
                editDialog.showModal();
            });
        });
        document.getElementById('cancel-edit-unit').addEventListener('click', () => editDialog.close());
        editDialog.addEventListener('click', (e) => e.target === editDialog && editDialog.close());
        editForm.addEventListener('submit', (e) => {
            e.preventDefault();
            handleRequest('../../function/_entityManagement/_editUnitMis.php', new FormData(editForm));
        });

        // --- Delete Logic ---
        document.querySelectorAll('.delete-unit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this unit? This action cannot be undone.')) {
                    const formData = new FormData();
                    formData.append('unit_id', btn.dataset.id);
                    handleRequest('../../function/_entityManagement/_deleteUnitMis.php', formData);
                }
            });
        });

        // --- Filter Logic ---
        const divisionFilter = document.getElementById('division-filter');
        const unitTableBody = document.getElementById('unit-table-body');
        const allUnitRows = unitTableBody.querySelectorAll('tr[data-id]');
        let noUnitsRow = document.getElementById('no-units-row');

        divisionFilter.addEventListener('change', () => {
            const selectedDivision = divisionFilter.value;
            let visibleRows = 0;

            allUnitRows.forEach(row => {
                const rowDivision = row.dataset.division;
                if (selectedDivision === "" || rowDivision === selectedDivision) {
                    row.style.display = ""; // Show row
                    visibleRows++;
                } else {
                    row.style.display = "none"; // Hide row
                }
            });

            if (!noUnitsRow) {
                noUnitsRow = document.createElement('tr');
                noUnitsRow.id = 'no-units-row';
                noUnitsRow.innerHTML = `<td colspan="3" class="text-center border border-[#1E1E1ECC] p-2 bg-[#F1F7F9]">No units found.</td>`;
                unitTableBody.appendChild(noUnitsRow);
            }
            noUnitsRow.style.display = visibleRows === 0 ? '' : 'none';
        });
    });
</script>