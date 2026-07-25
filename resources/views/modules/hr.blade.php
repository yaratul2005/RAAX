<!-- Module View: HR & Payroll Engine -->
<div id="view-hr" class="view-panel">
    <!-- Active Headcount & Biometric TCP Sync Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">HR Directory & Automated Payroll Engine</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Active Headcount: <span class="mono" style="color:var(--status-green);">1,200 Employees</span> | Attendance Rate: <span class="mono" style="color:var(--status-green);">98.4%</span> | Biometric TCP Daemon: <span style="color:var(--status-green); font-weight:700;">Port 4370 Active</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('biometricSyncModal').classList.add('open')"><i class="fa-solid fa-sync"></i> Sync Biometric Terminal</button>
            <button class="btn btn-sm" onclick="document.getElementById('payrollCycleModal').classList.add('open')"><i class="fa-solid fa-calculator"></i> Run Payroll Cycle</button>
        </div>
    </div>

    <!-- Payroll Structure Calculator Card -->
    <div class="card stagger-2" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-calculator"></i> Monthly Payroll Structure & TDS Income Tax Slab Simulator</div>
        </div>
        <div class="card-body">
            <div class="kpi-grid" style="margin-bottom:1rem;">
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Gross Monthly Payroll</div>
                    <div style="font-size:18px; font-weight:700; color:var(--text-pure);">BDT 36.5M</div>
                    <div style="font-size:10px; color:var(--text-muted);">1,200 Employees</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">10% Provident Fund (PF)</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-blue);">BDT 3.65M</div>
                    <div style="font-size:10px; color:var(--status-green);">Employer Matching Active</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">TDS Income Tax Deduction</div>
                    <div style="font-size:18px; font-weight:700; color:var(--orange-brand);">BDT 2.18M</div>
                    <div style="font-size:10px; color:var(--text-muted);">Withheld for NBR</div>
                </div>
                <div style="background:#09090b; padding:10px; border-radius:5px; border:1px solid var(--border-subtle);">
                    <div style="font-size:10px; color:var(--text-dim); text-transform:uppercase;">Net Bank Disbursement</div>
                    <div style="font-size:18px; font-weight:700; color:var(--status-green);">BDT 30.67M</div>
                    <div style="font-size:10px; color:var(--status-green);">BEFTN Spooler Ready</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Master Directory Table -->
    <div class="card stagger-3">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-users"></i> Employee Master Directory</div>
            <button class="btn btn-sm" onclick="document.getElementById('employeeOnboardModal').classList.add('open')"><i class="fa-solid fa-user-plus"></i> + Onboard Employee</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="employeeTable">
                <thead><tr><th>Full Name</th><th>Corporate Email</th><th>Phone Contact</th><th>Status</th></tr></thead>
                <tbody id="employeeTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
