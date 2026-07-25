<!-- Module View: Finance & General Ledger -->
<div id="view-finance" class="view-panel">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-book"></i> General Ledger Entries & Cryptographic SHA-256 Chain</div>
            <button class="btn btn-sm" onclick="showToast('Double-Entry Journal Builder ready.')"><i class="fa-solid fa-plus"></i> + Post Journal Entry</button>
        </div>
        <div class="data-table-container">
            <table class="data-table" id="journalTable">
                <thead><tr><th>Reference</th><th>Date</th><th>Description</th><th>Amount</th><th>SHA-256 Ledger Hash</th></tr></thead>
                <tbody id="journalTableBody"></tbody>
            </table>
        </div>
    </div>
</div>
