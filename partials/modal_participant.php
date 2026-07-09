<!-- SINGLE PARTICIPANT REGISTRANT MODAL -->
    <div id="participant-modal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-2xl font-sans max-h-[90vh] overflow-y-auto custom-scrollbar">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 id="participant-modal-title" class="text-base font-extrabold text-slate-950">Add Single Registrant</h3>
                    <p class="text-xs text-slate-500">Province and Municipality are auto-resolved from the linked Training ID.</p>
                </div>
                <button onclick="closeParticipantModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form id="participant-form" onsubmit="handleParticipantSubmit(event)" class="p-6 space-y-4">
                <input type="hidden" id="participant-id">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Participant Name</label>
                        <input type="text" id="participant-name" required placeholder="e.g., Juan Dela Cruz" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Sex</label>
                        <select id="participant-sex" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Project</label>
                        <input type="text" id="participant-project" placeholder="e.g., Free Wi-Fi for All" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Program</label>
                        <input type="text" id="participant-program" placeholder="e.g., ICT Literacy" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training ID (links to Training Tracker record)</label>
                    <input type="text" id="participant-training-id" placeholder="e.g., tr-101" onchange="onParticipantTrainingIdChange()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    <p id="participant-training-match" class="text-[10px] text-slate-400 mt-1"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training Title</label>
                    <input type="text" id="participant-training-title" placeholder="e.g., ISMS Compliance Auditing" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training Date</label>
                        <input type="date" id="participant-training-date" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Resource Person</label>
                        <input type="text" id="participant-resource-person" placeholder="e.g., Engr. Ricardo Salvador" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">CertID</label>
                        <input type="text" id="participant-cert-id" placeholder="e.g., CERT-2026-0001" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Certificate Type</label>
                        <input type="text" id="participant-cert-type" placeholder="e.g., Certificate of Completion" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Province (auto-resolved, or set manually if no Training ID)</label>
                        <select id="participant-province" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="">— Not set —</option>
                            <option value="Regional Office">Regional Office</option>
                            <option value="Butuan City">Butuan City</option>
                            <option value="Agusan del Norte">Agusan del Norte</option>
                            <option value="Agusan del Sur">Agusan del Sur</option>
                            <option value="Surigao del Norte">Surigao del Norte</option>
                            <option value="Surigao del Sur">Surigao del Sur</option>
                            <option value="Dinagat Islands">Dinagat Islands</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Municipality</label>
                        <input type="text" id="participant-municipality" placeholder="e.g., Prosperidad" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5 flex justify-end gap-3">
                    <button type="button" onclick="closeParticipantModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 text-xs font-semibold">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-dict-blue hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition-all">Save Registrant</button>
                </div>
            </form>
        </div>
    </div>