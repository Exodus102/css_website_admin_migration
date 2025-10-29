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
                    // Clear the required flag for division/office/customer type on cloned rows
                    input.required = false;
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

                const formData = new FormData(addResponseForm);
                // Manually add the campus data, which isn't in the form grid but is needed for each response.
                formData.append('user_campus', '<?php echo $user_campus; ?>');

                try {
                    // Send one request for all rows
                    const response = await fetch('../../function/_dataResponse/_addManualResponse.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }

                    const result = await response.json();
                    alert(result.message);

                    if (result.success) {
                        window.location.reload();
                    }

                } catch (error) {
                    alert('An error occurred while saving the responses. Please check the console.');
                    console.error('Add Response Error:', error);
                }
            });
        }
    });
</script>