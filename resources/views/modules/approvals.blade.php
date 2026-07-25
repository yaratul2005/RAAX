<!-- Module View: Approval Queue -->
<div id="view-approvals" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-stamp"></i> Pending Multi-Level Workflow Approvals (Maker-Checker & SoD Rules Enforced)</div>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="data-table">
                <thead>
                    <tr><th>Request #</th><th>Workflow Category</th><th>Requester</th><th>Impact Value</th><th>Required Role</th><th>Status</th><th>Decision Action</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="mono">REQ-1024</td>
                        <td>PO Price Tolerance Exceeded (>10%)</td>
                        <td>A. Rahman</td>
                        <td class="mono">BDT 1,250,000</td>
                        <td><span class="status-chip posted">Executive Manager</span></td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="showToast('Request REQ-1024 Approved!')"><i class="fa-solid fa-check"></i> Approve</button>
                            <button class="btn btn-outline btn-sm" style="color:var(--status-red);" onclick="showToast('Request REQ-1024 Rejected.')"><i class="fa-solid fa-xmark"></i> Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">REQ-1025</td>
                        <td>Customer Credit Limit Override</td>
                        <td>M. Hossain</td>
                        <td class="mono">BDT 850,000</td>
                        <td><span class="status-chip posted">CFO / Finance Control</span></td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="showToast('Request REQ-1025 Approved!')"><i class="fa-solid fa-check"></i> Approve</button>
                            <button class="btn btn-outline btn-sm" style="color:var(--status-red);" onclick="showToast('Request REQ-1025 Rejected.')"><i class="fa-solid fa-xmark"></i> Reject</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="mono">REQ-1026</td>
                        <td>Manual Journal Entry Void</td>
                        <td>S. Ahmed</td>
                        <td class="mono">BDT 45,000</td>
                        <td><span class="status-chip posted">Senior Auditor</span></td>
                        <td><span class="status-chip draft">Pending</span></td>
                        <td>
                            <button class="btn btn-sm" onclick="showToast('Request REQ-1026 Approved!')"><i class="fa-solid fa-check"></i> Approve</button>
                            <button class="btn btn-outline btn-sm" style="color:var(--status-red);" onclick="showToast('Request REQ-1026 Rejected.')"><i class="fa-solid fa-xmark"></i> Reject</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
