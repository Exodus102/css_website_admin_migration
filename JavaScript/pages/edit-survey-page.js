document.addEventListener("DOMContentLoaded", () => {
  // These are the only two static containers in your initial HTML
  const surveyContainer = document.getElementById("survey-container");
  const modal = document.getElementById("question-type-modal");
  const closeModalBtn = document.getElementById("close-modal-btn");

  let currentQuestionContainer = null;
  let sectionCount = 0;

  // Use a single, powerful event listener on the main container
  surveyContainer.addEventListener("click", (event) => {
    const target = event.target;

    // --- Logic for the initial "Add New Questionnaire" button ---
    // This button exists in the initial HTML
    if (target.id === "add-new-questionnaire-btn") {
      const targetUrl = target.getAttribute("data-target");
      if (targetUrl) {
        fetch(targetUrl)
          .then((response) => response.text())
          .then((htmlContent) => {
            // This replaces the content of the entire survey-container
            surveyContainer.innerHTML = htmlContent;
          })
          .catch((error) => {
            console.error("Fetch error:", error);
            surveyContainer.innerHTML =
              '<p style="color: red;">Failed to load the new questionnaire. Please try again.</p>';
          });
      }
    }
  });

  // We need a delegated listener on the <body> or a higher-level static container
  // to handle buttons that are added after the page loads.
  document.body.addEventListener("click", (event) => {
    const target = event.target;

    // --- Logic for adding a new section ---
    // This button exists in the new-questionaire.php file
    if (target.id === "add-section-btn") {
      const sectionsContainer = document.getElementById("sections-container");
      if (sectionsContainer) {
        sectionCount++;
        const sectionDiv = document.createElement("div");
        sectionDiv.className = "border p-4 mt-4 rounded-md bg-gray-200";
        sectionDiv.innerHTML = `
                    <h2 class="font-bold">Section ${sectionCount}</h2>
                    <div class="mt-2" id="questions-section-${sectionCount}">
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button class="add-question-btn border px-3 py-1 rounded-md bg-green-500 text-white">Add Question</button>
                        <button class="edit-section-btn border px-3 py-1 rounded-md bg-gray-500 text-white">Edit Section</button>
                    </div>
                `;
        sectionsContainer.appendChild(sectionDiv);
      }
    }

    // --- Logic for dynamically added "Add Question" buttons ---
    // This listener works because it's on a static parent (document.body)
    else if (target.classList.contains("add-question-btn")) {
      const sectionDiv = target.closest(
        ".border.p-4.mt-4.rounded-md.bg-gray-200"
      );
      if (sectionDiv) {
        const sectionId = sectionDiv.querySelector(
          '[id^="questions-section-"]'
        ).id;
        currentQuestionContainer = document.getElementById(sectionId);
        if (modal) {
          modal.classList.remove("hidden");
        }
      }
    }
  });

  // --- Modal Logic (remains the same) ---
  if (closeModalBtn && modal) {
    closeModalBtn.addEventListener("click", () => {
      modal.classList.add("hidden");
      currentQuestionContainer = null;
    });

    modal.addEventListener("click", (event) => {
      const typeButton = event.target;
      if (typeButton.classList.contains("modal-btn")) {
        const questionType = typeButton.getAttribute("data-type");
        if (currentQuestionContainer) {
          const questionDiv = document.createElement("div");
          questionDiv.className = "border p-2 mt-2 bg-white rounded-md";
          questionDiv.innerHTML = `<label>Question Type: ${questionType}</label>`;
          currentQuestionContainer.appendChild(questionDiv);
        }
        if (modal) {
          modal.classList.add("hidden");
        }
      }
    });
  }
});
