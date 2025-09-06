document.addEventListener("DOMContentLoaded", () => {
  const addQuestionBtn = document.getElementById("addQuestionBtn");
  const questionTypeDialog = document.getElementById("questionTypeDialog");
  const closeDialogBtn = document.getElementById("closeDialogBtn");
  const questionsContainer = document.getElementById("questions-container");
  const surveyForm = document.getElementById("surveyForm");

  if (addQuestionBtn && questionTypeDialog) {
    addQuestionBtn.addEventListener("click", () => {
      questionTypeDialog.showModal();
    });
  }

  if (closeDialogBtn && questionTypeDialog) {
    closeDialogBtn.addEventListener("click", () => {
      questionTypeDialog.close();
    });
  }

  // Close the dialog if the user clicks outside of it
  questionTypeDialog.addEventListener("click", (event) => {
    if (event.target === questionTypeDialog) {
      questionTypeDialog.close();
    }
  });

  // Handle question type selection
  const questionTypeButtons = document.querySelectorAll(".question-type-btn");
  questionTypeButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      const questionType = event.target.dataset.type;
      addQuestion(questionType);
      questionTypeDialog.close();
    });
  });

  /**
   * Adds a new question block to the page based on the selected type.
   * @param {string} type - The type of question to add (e.g., 'dropdown', 'text').
   */
  function addQuestion(type) {
    const questionWrapper = document.createElement("div");
    questionWrapper.className = "p-4 border rounded-lg shadow-sm bg-white";
    questionWrapper.dataset.questionType = type;

    let questionContent = "";

    // A unique ID for elements within this question, to link labels and inputs
    const questionId = `question-${Date.now()}`;

    switch (type) {
      case "dropdown":
        questionContent = `
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Dropdown Question</h3>
                        <button type="button" class="remove-question-btn text-red-500 hover:text-red-700 font-bold">&times; Remove</button>
                    </div>
                    <div>
                        <label for="${questionId}-text" class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" id="${questionId}-text" placeholder="Enter your question" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Choices</label>
                        <div class="choices-container space-y-2 mt-1">
                            <!-- Choices will be added here -->
                        </div>
                        <button type="button" class="add-choice-btn mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add Choice</button>
                    </div>
                    <div class="mt-4">
                        <label for="${questionId}-transaction-type" class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select id="${questionId}-transaction-type" class="transaction-type-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="0">Face-to-Face</option>
                            <option value="1">Online</option>
                            <option value="2" selected>Both</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end mt-4 pt-4 border-t">
                        <label for="${questionId}-required" class="text-sm font-medium text-gray-700 mr-2">Required</label>
                        <input type="checkbox" id="${questionId}-required" class="required-toggle h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    </div>
                `;
        break;
      case "text":
        questionContent = `
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Text Question</h3>
                        <button type="button" class="remove-question-btn text-red-500 hover:text-red-700 font-bold">&times; Remove</button>
                    </div>
                    <div>
                        <label for="${questionId}-text" class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" id="${questionId}-text" placeholder="Enter your question" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="mt-4">
                        <label for="${questionId}-transaction-type" class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select id="${questionId}-transaction-type" class="transaction-type-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="0">Face-to-Face</option>
                            <option value="1">Online</option>
                            <option value="2" selected>Both</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end mt-4 pt-4 border-t">
                        <label for="${questionId}-required" class="text-sm font-medium text-gray-700 mr-2">Required</label>
                        <input type="checkbox" id="${questionId}-required" class="required-toggle h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    </div>
                `;
        break;
      case "description":
        questionContent = `
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <button type="button" class="remove-question-btn text-red-500 hover:text-red-700 font-bold">&times; Remove</button>
                    </div>
                    <div>
                        <label for="${questionId}-text" class="block text-sm font-medium text-gray-700">Description Text</label>
                        <textarea id="${questionId}-text" placeholder="Enter your description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                    </div>
                    <div class="mt-4">
                        <label for="${questionId}-transaction-type" class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select id="${questionId}-transaction-type" class="transaction-type-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="0">Face-to-Face</option>
                            <option value="1">Online</option>
                            <option value="2" selected>Both</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end mt-4 pt-4 border-t">
                        <label for="${questionId}-required" class="text-sm font-medium text-gray-700 mr-2">Required</label>
                        <input type="checkbox" id="${questionId}-required" class="required-toggle h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    </div>
                `;
        break;
      case "multiple-choice":
        questionContent = `
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Multiple Choice Question</h3>
                        <button type="button" class="remove-question-btn text-red-500 hover:text-red-700 font-bold">&times; Remove</button>
                    </div>
                    <div>
                        <label for="${questionId}-text" class="block text-sm font-medium text-gray-700">Question Text</label>
                        <input type="text" id="${questionId}-text" placeholder="Enter your question" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Choices</label>
                        <div class="choices-container space-y-2 mt-1">
                            <!-- Choices will be added here -->
                        </div>
                        <button type="button" class="add-choice-btn mt-2 text-sm text-blue-600 hover:text-blue-800">+ Add Choice</button>
                    </div>
                    <div class="mt-4">
                        <label for="${questionId}-transaction-type" class="block text-sm font-medium text-gray-700">Transaction Type</label>
                        <select id="${questionId}-transaction-type" class="transaction-type-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="0">Face-to-Face</option>
                            <option value="1">Online</option>
                            <option value="2" selected>Both</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end mt-4 pt-4 border-t">
                        <label for="${questionId}-required" class="text-sm font-medium text-gray-700 mr-2">Required</label>
                        <input type="checkbox" id="${questionId}-required" class="required-toggle h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" checked>
                    </div>
                `;
        break;
      default:
        console.warn("Unsupported question type:", type);
        return;
    }

    questionWrapper.innerHTML = questionContent;
    questionsContainer.appendChild(questionWrapper);

    // Add event listener for the new 'Add Choice' button if it exists
    const addChoiceBtn = questionWrapper.querySelector(".add-choice-btn");
    if (addChoiceBtn) {
      const choicesContainer =
        questionWrapper.querySelector(".choices-container");
      addChoiceBtn.addEventListener("click", (event) => {
        event.preventDefault(); // Explicitly prevent form submission
        addChoiceInput(choicesContainer);
      });
      addChoiceInput(choicesContainer); // Add one choice by default
    }

    // Add event listener for the new 'Remove Question' button
    questionWrapper
      .querySelector(".remove-question-btn")
      .addEventListener("click", (event) => {
        event.preventDefault(); // Explicitly prevent form submission
        questionWrapper.remove();
      });
  }

  function addChoiceInput(container) {
    const choiceWrapper = document.createElement("div");
    choiceWrapper.className = "flex items-center gap-2";
    choiceWrapper.innerHTML = `
            <input type="text" placeholder="Enter a choice" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <button type="button" class="remove-choice-btn text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
        `;
    container.appendChild(choiceWrapper);

    // Add event listener to the new remove button
    choiceWrapper
      .querySelector(".remove-choice-btn")
      .addEventListener("click", (event) => {
        event.preventDefault(); // Explicitly prevent form submission
        choiceWrapper.remove();
      });
  }

  /**
   * Formats the question type string for display or database storage.
   * e.g., 'multiple-choice' becomes 'Multiple Choice'.
   * @param {string} type The raw question type.
   * @returns {string} The formatted question type.
   */
  function formatQuestionTypeForDisplay(type) {
    return type
      .split("-")
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(" ");
  }

  if (surveyForm) {
    surveyForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      const surveyData = {
        survey_name: document.getElementById("surveyName").value,
        questions: [],
      };

      const questionWrappers =
        questionsContainer.querySelectorAll(".p-4.border");

      questionWrappers.forEach((wrapper) => {
        const questionInput = wrapper.querySelector(
          'input[type="text"][id$="-text"], textarea[id$="-text"]'
        );
        const questionText = questionInput ? questionInput.value.trim() : "";
        const questionType = wrapper.dataset.questionType;
        const requiredInput = wrapper.querySelector(".required-toggle");
        // Default to true (1) if the toggle isn't found for some reason.
        const isRequired = requiredInput ? (requiredInput.checked ? 1 : 0) : 1;
        const transactionTypeInput = wrapper.querySelector(
          ".transaction-type-select"
        );
        const transactionType = transactionTypeInput
          ? transactionTypeInput.value
          : "2"; // Default to 'Both' (2)

        const choices = [];
        if (questionType === "dropdown" || questionType === "multiple-choice") {
          const choiceInputs = wrapper.querySelectorAll(
            '.choices-container input[type="text"]'
          );
          choiceInputs.forEach((input) => {
            if (input.value.trim() !== "") {
              choices.push(input.value.trim());
            }
          });
        }

        if (questionText) {
          // Only add questions that have text
          surveyData.questions.push({
            type: formatQuestionTypeForDisplay(questionType),
            question: questionText,
            required: isRequired,
            choices: choices,
            transaction_type: transactionType,
          });
        }
      });

      try {
        const response = await fetch(
          // Corrected path
          "../../function/_questionaire/_saveSurvey.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(surveyData),
          }
        );
        const result = await response.json();
        alert(result.message);
        if (result.success) {
          questionsContainer.innerHTML = "";
          surveyForm.reset();
        }
      } catch (error) {
        console.error("Error saving survey:", error);
        alert(
          "An error occurred while saving the survey. Check the console for details."
        );
      }
    });
  }
});
