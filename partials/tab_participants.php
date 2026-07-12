<!-- TAB: PARTICIPANTS PENETRATION -->
        <div id="tab-participants" class="tab-content hidden flex flex-col gap-6">
            <!-- SECTION 1: PENETRATION METRICS & CSV PORTAL -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- CSV Import and Quick Statistics -->
                <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between gap-6">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-lg">
                                    <i class="fa-solid fa-file-excel"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-sm">Bulk CSV Registration Gateway</h3>
                                    <p class="text-xs text-slate-500">Upload bulk attendee rosters directly into TMD records</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-dict-bright hover:bg-slate-50/50 cursor-pointer transition-all relative">
                            <input type="file" id="csv-file-input" accept=".csv" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="handleCSVUpload(event)">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-3 block"></i>
                            <span class="text-xs font-bold text-slate-700 block">Click or Drag & Drop csv files here</span>
                            <span class="text-[10px] text-slate-400 mt-1 block">Expected Headings: Participant Name, Project, Program, Training Title, Training Date, Training ID, CertID, Certificate Type, Resource Person, Sex, Province, Municipality. Max 2,000 rows per upload.</span>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button onclick="downloadCSVTemplate()" class="flex-grow text-center text-xs py-2 border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-lg transition-all flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-download text-indigo-500"></i> Download CSV Template
                            </button>
                            <button onclick="openParticipantModal(null)" class="flex-grow text-center text-xs py-2 bg-dict-bright hover:bg-blue-700 text-white font-bold rounded-lg transition-all flex items-center justify-center gap-1.5 shadow font-sans">
                                <i class="fa-solid fa-user-plus"></i> Add Single Registrant
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 border-t border-slate-100 pt-5">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[9px] uppercase font-bold text-slate-400 block">Total Imported</span>
                            <div class="text-lg font-black text-slate-800" id="part-total-count">0</div>
                        </div>
                        <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100/50">
                            <span class="text-[9px] uppercase font-bold text-blue-500 block">Male Total</span>
                            <div class="text-lg font-black text-blue-700" id="part-male-count">0</div>
                        </div>
                        <div class="p-3 bg-pink-50/50 rounded-xl border border-pink-100/50">
                            <span class="text-[9px] uppercase font-bold text-pink-500 block">Female Total</span>
                            <div class="text-lg font-black text-pink-700" id="part-female-count">0</div>
                        </div>
                        <div class="p-3 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                            <span class="text-[9px] uppercase font-bold text-indigo-500 block">Linked Courses</span>
                            <div class="text-lg font-black text-indigo-700" id="part-linked-courses">0</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col">
                    <div class="border-b border-slate-100 pb-3 mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Provincial Penetration Distribution (Male & Female Breakdown)</h3>
                            <p class="text-xs text-slate-500">Side-by-side analysis mapping total registrants by sex across administrative borders</p>
                        </div>
                    </div>
                    <div class="relative w-full flex-grow min-h-[250px] flex items-center justify-center">
                        <canvas id="participantsProvinceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PARTICIPANTS SPREADSHEET LEDGER -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-sm">Consolidated Registrants Ledger Directory</h3>
                        <p class="text-xs text-slate-500">Query student indices, audit disaggregated sex values, and export clean registry streams</p>
                    </div>
                </div>

                <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <input type="text" id="part-search-input" placeholder="Search registrant name, project, program, cert ID, resource person..." class="w-full text-xs pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright" onkeyup="renderParticipantsLedgerTable()">
                    </div>
                    <div>
                        <select id="part-province-filter" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright" onchange="renderParticipantsLedgerTable()">
                            <option value="">All Provinces (Auto-Resolved)</option>
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
                        <select id="part-sex-filter" class="w-full text-xs px-3 py-2 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-dict-bright" onchange="renderParticipantsLedgerTable()">
                            <option value="">All Sexes</option>
                            <option value="Male">Male Only</option>
                            <option value="Female">Female Only</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="clearParticipantsFilters()" class="flex-grow px-3 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 rounded-lg text-xs font-semibold transition-all">
                            Reset Filters
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-100/50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <tr>
                                <th class="p-4">Participant Name</th>
                                <th class="p-4">Project</th>
                                <th class="p-4">Program</th>
                                <th class="p-4">Training Title</th>
                                <th class="p-4">Training Date</th>
                                <th class="p-4">Training ID</th>
                                <th class="p-4">CertID</th>
                                <th class="p-4">Certificate Type</th>
                                <th class="p-4">Resource Person</th>
                                <th class="p-4">Province</th>
                                <th class="p-4">Municipality</th>
                                <th class="p-4">Gender</th>
                                <th class="p-4 text-center">Action Deck</th>
                            </tr>
                        </thead>
                        <tbody id="participants-table-body" class="divide-y divide-slate-100 text-xs">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>

                <div id="participants-empty-state" class="hidden p-12 text-center text-slate-400">
                    <i class="fa-solid fa-users-slash text-4xl mb-3 text-slate-300"></i>
                    <p class="font-semibold text-slate-600">No student registrants matching active query terms.</p>
                </div>
            </section>
        </div>