        <!-- TAB: SIMULATED REST API -->
        <div id="tab-api-explorer" class="tab-content hidden flex flex-col gap-6">
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 flex flex-col gap-4">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-sm">Laravel 12 / PHP 8.3 REST API Simulation Interface</h3>
                    <p class="text-xs text-slate-500">Live JSON response outputs matching the specified `DashboardController::getAnalyticsData` specification</p>
                </div>

                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden flex flex-col">
                    <!-- API Address Bar Simulator -->
                    <div class="bg-slate-900 text-slate-400 px-4 py-2 text-xs font-mono flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="bg-emerald-500 text-slate-950 px-1.5 py-0.5 rounded font-bold">GET</span>
                            <span>http://dict-caraga.intranet/api/analytics/dashboard-data</span>
                        </div>
                        <span class="text-slate-500 text-[10px]">JSON Payload Output</span>
                    </div>

                    <!-- Code Display Area -->
                    <pre class="p-4 text-slate-300 bg-slate-950 font-mono text-[11px] h-[360px] overflow-y-auto custom-scrollbar" id="api-output-payload">
                        <!-- Simulated json populated dynamically -->
                    </pre>
                </div>
            </section>
        </div>

