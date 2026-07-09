<!-- TAB: PMT DOWNLOADS REGISTRY (FAR-RIGHT PANEL) -->
        <div id="tab-pmt-downloads" class="tab-content hidden flex flex-col gap-6">
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 flex flex-col gap-6">
                
                <div class="border-b border-slate-100 pb-4 flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-down text-amber-500"></i> Central Office PMT Downloads Registry
                        </h3>
                        <p class="text-xs text-slate-500">Input and track all target allocations, budget allotments, Sub-AROs, and reference documents downloaded from the Central Office.</p>
                    </div>
                    <button onclick="openDownloadModal(null)" class="px-4 py-2 bg-dict-bright hover:bg-blue-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
                        <i class="fa-solid fa-plus"></i> Register Central Download
                    </button>
                </div>

                <!-- Live Allocation Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4" id="downloads-summary-deck">
                    <!-- Calculated dynamically -->
                </div>

                <!-- registered downloads list -->
                <div class="overflow-x-auto custom-scrollbar border border-slate-100 rounded-xl">
                    <div class="overflow-x-auto custom-scrollbar border border-slate-100 rounded-xl">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50 font-bold text-slate-500 border-b border-slate-200 uppercase">
                                <tr>
                                    <th class="p-3">Reference / Doc Title</th>
                                    <th class="p-3">Course Type</th>
                                    <th class="p-3">Duration Block</th>
                                    <th class="p-3 text-center">Target Volume</th>
                                    <th class="p-3 text-right">Budget / Target</th>
                                    <th class="p-3 text-right">Total Sub Allocation</th>
                                    <th class="p-3">Sub-ARO Code</th>
                                    <th class="p-3">UACS Code</th>
                                    <th class="p-3 text-center">Action Deck</th>
                                </tr>
                            </thead>
                            <tbody id="downloads-table-body" class="divide-y divide-slate-100 text-slate-700">
                                </tbody>
                        </table>
                    </div>
                </div>

                <div id="downloads-empty-state" class="hidden p-12 text-center text-slate-400">
                    <i class="fa-solid fa-cloud-arrow-down text-4xl mb-3 text-slate-300"></i>
                    <p class="font-semibold text-slate-600">No Central PMT allocations registered yet.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Register downloadable targets and Sub-ARO codes from the Central Office PMT.</p>
                </div>

                <!-- Per-Download Provincial Ledger Generator -->
                <div class="border-t border-slate-100 pt-6 flex flex-col gap-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-table-list text-amber-500"></i> Generate Provincial Ledger for a Registered Download
                        </h3>
                        <p class="text-xs text-slate-500">Pick a Reference Document Name to break its Training Target Volume down by province and implementation duration.</p>
                    </div>
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="flex flex-col flex-grow max-w-sm">
                            <label class="text-[10px] text-slate-500 font-bold uppercase mb-1">Reference Document Name</label>
                            <select id="download-ledger-select" class="text-xs border border-slate-200 rounded-lg px-2.5 py-2 bg-white text-slate-700 font-semibold focus:ring-2 focus:ring-dict-bright focus:outline-none">
                                <option value="">Select a registered download...</option>
                            </select>
                        </div>
                        <button onclick="generateDownloadLedgerMatrix()" class="px-4 py-2 bg-dict-bright hover:bg-blue-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
                            <i class="fa-solid fa-check"></i> Apply
                        </button>
                    </div>

                    <div id="download-ledger-container" class="hidden"></div>
                </div>

            </section>
        </div>