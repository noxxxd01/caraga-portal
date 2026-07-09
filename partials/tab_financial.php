        <!-- TAB: FINANCIAL LEDGER & BAR CHARTS -->
        <div id="tab-financial" class="tab-content hidden flex flex-col gap-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Financial Matrix Column -->
                <div class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col gap-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h3 class="font-extrabold text-slate-900 text-sm">Financial Apportionment Summary</h3>
                        <p class="text-xs text-slate-500">Consolidated budget expenditures, burns, and savings metrics across Caraga region</p>
                    </div>

                    <!-- Metrics Breakdown List -->
                    <div class="space-y-4 flex-grow justify-center flex flex-col">
                        <div class="p-4 bg-indigo-50/50 rounded-xl border border-indigo-100/50">
                            <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Total Allocated Budget</span>
                            <div class="text-xl font-extrabold text-indigo-900 mt-1" id="fin-allocated-val">₱0.00</div>
                        </div>

                        <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100/50">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">Total Utilized (Burned)</span>
                                <span class="text-xs font-black text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full" id="fin-utilized-pct-val">0%</span>
                            </div>
                            <div class="text-xl font-extrabold text-purple-900 mt-1" id="fin-utilized-val">₱0.00</div>
                            <div class="w-full bg-purple-100 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div id="fin-utilized-progress-bar" class="h-full bg-purple-600 rounded-full transition-all duration-500" style="width: 0%"></div>
                            </div>
                        </div>

                        <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100/50">
                            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Total Saving / Reserve Balance</span>
                            <div class="text-xl font-extrabold text-emerald-900 mt-1" id="fin-remaining-val">₱0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Visual Progress Breakdown -->
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col">
                    <div class="border-b border-slate-100 pb-4 mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">Operating Unit Budget Variance</h3>
                            <p class="text-xs text-slate-500">Burn analytics comparing original PMT allocations against actual project utilization</p>
                        </div>
                    </div>
                    <!-- Chart Wrapper Canvas -->
                    <div class="relative w-full h-[320px] flex items-center justify-center">
                        <canvas id="financialVarianceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

