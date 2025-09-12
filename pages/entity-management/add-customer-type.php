<?php
require_once __DIR__ . '/../../function/_databaseConfig/_dbConfig.php';

$customerTypes = [];
try {
    // Ensure tbl_customer_type exists before querying
    $stmt = $pdo->query("SELECT id, customer_type FROM tbl_customer_type ORDER BY customer_type ASC");
    $customerTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // This will catch errors if the table doesn't exist.
    // You can log this error for debugging. error_log($e->getMessage());
}
?>
<div class="border border-[#1E1E1E] rounded-md">
    <div class="p-4">
        <h2 class="font-bold text-lg">Customer Type</h2><br>
        <table class="border border-[#1E1E1ECC] w-full">
            <thead class="bg-[#064089] text-white font-normal">
                <tr>
                    <th class="border border-[#1E1E1ECC] font-normal">#</th>
                    <th class="border border-[#1E1E1ECC] font-normal">Customer Type</th>
                    <th class="border border-[#1E1E1ECC] font-normal">Actions</th>
                </tr>
            </thead>
            <tbody id="customer-type-table-body">
                <?php if (empty($customerTypes)) : ?>
                    <tr>
                        <td colspan="3" class="text-center border border-[#1E1E1ECC] p-2 bg-[#F1F7F9]">No customer types found.</td>
                    </tr>
                <?php else : ?>
                    <?php $count = 1; ?>
                    <?php foreach ($customerTypes as $type) : ?>
                        <tr data-id="<?php echo $type['id']; ?>" class="bg-[#F1F7F9]">
                            <td class="border border-[#1E1E1ECC] text-center p-2"><?php echo $count++; ?></td>
                            <td class="border border-[#1E1E1ECC] p-2"><?php echo htmlspecialchars($type['customer_type']); ?></td>
                            <td class="border border-[#1E1E1ECC] p-2">
                                <div class="flex justify-center items-center gap-2">
                                    <button data-id="<?php echo $type['id']; ?>" data-name="<?php echo htmlspecialchars($type['customer_type']); ?>" class="edit-customer-type-btn flex items-center gap-1 bg-[#D9E2EC] text-[#064089] px-3 py-1 rounded-md text-xs font-semibold transition hover:bg-[#c2ccd6]">
                                        <img src="../../resources/svg/pencil.svg" alt="Edit" class="h-4 w-4">
                                        <span>Edit</span>
                                    </button>
                                    <button data-id="<?php echo $type['id']; ?>" class="delete-customer-type-btn bg-[#FEE2E2] text-[#EF4444] px-2 py-1 rounded-md text-xs font-semibold transition hover:bg-[#fecaca]">
                                        <img src="../../resources/svg/trash-bin.svg" alt="Delete" class="h-4 w-4">
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table><br>
        <button id="add-customer-type-btn" class="bg-[#D6D7DC] border border-[#1E1E1ECC] px-10 inline-block rounded-md">+ <span class="font-bold">Add</span></button>
    </div>
</div>

<!-- Add Customer Type Dialog -->
<dialog id="add-customer-type-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50">
    <form id="add-customer-type-form" method="POST">
        <h3 class="font-bold text-lg mb-4">Add Customer Type</h3>
        <div>
            <label for="customer-type-name" class="block text-sm font-medium text-gray-700">CUSTOMER TYPE</label>
            <input type="text" id="customer-type-name" name="customer_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="New Customer Type" required>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" id="cancel-add-customer-type" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
        </div>
    </form>
</dialog>

<!-- Edit Customer Type Dialog -->
<dialog id="edit-customer-type-dialog" class="p-6 rounded-md shadow-lg backdrop:bg-black backdrop:bg-opacity-50">
    <form id="edit-customer-type-form" method="POST">
        <h3 class="font-bold text-lg mb-4">Edit Customer Type</h3>
        <input type="hidden" id="edit-customer-type-id" name="customer_type_id">
        <div>
            <label for="edit-customer-type-name" class="block text-sm font-medium text-gray-700">Customer Type Name</label>
            <input type="text" id="edit-customer-type-name" name="customer_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <button type="button" id="cancel-edit-customer-type" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
        </div>
    </form>
</dialog>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- Add Logic ---
        const addDialog = document.getElementById('add-customer-type-dialog');
        const addForm = document.getElementById('add-customer-type-form');
        document.getElementById('add-customer-type-btn').addEventListener('click', () => addDialog.showModal());
        document.getElementById('cancel-add-customer-type').addEventListener('click', () => addDialog.close());
        addDialog.addEventListener('click', (e) => e.target === addDialog && addDialog.close());

        addForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addForm);
            const response = await fetch('../../function/_entityManagement/_addCustomerType.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) window.location.reload();
        });

        // --- Edit Logic ---
        const editDialog = document.getElementById('edit-customer-type-dialog');
        const editForm = document.getElementById('edit-customer-type-form');
        const editIdInput = document.getElementById('edit-customer-type-id');
        const editNameInput = document.getElementById('edit-customer-type-name');

        document.querySelectorAll('.edit-customer-type-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                editIdInput.value = btn.dataset.id;
                editNameInput.value = btn.dataset.name;
                editDialog.showModal();
            });
        });

        document.getElementById('cancel-edit-customer-type').addEventListener('click', () => editDialog.close());
        editDialog.addEventListener('click', (e) => e.target === editDialog && editDialog.close());

        editForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(editForm);
            const response = await fetch('../../function/_entityManagement/_editCustomerType.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) window.location.reload();
        });

        // --- Delete Logic ---
        document.querySelectorAll('.delete-customer-type-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const customerTypeId = btn.dataset.id;
                if (confirm('Are you sure you want to delete this customer type? This action cannot be undone.')) {
                    try {
                        const formData = new FormData();
                        formData.append('customer_type_id', customerTypeId);

                        const response = await fetch('../../function/_entityManagement/_deleteCustomerType.php', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();
                        alert(result.message);
                        if (result.success) window.location.reload();
                    } catch (error) {
                        console.error('Error deleting customer type:', error);
                        alert('An error occurred while deleting the customer type.');
                    }
                }
            });
        });
    });
</script>