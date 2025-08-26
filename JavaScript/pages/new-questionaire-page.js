document.addEventListener("DOMContentLoaded", () => {
  const addSectionBtn = document.getElementById("add-section-btn");
  const sectionsContainer = document.getElementById("sections-container");
  const modal = document.getElementById("question-type-modal");
  const closeModalBtn = document.getElementById("close-modal-btn");

  let currentQuestionContainer = null;
  let sectionCount = 0;

  // Add a new section when the "Add Section" button is clicked
  addSectionBtn.addEventListener("click", () => {
    sectionCount++;
    const sectionDiv = document.createElement("div");
    sectionDiv.className = "border p-4 mt-4 rounded-md bg-gray-200";
    sectionDiv.innerHTML = `
            <h2 class="font-bold">Section ${sectionCount}</h2>
            <div class="mt-2" id="questions-section-${sectionCount}">
                </div>
            <div class="mt-4 flex gap-2">
                <button class="add-question-btn border px-3 py-1 rounded-md bg-green-500 text-white" data-section-id="questions-section-${sectionCount}">Add Question</button>
                <button class="edit-section-btn border px-3 py-1 rounded-md bg-gray-500 text-white">Edit Section</button>
            </div>
        `;
    sectionsContainer.appendChild(sectionDiv);
  });

  // Event Delegation: Listen for clicks on the "Add Question" button inside any section
  sectionsContainer.addEventListener("click", (event) => {
    const target = event.target;
    if (target.classList.contains("add-question-btn")) {
      const sectionId = target.getAttribute("data-section-id");
      currentQuestionContainer = document.getElementById(sectionId);
      modal.classList.remove("hidden");
    }
  });

  // Close modal listener
  closeModalBtn.addEventListener("click", () => {
    modal.classList.add("hidden");
    currentQuestionContainer = null;
  });

  // Handle clicks on the modal buttons
  modal.addEventListener("click", (event) => {
    const typeButton = event.target;
    if (typeButton.classList.contains("modal-btn")) {
      const questionType = typeButton.getAttribute("data-type");
      if (currentQuestionContainer) {
        const questionDiv = document.createElement("div");
        questionDiv.className = "border p-2 mt-2 bg-white rounded-md";
        questionDiv.innerHTML = `
                    <label>Question Type: ${questionType}</label>
                    `;
        currentQuestionContainer.appendChild(questionDiv);
      }
      modal.classList.add("hidden");
    }
  });
});
