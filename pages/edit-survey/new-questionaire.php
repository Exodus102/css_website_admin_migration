<div id="survey-form">
    <div class="p-6">
        <button id="add-section-btn" class="border px-4 py-1 border-[#000000cc] shadow-lg font-bold rounded-md">Add Section</button>
    </div>
    <div id="sections-container">
    </div>
</div>

<div id="question-type-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 h-full w-full hidden flex items-center justify-center">
    <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4 text-center">Select Question Type</h3>
        <div class="flex flex-col gap-2">
            <button data-type="dropdown" class="modal-btn border px-4 py-2 rounded-md bg-blue-500 text-white">Dropdown</button>
            <button data-type="text" class="modal-btn border px-4 py-2 rounded-md bg-blue-500 text-white">Text</button>
            <button data-type="multiple-choice" class="modal-btn border px-4 py-2 rounded-md bg-blue-500 text-white">Multiple Choice</button>
            <button data-type="description" class="modal-btn border px-4 py-2 rounded-md bg-blue-500 text-white">Description</button>
        </div>
        <div class="text-center mt-4">
            <button id="close-modal-btn" class="border px-4 py-2 rounded-md bg-red-500 text-white">Cancel</button>
        </div>
    </div>
</div>