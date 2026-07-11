<!-- TAB: EXECUTIVE DASHBOARD -->
        <div id="tab-dashboard" class="tab-content flex flex-col gap-6">
            
            <!-- SECTION 1: FULL WIDTH GIS INTERACTIVE MAP -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
                <div class="p-4 bg-slate-50 border-b border-slate-200/60 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-dict-blue rounded-xl">
                            <i class="fa-solid fa-map-location-dot text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Interactive Caraga Training Locator & GIS Heatmap</h3>
                            <p class="text-xs text-slate-500">Locked regional boundaries showcasing exclusive target deployment markers and heat signatures (Static Zoom)</p>
                        </div>
                    </div>

                    <!-- Live Map Filter Panel -->
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex flex-col">
                            <label class="text-[10px] text-slate-500 font-bold uppercase mb-1">Province/Office</label>
                            <select id="map-province" class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white text-slate-700 font-semibold focus:ring-2 focus:ring-dict-bright focus:outline-none" onchange="syncDashboardFilters(this, 'map-province')">
                                <option value="">All Areas</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="Butuan City">Butuan City</option>
                                <option value="Agusan del Norte">Agusan del Norte</option>
                                <option value="Agusan del Sur">Agusan del Sur</option>
                                <option value="Surigao del Norte">Surigao del Norte</option>
                                <option value="Surigao del Sur">Surigao del Sur</option>
                                <option value="Dinagat Islands">Dinagat Islands</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[10px] text-slate-500 font-bold uppercase mb-1">Status Pin Color</label>
                            <select id="map-status" class="text-xs border border-slate-200 rounded-lg px-2.5 py-1.5 bg-white text-slate-700 font-semibold focus:ring-2 focus:ring-dict-bright focus:outline-none" onchange="syncDashboardFilters(this, 'map-status')">
                                <option value="">All Statuses</option>
                                <option value="completed">🟢 Completed</option>
                                <option value="ongoing">🔵 Ongoing</option>
                                <option value="upcoming">🟠 Upcoming</option>
                                <option value="cancelled">🔴 Cancelled</option>
                                <option value="rescheduled">⚪ Rescheduled</option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="text-[10px] text-slate-500 font-bold uppercase mb-1">Interactive Search</label>
                            <div class="relative">
                                <input type="text" id="map-search-input" placeholder="Search title or venue..." class="text-xs border border-slate-200 rounded-lg pl-8 pr-2.5 py-1.5 bg-white text-slate-700 focus:ring-2 focus:ring-dict-bright focus:outline-none w-52" onkeyup="syncDashboardFilters(this, 'map-search')">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-[10px]"></i>
                            </div>
                        </div>

                        <div class="flex flex-col justify-end pt-5">
                            <button id="heatmap-toggle-btn" onclick="toggleHeatmap()" class="text-xs px-3.5 py-1.5 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-slate-700 font-bold flex items-center gap-1.5 transition-all">
                                <i class="fa-solid fa-fire text-orange-500"></i> Toggle Heatmap
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Leaflet Frame Container -->
                <div class="relative w-full h-[520px]">
                    <div id="gis-map" class="w-full h-full z-10"></div>
                </div>
            </section>

            <!-- SECTION 3: OPERATIONAL CATEGORIES, PROVINCIAL LEDGER & FINANCIAL SUMMARY -->
            <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-4 flex flex-col gap-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Operational Categories Counts</h4>
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex-grow flex flex-col justify-between">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5"><i class="fa-solid fa-laptop-code text-blue-600"></i> ICT Trainings Targets</span>
                            <span id="ict-total-badge" class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded-full">0 Total</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs mt-3">
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">16 Hours</span>
                                <span id="ict-16h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">20 Hours</span>
                                <span id="ict-20h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">40 Hours</span>
                                <span id="ict-40h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex-grow flex flex-col justify-between">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <span class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5"><i class="fa-solid fa-chalkboard-user text-amber-600"></i> Webinars Targets</span>
                            <span id="webinar-total-badge" class="bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full">0 Total</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs mt-3">
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">2 Hours</span>
                                <span id="web-2h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">3 Hours</span>
                                <span id="web-3h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                            <div class="p-2 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[10px] text-slate-400 block font-bold uppercase">4 Hours</span>
                                <span id="web-4h-val" class="font-black text-slate-800 text-sm">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Provinces Allocation & Remaining Targets Ledger</h4>
                    
                    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex-grow flex flex-col">
                        <span class="text-[11px] text-slate-400 block mb-2 font-medium">Manual target distribution parameters from Regional Office to Provinces</span>
                        <div class="overflow-y-auto custom-scrollbar flex-grow max-h-[220px]">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="sticky top-0 bg-white">
                                    <tr class="text-slate-400 font-bold uppercase border-b border-slate-100 text-[9px]">
                                        <th class="py-1 pb-2">Province / Branch</th>
                                        <th class="py-1 pb-2 text-center">Target</th>
                                        <th class="py-1 pb-2 text-center">Accomplished</th>
                                        <th class="py-1 pb-2 text-center">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody id="provincial-summary-tbody" class="divide-y divide-slate-100 text-slate-700">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider px-1">Financial Expenditure Summary</h4>
                    
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Consolidated Budget</span>
                            <span class="text-[11px] text-slate-400 block -mt-0.5">Downloaded from Central CO PMT</span>
                            <h3 class="text-xl font-black text-slate-900 mt-2" id="card-budget-allocated">₱0.00</h3>
                        </div>
                        <div class="p-3 bg-slate-100 rounded-xl text-slate-600"><i class="fa-solid fa-building-shield text-lg"></i></div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Budget Utilized</span>
                            <span class="text-[11px] text-slate-400 block -mt-0.5">Total spent across all regional trainings</span>
                            <h3 class="text-xl font-black text-purple-600 mt-2" id="card-budget-utilized">₱0.00</h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-xl text-purple-600"><i class="fa-solid fa-wallet text-lg"></i></div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total remaining budget</span>
                            <span class="text-[11px] text-slate-400 block -mt-0.5">Consolidated Budget minus Utilized</span>
                            <h3 class="text-xl font-black text-rose-600 mt-2" id="card-budget-remaining">₱0.00</h3>
                        </div>
                        <div class="p-3 bg-rose-50 rounded-xl text-rose-600"><i class="fa-solid fa-scale-balanced text-lg"></i></div>
                    </div>
                </div>
            </section>

            <section class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <h3 class="font-extrabold text-slate-900 text-sm">Provincial Target Analytics Chart Matrix</h3>
                    <p class="text-xs text-slate-500">Visual display of distributed target configurations, total accomplishment statistics, and remaining tracking records</p>
                </div>
                <div class="relative h-[300px] w-full">
                    <canvas id="provincialOfficeChart"></canvas>
                </div>
            </section>
        </div>