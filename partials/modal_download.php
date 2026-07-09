<!-- CENTRAL DOWNLOADS MODAL -->
    <div id="download-modal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl font-sans">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 id="download-modal-title" class="text-base font-extrabold text-slate-950">Register Central Office Download Document</h3>
                    <p class="text-xs text-slate-500">Record target distributions, allotted budgets, and Sub-ARO codes issued.</p>
                </div>
                <button onclick="closeDownloadModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="download-form" onsubmit="handleDownloadSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" id="download-index">

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Document Release Reference Title</label>
                    <input type="text" id="dl-title" required placeholder="e.g., CO-PMT Technical Capability Fund Allocation" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Types of Courses</label>
                        <select id="dl-type" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="ICT Training">ICT Training Track</option>
                            <option value="Webinar">Webinar Track</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training Duration Block</label>
                        <select id="dl-duration" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="16 Hours">16 Hours</option>
                            <option value="20 Hours">20 Hours</option>
                            <option value="40 Hours">40 Hours</option>
                            <option value="2 Hours">2 Hours</option>
                            <option value="3 Hours">3 Hours</option>
                            <option value="4 Hours">4 Hours</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Number of Target Trainings</label>
                        <input type="number" id="dl-target" required min="1" placeholder="10" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Allotted Budget per Target Training (₱)</label>
                        <input type="number" id="dl-budget-per" required min="0" placeholder="100000" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Issued Sub-ARO Reference Code</label>
                        <input type="text" id="dl-subaro" required placeholder="e.g., Sub-ARO-CO-2026-4402" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">UACS Reference Code</label>
                        <input type="text" id="dl-uacs" required placeholder="e.g., UACS-5020201000" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5 flex justify-end gap-3">
                    <button type="button" onclick="closeDownloadModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-dict-blue hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition-all">Register Download</button>
                </div>
            </form>
        </div>
    </div>