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
   * @param {object|null} questionData - Optional data to pre-fill the question inputs.
   */
  function addQuestion(type, questionData = null) {
    const questionWrapper = document.createElement("div");
    questionWrapper.className = "p-4 border rounded-lg shadow-sm bg-white";

    // If it's a view, add a class to disable interactions
    if (questionData && questionData.isViewMode) {
      questionWrapper.classList.add("view-mode");
    }
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
                            <option value="2" selected>Both</option>                        </select>
                    </div>
                    <div class="mt-4">
                        <label for="${questionId}-question-rendering" class="block text-sm font-medium text-gray-700">Question Rendering</label>
                        <select id="${questionId}-question-rendering" class="question-rendering-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="None" selected>None</option>
                            <option value="QoS">QoS</option>
                            <option value="Su">Su</option>
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
                    <div class="mt-4">
                        <label for="${questionId}-question-rendering" class="block text-sm font-medium text-gray-700">Question Rendering</label>
                        <select id="${questionId}-question-rendering" class="question-rendering-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="None" selected>None</option>
                            <option value="QoS">QoS</option>
                            <option value="Su">Su</option>
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
                    <div class="mt-4">
                        <label for="${questionId}-question-rendering" class="block text-sm font-medium text-gray-700">Question Rendering</label>
                        <select id="${questionId}-question-rendering" class="question-rendering-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="None" selected>None</option>
                            <option value="QoS">QoS</option>
                            <option value="Su">Su</option>
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
                    <div class="mt-4">
                        <label for="${questionId}-question-rendering" class="block text-sm font-medium text-gray-700">Question Rendering</label>
                        <select id="${questionId}-question-rendering" class="question-rendering-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="None" selected>None</option>
                            <option value="QoS">QoS</option>
                            <option value="Su">Su</option>
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
      if (
        questionData &&
        questionData.choices &&
        questionData.choices.length > 0
      ) {
        // Populate existing choices
        questionData.choices.forEach((choiceText) => {
          addChoiceInput(choicesContainer, choiceText);
        });
      } else {
        // Add one empty choice by default for new questions
        addChoiceInput(choicesContainer);
      }

      // Add event listener for the 'Add Choice' button
      addChoiceBtn.addEventListener("click", (event) => {
        event.preventDefault();
        addChoiceInput(choicesContainer);
      });
    }

    // Pre-fill data if provided
    if (questionData) {
      const questionInput = questionWrapper.querySelector(
        'input[type="text"][id$="-text"], textarea[id$="-text"]'
      );
      if (questionInput) questionInput.value = questionData.question || "";

      const requiredToggle = questionWrapper.querySelector(".required-toggle");
      if (requiredToggle) requiredToggle.checked = !!questionData.required;

      const transactionSelect = questionWrapper.querySelector(
        ".transaction-type-select"
      );
      if (transactionSelect)
        transactionSelect.value = questionData.transaction_type || "2";

      const renderingSelect = questionWrapper.querySelector(
        ".question-rendering-select"
      );
      if (renderingSelect)
        renderingSelect.value = questionData.question_rendering || "None";
    }

    // Add event listener for the new 'Remove Question' button
    questionWrapper
      .querySelector(".remove-question-btn")
      .addEventListener("click", (event) => {
        event.preventDefault(); // Explicitly prevent form submission
        questionWrapper.remove();
      });
  }

  /**
   * Adds a choice input to a choices container.
   * @param {HTMLElement} container - The container to add the choice to.
   * @param {string|null} choiceText - Optional text to pre-fill the input.
   */
  function addChoiceInput(container, choiceText = null) {
    const choiceWrapper = document.createElement("div");
    choiceWrapper.className = "flex items-center gap-2";
    choiceWrapper.innerHTML = `
            <input type="text" placeholder="Enter a choice" class="flex-grow rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <button type="button" class="remove-choice-btn text-red-500 hover:text-red-700 text-xl font-bold">&times;</button>
        `;
    if (choiceText) {
      choiceWrapper.querySelector('input[type="text"]').value = choiceText;
    }
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

  /**
   * Fetches and displays a survey for viewing.
   * @param {string} surveyId The ID of the survey to load.
   */
  async function loadSurveyForViewing(surveyId) {
    try {
      const response = await fetch(
        `../../function/_questionaire/_getSurveyDetails.php?id=${surveyId}`
      );
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const result = await response.json();

      if (result.success) {
        const surveyData = result.data;
        const surveyNameInput = document.getElementById("surveyName");
        const questionsContainer = document.getElementById(
          "questions-container"
        );

        // Clear previous content
        questionsContainer.innerHTML = "";
        surveyNameInput.value = surveyData.survey_name;

        // Populate questions
        surveyData.questions.forEach((question) => {
          // Add a flag to indicate this is for viewing
          question.isViewMode = true;
          addQuestion(question.type, question);
        });

        // Disable all form fields for view mode
        document
          .querySelectorAll(
            "#questionnaire-creator-container .view-mode input, #questionnaire-creator-container .view-mode textarea, #questionnaire-creator-container .view-mode select, #questionnaire-creator-container .view-mode button"
          )
          .forEach((el) => {
            el.disabled = true;
          });

        // Show the creator/viewer container
        document
          .getElementById("survey-list-container")
          .classList.add("hidden");
        document
          .getElementById("questionnaire-creator-container")
          .classList.remove("hidden");
      } else {
        alert(`Error loading survey: ${result.message}`);
      }
    } catch (error) {
      console.error("Failed to load survey:", error);
      alert(
        "An error occurred while loading the survey. Please check the console."
      );
    }
  }

  /**
   * Fetches and displays a survey for editing.
   * @param {string} surveyId The ID of the survey to load.
   */
  async function loadSurveyForEditing(surveyId) {
    try {
      const response = await fetch(
        `../../function/_questionaire/_getSurveyDetails.php?id=${surveyId}`
      );
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const result = await response.json();

      if (result.success) {
        const surveyData = result.data;
        const surveyNameInput = document.getElementById("surveyName");
        const surveyIdInput = document.getElementById("surveyId");
        const questionsContainer = document.getElementById(
          "questions-container"
        );

        // Clear previous content and set IDs
        questionsContainer.innerHTML = "";
        surveyNameInput.value = surveyData.survey_name;
        surveyIdInput.value = surveyId; // Set the hidden ID for the form

        // Populate questions (editable)
        surveyData.questions.forEach((question) => {
          addQuestion(question.type, question); // No isViewMode flag
        });

        // Show the creator/editor container
        document
          .getElementById("survey-list-container")
          .classList.add("hidden");
        document
          .getElementById("questionnaire-creator-container")
          .classList.remove("hidden");
      } else {
        alert(`Error loading survey for editing: ${result.message}`);
      }
    } catch (error) {
      console.error("Failed to load survey for editing:", error);
      alert(
        "An error occurred while loading the survey. Please check the console."
      );
    }
  }

  if (surveyForm) {
    surveyForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      const surveyId = document.getElementById("surveyId").value;

      const surveyData = {
        survey_name: document.getElementById("surveyName").value,
        questions: [],
      };

      // If we are editing, include the survey_id in the payload
      if (surveyId) {
        surveyData.survey_id = surveyId;
      }

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
        const questionRenderingInput = wrapper.querySelector(
          ".question-rendering-select"
        );
        const questionRendering = questionRenderingInput
          ? questionRenderingInput.value
          : "None"; // Default to 'None'

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
            question_rendering: questionRendering,
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
          // Go back to the list view after a successful save/update
          document.getElementById('questionnaire-creator-container').classList.add('hidden');
          document.getElementById('survey-list-container').classList.remove('hidden');
          window.location.reload(); // Reload to see the changes in the list
        }
      } catch (error) {
        console.error("Error saving survey:", error);
        alert(
          "An error occurred while saving the survey. Check the console for details."
        );
      }
    });
  }

  // Add event listeners for all "View" buttons
  const viewSurveyButtons = document.querySelectorAll(".view-survey-btn");
  viewSurveyButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      loadSurveyForViewing(event.target.dataset.surveyId);
    });
  });

  // Add event listeners for all "Edit" buttons
  const editSurveyButtons = document.querySelectorAll(".edit-survey-btn");
  editSurveyButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      loadSurveyForEditing(event.target.dataset.surveyId);
    });
  });

  // Add event listeners for all "Activate" buttons
  const activateSurveyButtons = document.querySelectorAll(".activate-survey-btn");
  activateSurveyButtons.forEach((button) => {
    button.addEventListener("click", async (event) => {
      const surveyId = event.target.dataset.surveyId;
      if (
        !confirm(
          "Are you sure you want to make this survey active? This will deactivate any other active survey."
        )
      ) {
        return;
      }

      try {
        const response = await fetch(
          "../../function/_questionaire/_activateSurvey.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ survey_id: surveyId }),
          }
        );

        const result = await response.json();
        alert(result.message);
      } catch (error) {
        console.error("Error activating survey:", error);
        alert("An error occurred while activating the survey.");
      }
    });
  });
});
