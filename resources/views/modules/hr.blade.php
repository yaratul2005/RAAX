<!-- Module View: HR & Payroll Engine -->
<div id="view-hr" class="view-panel">
    <!-- Payroll Calculator Simulator & Shift Attendance Bar -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Automated Monthly Payroll Engine</div>
                <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Employee: Abdur Rahman — Net Salary: <span class="mono" style="color:var(--status-green);">BDT 135,000</span> (Basic + Allowances - PF - TDS Tax)</div>
            </div>
            <button class="btn btn-sm" onclick="showToast('Payroll calculation cycle executed cleanly for all 1,200 employees!')"><i class="fa-solid fa-calculator"></i> Run Payroll Cycle</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Attendance Terminal Log</div>
                <div style="font-size:11.5px; color:var(--text-muted);">Biometric Pull Sync</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="showToast('Synced 1,180 Biometric attendance punches from ZKTeco terminals!')"><i class="fa-solid fa-fingerprint"></i> Sync Terminal</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-users"></i> Employee Master Directory & Monthly Payroll Ledger</div>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="employeeTable">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
                <tbody id="employeeTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
