<!-- TAB: TRAINING TRACKER & CRUD -->
        <div id="tab-tracker" class="tab-content hidden flex flex-col gap-6">
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                
                <!-- Table Header Toolsets -->
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-dict-light text-dict-blue rounded-xl">
                            <i class="fa-solid fa-list-check text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Caraga Region Training Database Tracker</h3>
                            <p class="text-xs text-slate-500">Perform direct CRUD operations, manage sex-disaggregated participants, or map central documents</p>
                        </div>
                    </div>

                    <!-- Actions Panel -->
                    <div class="flex items-center gap-2">
                        <button onclick="exportToCSV()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-file-csv text-emerald-600"></i> Export to CSV
                        </button>
                        <button onclick="generateTrainingCalendarPDF()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all">
                            <i class="fa-solid fa-file-pdf text-red-600"></i> Generate Training Calendar
                        </button>
                        <button onclick="openFormModal(null)" class="px-4 py-2 bg-dict-bright hover:bg-blue-700 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md transition-all">
                            <i class="fa-solid fa-plus"></i> Launch New Training Project
                        </button>
                    </div>
                </div>

                <!-- Table Filters -->
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <input type="text" id="tracker-search-input" placeholder="Search title, venue, or officer..." class="w-full text-xs pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright focus:border-transparent" onkeyup="syncDashboardFilters(this, 'tracker-search')">
                    </div>
                    <div>
                        <select id="tracker-province-select" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright" onchange="syncDashboardFilters(this, 'tracker-province')">
                            <option value="">All Areas</option>
                            <option value="Agusan del Norte">Agusan del Norte</option>
                            <option value="Agusan del Sur">Agusan del Sur</option>
                            <option value="Surigao del Norte">Surigao del Norte</option>
                            <option value="Surigao del Sur">Surigao del Sur</option>
                            <option value="Dinagat Islands">Dinagat Islands</option>
                            <option value="Butuan City">Butuan City</option>
                            <option value="Regional Office">Regional Office</option>
                        </select>
                    </div>
                    <div>
                        <select id="tracker-status-select" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright" onchange="syncDashboardFilters(this, 'tracker-status')">
                            <option value="">All Statuses</option>
                            <option value="completed">Completed</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="clearAllFilters()" class="flex-grow px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-semibold transition-all">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <!-- Interactive Data Table -->
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <tr>
                                <th class="p-4">Training Identification / Title</th>
                                <th class="p-4">Type</th>
                                <th class="p-4">Province</th>
                                <th class="p-4">Assigned Location</th>
                                <th class="p-4">Sex Desegregation (M/F)</th>
                                <th class="p-4 text-right">Costs & Savings</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Drive Resource</th>
                                <th class="p-4 text-center">Action Deck</th>
                            </tr>
                        </thead>
                        <tbody id="tracker-table-body" class="divide-y divide-slate-100 text-slate-700">
                            </tbody>
                    </table>
                </div>

                <!-- Table Empty State -->
                <div id="tracker-empty-state" class="hidden p-12 text-center text-slate-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-slate-300"></i>
                    <p class="font-semibold text-slate-600">No matching training records discovered.</p>
                    <p class="text-[11px] text-slate-400 mt-1">Adjust filters or create a new entry to begin tracking.</p>
                </div>
            </section>
        </div>