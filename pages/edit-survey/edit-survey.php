<div class="bg-[#E6E7EC] p-6 shadow-lg rounded-md overflow-hidden" id="survey-list-container">
  <h1 class="text-3xl font-bold mb-2">Edit Survey</h1>
  <p>Customize your survey details and questions.</p>
  <table class="border px-4 py-2 border-[#1E1E1ECC] shadow-lg overflow-hidden">
    <thead class="bg-[#064089] text-white font-normal">
      <tr>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">#</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Questionnaire</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Date Created</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Date Approved</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Change Log</th>
        <th class="border px-4 py-3 border-[#1E1E1ECC]">Actions</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
  <div class="mt-4" id="button-container">
    <button id="add-new-questionnaire-btn" class="border px-4 py-1 border-[#000000cc] shadow-lg font-bold rounded-md"> + Add New Questionnaire</button>
  </div>
</div>

<div id="questionnaire-creator-container" class="hidden">
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

    if (addNewBtn) {
      addNewBtn.addEventListener('click', () => {
        listView.classList.add('hidden');
        creatorView.classList.remove('hidden');
      });
    }
  });
</script>