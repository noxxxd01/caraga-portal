<!-- REUSABLE FORM MODAL -->
    <div id="crud-modal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[9999] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-4xl max-h-[90vh] overflow-y-auto custom-scrollbar">
            
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 id="modal-form-title" class="text-base font-extrabold text-slate-950">Add New Training Record</h3>
                    <p class="text-xs text-slate-500">Enter high-fidelity parameters. GIS coordinates place markers automatically onto the Map layer.</p>
                </div>
                <button onclick="closeFormModal()" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition-all">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Body -->
            <form id="crud-form" onsubmit="handleFormSubmission(event)" class="p-6 space-y-6">
                <input type="hidden" id="form-id">

                <!-- Section 1: Structural Specs -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training / Seminar Title</label>
                        <input type="text" id="form-title" required placeholder="e.g., Free Wi-Fi Local Government Unit Installation & Config Workshop" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Course Code / ID</label>
                        <input type="text" id="form-code" required placeholder="e.g., CYBER-SEC-202" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Course Type</label>
                        <select id="form-course-type" required onchange="onFormTypeChange()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="ICT Training">ICT Training</option>
                            <option value="Webinar">Webinar</option>
                        </select>
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Course Description</label>
                        <textarea id="form-description" rows="2" placeholder="e.g., Hands-on workshop covering Model-View-Controller architecture for enterprise web application deployment." class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none resize-none"></textarea>
                    </div>
                </div>

                <!-- Section 2: GIS Location and Target Pre-Fills -->
                <div class="p-4 bg-slate-50 rounded-xl space-y-4 border border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 flex items-center gap-1.5 border-b border-blue-100 pb-1.5">
                        <i class="fa-solid fa-location-crosshairs text-red-500"></i> Region Coordinate Helper
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Province Area / Office</label>
                            <select id="form-province" required onchange="onModalProvinceChange()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
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
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Municipality / City</label>
                            <input type="text" id="form-municipality" required placeholder="e.g., Prosperidad" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Barangay</label>
                            <input type="text" id="form-barangay" required placeholder="e.g., Poblacion" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Specific Venue Location</label>
                            <input type="text" id="venue" required placeholder="e.g., DICT Training Room, 2nd Floor" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Latitude</label>
                                <input type="number" id="form-latitude" step="any" required placeholder="9.1204" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Longitude</label>
                                <input type="number" id="form-longitude" step="any" required placeholder="125.5342" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Project Timeline & Officers -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Start Date</label>
                        <input type="date" id="form-start-date" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">End Date</label>
                        <input type="date" id="form-end-date" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Course Officer</label>
                        <input type="text" id="form-course-officer" required placeholder="e.g., Jane Doe" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Resource Person / Trainer</label>
                        <input type="text" id="form-resource-person" required placeholder="e.g., John Smith" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                    </div>
                </div>

                <!-- Section 4: Operational Metrics, Budgeting & Folder Relocation -->
                <div class="p-4 bg-blue-50/50 border border-blue-100 rounded-xl space-y-4">
                    <h4 class="text-xs font-bold text-slate-700 flex items-center gap-1.5 border-b border-blue-100 pb-1.5">
                        <i class="fa-solid fa-calculator text-blue-600"></i> Project Quantities, Budget Allocations, & Saved Balance Metrics
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Budget Allocated (₱)</label>
                            <input type="number" id="form-budget-allocated" required step="0.01" placeholder="75000" onkeyup="computeBudgetSavings()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Budget Utilized (₱)</label>
                            <input type="number" id="form-budget-utilized" required step="0.01" placeholder="68000" onkeyup="computeBudgetSavings()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Budget Saved (₱)</label>
                            <input type="text" id="form-budget-saved" readonly placeholder="0.00" class="w-full text-xs border border-slate-200 bg-slate-100 font-extrabold rounded-lg px-3 py-2 text-emerald-700 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Cloud drive documentation link</label>
                            <input type="url" id="form-drive-link" placeholder="e.g., https://drive.google.com/drive/folders/..." class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                    </div>

                    <!-- Pax and Duration Matrix -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Training Duration (Hours)</label>
                            <select id="form-duration" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                                <!-- Populated dynamically by script -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Target Pax (Baseline)</label>
                            <input type="number" id="form-target-participants" required min="1" placeholder="30" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Registered Males (Pax)</label>
                            <input type="number" id="form-male-participants" required min="0" placeholder="15" onkeyup="computeDisaggregatedPaxTotal()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Registered Females (Pax)</label>
                            <input type="number" id="form-female-participants" required min="0" placeholder="15" onkeyup="computeDisaggregatedPaxTotal()" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <input type="hidden" id="form-actual-participants">
                    </div>
                </div>

                <!-- Section 5: Status State -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Mode of Implementation</label>
                        <select id="form-implementation-mode" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="Face-to-Face">Face-to-Face</option>
                            <option value="Virtual">Virtual</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Current Implementation Status</label>
                        <select id="form-status" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <option value="completed">Completed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Attached Documentation Simulator</label>
                        <div class="flex items-center gap-4 pt-1.5">
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer">
                                <input type="checkbox" id="form-photos" class="rounded text-dict-bright focus:ring-dict-bright">
                                <span>Includes Photos</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer">
                                <input type="checkbox" id="form-documents" class="rounded text-dict-bright focus:ring-dict-bright">
                                <span>Includes Certificates</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Frame -->
                <div class="border-t border-slate-100 pt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeFormModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 bg-dict-blue hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>