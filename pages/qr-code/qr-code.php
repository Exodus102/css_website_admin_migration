<div class="flex-1 p-6 bg-[#e6e7ec] min-h-screen id=qr-code">
    <h2 class="text-3xl font-bold mb-2 text-[#1e1e1e]">QR Code</h2>
    <p class="text-sm text-[#1e1e1e] mb-6">Access survey page easily by generating QR code.</p>

    <div class="flex flex-col lg:flex-row items-stretch gap-6">
        <div class="bg-[#F1F7F9] shadow-md rounded-lg py-12 px-6 w-full max-w-2xl flex flex-col items-center justify-between">
            <div class="w-full flex flex-row items-start gap-4 mb-6">
                <div class="flex-1 space-y-4">
                 <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-2">CAMPUS</label>
                    <select id="campus-select" class="w-full border border-[#262627] rounded-md px-2 py-2 text-[#1e1e1e] text-sm font-bold focus:outline-none bg-[#e6e7ec]">
                      <option disabled selected hidden>Campus</option>
                      <option>Angono</option>
                      <option>Antipolo</option>
                      <option>Binangonan</option>
                      <option>Cainta</option>
                      <option>Cardona</option>
                      <option>Morong</option>
                      <option>Pililla</option>
                      <option>Rodriguez</option>
                      <option>Tanay</option>
                      <option>Taytay</option>
                    </select>
                  </div>
                    <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 mb-2">DIVISION</label>
                    <select id="division-select" class="w-full border border-[#262627] rounded-md px-2 py-2 text-[#1e1e1e] text-sm font-bold focus:outline-none bg-[#e6e7ec]">
                        <option disabled selected hidden>Division</option>
                        <option>Academic Affair</option>
                        <option>Office of the President</option>
                        <option>Administration and Finance Division</option>
                        <option>Research, Development, Extension, and Production Division</option>
                    </select>
                    </div>
                     <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 mb-2">OFFICE</label>
                    <select id="office-select" class="w-full border border-[#262627] rounded-md px-2 py-2 text-[#1e1e1e] text-sm font-bold focus:outline-none bg-[#e6e7ec]">
                        <option disabled selected hidden>Office</option>
                        <option>College of Accountancy</option>
                        <option>College of Business</option>
                        <option>College of Computer Studies</option>
                        <option>Graduate School</option>
                        <option>Campus NSTP</option>
                        <option>Student Development Service</option>
                        <option>Admission</option>
                        <option>Student Organization</option>
                        <option>Guidance and Counceling</option>
                        <option>Scholarship & Financial</option>
                        <option>OJT and Placement</option>
                        <option>Office of the Registrar</option>
                        <option>Library Services</option>
                        <option>Center for Culture and The Arts</option>
                    </select>
                    </div>
              <button id="generate-btn" class="flex justify-center items-center gap-2 w-1/3 mx-auto mt-4 border border-[#262627] bg-[#e6e7ec] hover:bg-[#064089] hover:text-[#F1F7F9] text-[#1e1e1e] rounded-md py-2 text-sm font-bold transition">
               <img src="../resources/svg/color-wand-outline.svg" alt="Magic-Wand-Icon" width="20" height="20">
                 Generate
              </button>
                </div>
                <div id="qr-section" class="border-2 border-[#064089] p-2 rounded-md w-60 h-60 bg-transparent flex items-center justify-center">
                  <img src="https://via.placeholder.com/256" alt="QR-Code">
                </div>
            </div>
        </div>
        <div class="bg-[#F1F7F9] shadow-md rounded-lg p-6 w-full max-w-sm flex flex-col items-center">
          <p class="mb-4 text-sm font-medium text-gray-700">SELECT THEME:</p>
            <div class="flex gap-3 mb-6">
             <div class="flex flex-col justify-center items-center px-5 py-7 border-2 rounded-md bg-blue-100 border-transparent cursor-pointer hover:scale-105 hover:border-blue-500 transition-transform shadow-md hover:shadow-lg shadow-gray-400" onclick="selectTheme('blue')">
              <img src="../resources/svg/scan-qr-code-blue.svg" alt="qr-code-blue-icon" width="16" height="16">
              <img src="../resources/svg/blue-ellipse.svg" alt="blue-ellipse" width="16" height="16" class="mt-10">
             </div>
              <div class="flex flex-col justify-center items-center px-5 py-7 border-2 rounded-md bg-orange-100 border-transparent hover:border-orange-500 cursor-pointer hover:scale-105 transition-transform shadow-md hover:shadow-lg shadow-gray-400" onclick="selectTheme('orange')">
                <img src="../resources/svg/scan-qr-code-orange.svg" alt="orange-qr-code-icon" width="16" height="16">
                <img src="../resources/svg/orange-ellipse.svg" alt="orange-ellipse-icon" width="16" height="16" class="mt-10">
              </div>
               <div class="flex flex-col justify-center items-center px-5 py-7 border-2 rounded-md bg-pink-100 border-transparent hover:border-pink-500 cursor-pointer hover:scale-105 transition-transform shadow-md hover:shadow-lg shadow-gray-400" onclick="selectTheme('pink')">
                 <img src="../resources/svg/scan-qr-code-pink.svg" alt="pink-qr-code" width="16" height="16">
                 <img src="../resources/svg/pink-ellipse.svg" alt="pink-ellipse-icon" width="16" height="16" class="mt-10">
               </div>
               <div class="flex flex-col justify-center items-center px-5 py-7 border-2 rounded-md bg-green-100 border-transparent hover:border-green-500 cursor-pointer hover:scale-105 transition-transform shadow-md hover:shadow-lg shadow-gray-400" onclick="selectTheme('green')">
                <img src="../resources/svg/scan-qr-code-green.svg" alt="green-qr-code" width="16" height="16">
                <img src="../resources/svg/green-ellipse.svg" alt="green-ellipses-icon" width="16" height="16" class="mt-10">
              </div>
            </div>
            <div class="flex items-center mb-4 w-full border border-[#262627] rounded-md overflow-hidden">
              <input id="input-link" type="text" value="" class="flex-1 px-3 py-2 text-sm bg-[#e6e7ec] text-[#1e1e1e] focus:outline-none"/>
              <button id="copy-button" class="bg-[#e6e7ec] hover:bg-[#d5d6db] text-[#1e1e1e] px-3 py-2 text-sm transition focus:outline-none">
               <img src="../resources/svg/copy.svg" alt="Copy Icon" width="20" height="20">
              </button>
            </div>
            <button class="flex justify-center items-center gap-2 w-1/3 border border-[#262627] bg-[#e6e7ec] hover:bg-[#064089] hover:text-[#F1F7F9] text-[#1e1e1e] rounded-md py-2 text-sm font-bold transition">
             <img src="../resources/svg/download.svg" alt="download-icon" width="20" height="20">
              Download
             </button>
        </div>
    </div>
</div>
