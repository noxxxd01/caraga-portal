    <!-- TOP EXECUTIVE BRANDED BAR -->
    <header class="bg-dict-navy text-white sticky top-0 z-[1000] border-b border-slate-800 shadow-xl">
        <div class="max-w-[1700px] mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-4">
            
            <!-- Branding Matrix -->
            <div class="flex items-center space-x-3">
                <div class="bg-white p-2 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="h-8 w-8 text-dict-blue" viewBox="0 0 100 100" fill="currentColor">
                        <polygon points="50,10 90,30 90,70 50,90 10,70 10,30" fill="#0B1E36"/>
                        <polygon points="50,18 82,34 82,66 50,82 18,66 18,34" fill="#2563EB"/>
                        <circle cx="50" cy="50" r="15" fill="#F59E0B"/>
                        <path d="M43,45 H57 V48 H50 V55 H43 Z" fill="#FFFFFF"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-bold tracking-tight">DICT CARAGA REGION</h1>
                        <span class="bg-emerald-500/10 text-emerald-400 text-[10px] font-bold px-2 py-0.5 rounded-full border border-emerald-500/20 uppercase tracking-widest">XAMPP Persistent Server</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">Training Management Division • Provincial Allocations & GIS Analytics Portal</p>
                </div>
            </div>

            <!-- Global Target Control Center (Auto-calculated from SQL Provincial Target sum) -->
            <div class="flex items-center bg-slate-900/60 rounded-xl px-4 py-2 border border-slate-800 gap-4">
                <div class="flex items-center gap-2 bg-slate-950 px-3 py-1.5 rounded-lg border border-slate-700">
                    <span id="annual-target-display" class="text-sm font-black text-white">0</span>
                    <span class="text-[10px] text-slate-400">Trainings</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[10px] text-slate-400">
                        Signed in as <span class="font-bold text-white"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                    </span>
                    <a href="logout.php" class="text-[10px] font-bold text-rose-400 hover:text-rose-300 border border-slate-700 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-all">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Sign Out
                    </a>
                </div>
            </div>

            

            <!-- Navigation Portals (ORDER SWAPPED FOR PMT AND API EXPLORER) -->
            <div class="flex items-center gap-1">
                <button onclick="setActiveTab('dashboard')" id="nav-btn-dashboard" class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-dict-bright text-white transition-all">
                    <i class="fa-solid fa-chart-line mr-1"></i> Executive Dashboard
                </button>
                <button onclick="setActiveTab('tracker')" id="nav-btn-tracker" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-list-check mr-1"></i> Training Tracker & CRUD
                </button>
                <button onclick="setActiveTab('participants')" id="nav-btn-participants" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-users-viewfinder mr-1 text-emerald-400"></i> Participants Penetration
                </button>
                <button onclick="setActiveTab('financial')" id="nav-btn-financial" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-file-invoice-dollar mr-1"></i> Financial Monitoring Ledger
                </button>
                <button onclick="setActiveTab('pmt-downloads')" id="nav-btn-pmt-downloads" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700/50 transition-all">
                    <i class="fa-solid fa-cloud-arrow-down mr-1 text-amber-400"></i> PMT Downloads Registry
                </button>
                <button onclick="setActiveTab('settings')" id="nav-btn-settings" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-slate-300 hover:text-white transition-all">
                    <i class="fa-solid fa-gear mr-1"></i> Settings
                </button>
            </div>
        </div>
    </header>

