<!-- TAB: ADMIN SETTINGS -->
        <div id="tab-settings" class="tab-content hidden flex flex-col gap-6">
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 flex flex-col gap-6 max-w-xl">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-user-shield text-slate-600"></i> Account Settings
                    </h3>
                    <p class="text-xs text-slate-500">Signed in as <span class="font-bold text-slate-700"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span></p>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">Change Password</h4>
                    <form id="change-password-form" onsubmit="handleChangePassword(event)" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Current Password</label>
                            <input type="password" id="current-password" required class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">New Password</label>
                            <input type="password" id="new-password" required minlength="8" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                            <p class="text-[10px] text-slate-400 mt-1">Minimum 8 characters.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase mb-1.5">Confirm New Password</label>
                            <input type="password" id="confirm-password" required minlength="8" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-dict-bright focus:outline-none">
                        </div>
                        <div id="change-password-error" class="hidden p-3 bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold rounded-lg"></div>
                        <div class="border-t border-slate-100 pt-4 flex justify-end">
                            <button type="submit" class="px-5 py-2 bg-dict-blue hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>