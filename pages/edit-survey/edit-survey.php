<div class="p-4 overflow-hidden" id="survey-list-container">
  <h1 class="text-3xl font-bold mb-2 font-sfpro leading-5">Edit Survey</h1>
  <p class="font-sfpro">Customize your survey details and questions.</p><br>
  <?php
  require_once '../../function/_databaseConfig/_dbConfig.php';

  try {
    $stmt = $pdo->query("SELECT id, question_survey, `timestamp`, date_approved, change_log FROM tbl_questionaireform ORDER BY question_survey ASC");
    $questionnaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    // You could log the error here if needed
    // error_log($e->getMessage());
    $questionnaires = [];
  }
  ?>
  <table class="border px-4 py-2 border-[#1E1E1ECC] shadow-lg overflow-hidden">
    <thead class="bg-[#064089] text-white font-normal text-left w-full">
      <tr>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">#</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Questionnaire</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Date Created</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Date Approved</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Change Log</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC] text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($questionnaires)) : ?>
        <tr>
          <td colspan="6" class="text-center border px-4 py-3 border-[#1E1E1ECC]">No questionnaires found.</td>
        </tr>
      <?php else : ?>
        <?php $row_number = 1; ?>
        <?php foreach ($questionnaires as $q) : ?>
          <tr class="bg-white">
            <td class="border px-4 py-3 border-[#1E1E1ECC]"><?php echo $row_number++; ?></td>
            <td class="border px-4 py-3 border-[#1E1E1ECC]"><?php echo htmlspecialchars($q['question_survey']); ?></td>
            <td class="border px-4 py-3 border-[#1E1E1ECC]"><?php echo date('F j, Y, g:i a', strtotime($q['timestamp'])); ?></td>
            <td class="border px-4 py-3 border-[#1E1E1ECC]"><?php echo $q['date_approved'] ? date('F j, Y, g:i a', strtotime($q['date_approved'])) : 'N/A'; ?></td>
            <td class="border px-4 py-3 border-[#1E1E1ECC]"><?php echo htmlspecialchars($q['change_log'] ?: 'N/A'); ?></td>
            <td class="border px-4 py-3 border-[#1E1E1ECC]">
              <div class="flex justify-center items-center gap-2">
                <button data-survey-id="<?php echo $q['id']; ?>" class="activate-survey-btn bg-[#D9E2EC] text-[#064089] px-3 py-1 rounded-md text-xs font-semibold transition">Active</button>
                <button data-survey-id="<?php echo $q['id']; ?>" class="view-survey-btn bg-[#D9E2EC] text-[#064089] px-3 py-1 rounded-md text-xs font-semibold transition">View</button>
                <button data-survey-id="<?php echo $q['id']; ?>" class="edit-survey-btn bg-[#D9E2EC] text-[#064089] px-3 py-1 rounded-md text-xs font-semibold transition">Edit</button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <div class="mt-4" id="button-container">
    <button id="add-new-questionnaire-btn" class="border px-4 py-1 border-[#000000cc] shadow-lg font-bold rounded-md"> + Add New Questionnaire</button>
  </div>
</div>

<div id="questionnaire-creator-container" class="hidden">
  <div class="mb-4">
    <button id="back-to-list-btn" class="border px-4 py-1 border-[#000000cc] shadow-lg font-bold rounded-md">&larr; Back to List</button>
  </div>

  <button id="addQuestionBtn">Add Question</button>

  <dialog id="questionTypeDialog">
    <div class="p-4">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Select Question Type</h2>
        <button id="closeDialogBtn" aria-label="close" class="text-2xl font-bold leading-none">&times;</button>
      </div>
      <div class="dialog-form grid grid-cols-2 gap-4">
        <button type="button" class="question-type-btn p-4 border rounded-md hover:bg-gray-100" data-type="dropdown">Dropdown</button>
        <button type="button" class="question-type-btn p-4 border rounded-md hover:bg-gray-100" data-type="text">Text</button>
        <button type="button" class="question-type-btn p-4 border rounded-md hover:bg-gray-100" data-type="description">Description</button>
        <button type="button" class="question-type-btn p-4 border rounded-md hover:bg-gray-100" data-type="multiple-choice">Multiple Choices</button>
      </div>
    </div>
  </dialog>

  <form id="surveyForm" class="mt-8">
    <div>
      <input type="hidden" id="surveyId" name="survey_id" />
      <label for="surveyName" class="block text-sm font-medium text-gray-700">Survey Name</label>
      <input type="text" id="surveyName" name="survey_name" value="2025 Questionaire_v1.2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
    </div>

    <div id="questions-container" class="mt-8 space-y-6">
      <!-- New questions will be added here -->
    </div>

    <div class="mt-8">
      <button type="submit" id="saveSurveyBtn" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">Save Survey</button>
    </div>
  </form>
</div>

<script src="../../JavaScript/pages/edit-survey/new-questionaire-page.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const listView = document.getElementById('survey-list-container');
    const creatorView = document.getElementById('questionnaire-creator-container');
    const addNewBtn = document.getElementById('add-new-questionnaire-btn');
    const backBtn = document.getElementById('back-to-list-btn');
    const viewBtns = document.querySelectorAll('.view-survey-btn');
    const surveyIdInput = document.getElementById('surveyId');

    if (addNewBtn) {
      addNewBtn.addEventListener('click', () => {
        listView.classList.add('hidden');
        creatorView.classList.remove('hidden');
        // Ensure we're in "create" mode by clearing the ID
        if (surveyIdInput) surveyIdInput.value = '';
      });
    }

    if (backBtn) {
      backBtn.addEventListener('click', () => {
        creatorView.classList.add('hidden');
        listView.classList.remove('hidden');
        // Reset the form for the next use
        document.getElementById('surveyForm').reset();
        if (surveyIdInput) surveyIdInput.value = '';
        document.getElementById('questions-container').innerHTML = '';
      });
    }
  });
</script>