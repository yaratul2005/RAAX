<!-- Module View: HR & Payroll Engine -->
<div id="view-hr" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-users"></i> Employee Master Directory & Monthly Payroll Engine</div>
            <button class="btn btn-sm" onclick="showToast('Payroll calculation cycle executed cleanly.')"><i class="fa-solid fa-calculator"></i> Run Payroll Cycle</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="employeeTable">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th></tr></thead>
                <tbody id="employeeTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
