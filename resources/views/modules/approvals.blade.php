<!-- Module View: Workflow Approvals Queue -->
<div id="view-approvals" class="view-panel">
    <!-- Filter Tabs & Bulk Approve Action Bar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.75rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm active" onclick="filterApprovalQueue('all')">All Pending (3)</button>
            <button class="btn btn-outline btn-sm" onclick="filterApprovalQueue('high')">High Value (&gt;500k BDT)</button>
            <button class="btn btn-outline btn-sm" onclick="filterApprovalQueue('sod')">SoD Rules Guard</button>
        </div>
        <button class="btn btn-sm" onclick="handleBulkApprove()"><i class="fa-solid fa-check-double"></i> Bulk Approve Selected</button>
    </div>

    <div class="card stagger-2">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-stamp"></i> Multi-Tier Executive Approval Workflows (Segregation of Duties Enforced)</div>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead><tr><th>Request ID</th><th>Workflow Type</th><th>Financial Impact</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="approvalQueueBody">
                    <tr>
                        <td class="mono">REQ-1024</td>
                        <td>PO Price Tolerance (&gt;10% Variance)</td>
                        <td class="mono">BDT 1,250,000</td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="approveWorkflowReq('REQ-1024')"><i class="fa-solid fa-check"></i> Approve</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">REQ-1025</td>
                        <td>Customer Credit Limit Excess</td>
                        <td class="mono">BDT 850,000</td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="approveWorkflowReq('REQ-1025')"><i class="fa-solid fa-check"></i> Approve</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">REQ-1026</td>
                        <td>Manual Journal Entry Void</td>
                        <td class="mono">BDT 45,000</td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="approveWorkflowReq('REQ-1026')"><i class="fa-solid fa-check"></i> Approve</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
