<!-- Module View: EDI Integration -->
<div id="view-edi" class="view-panel">
    <!-- ANSI X12 EDI Gateway Toolbar -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">ANSI X12 B2B EDI Integration Engine</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">ISA Identifier: <span class="mono">ZZ:RAAXERP</span> | Protocol: <span class="mono">AS2 / SFTP Encrypted</span> | Status: <span style="color:var(--status-green); font-weight:700;">ONLINE (Queue Clear)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="showToast('Polled inbound AS2 EDI queue: 0 new documents pending.')"><i class="fa-solid fa-sync"></i> Poll EDI Receiver</button>
            <button class="btn btn-sm" onclick="openCodeEditor('ANSI X12 EDI 856 Advanced Ship Notice', 'ISA*00*          *00*          *ZZ*RAAXERP        *ZZ*APEXCORP       *260725*1200*U*00401*000000001*0*P*>~\nGS*SH*RAAXERP*APEXCORP*20260725*1200*1*X*004010~\nST*856*0001~\nBSN*00*ASN-2026-9912*20260725*1200~\nHL*1**S~\nTD5**2*FDEG*M~\nREF*BM*BOL-99120~\nSE*6*0001~\nGE*1*1~\nIEA*1*000000001~', 'ANSI X12 Payload')"><i class="fa-solid fa-paper-plane"></i> Send EDI 856 ASN</button>
        </div>
    </div>

    <!-- ANSI X12 Message Parser & Inspector Card -->
    <div class="card stagger-2" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-network-wired"></i> Raw ANSI X12 Segment Message Parser (EDI 850 / 855 / 856)</div>
            <button class="btn btn-outline btn-sm" onclick="openCodeEditor('Raw ANSI X12 Segment Message', 'ISA*00*...~', 'ANSI X12 Specification')"><i class="fa-solid fa-code"></i> Open Code Inspector</button>
        </div>
        <div class="card-body">
            <div class="terminal-box" style="height:170px;"><span class="hl-orange">[INBOUND TRANSMISSION RECV] AS2 Payload #EDI-850-2026-0044</span>
ISA*00*          *00*          *ZZ*APEXCORP       *ZZ*RAAXERP        *260725*1145*U*00401*000000044*0*P*>~
GS*PO*APEXCORP*RAAXERP*20260725*1145*44*X*004010~
ST*850*0001~
BEG*00*SA*PO-APEX-9912**20260725~
PO1*1*100*EA*7391.30**BP*SKU-RAW-STEEL~
CTT*1~
SE*6*0001~
<span class="hl-green">[PARSER RESULT] Successfully parsed EDI 850 Purchase Order PO-APEX-9912. Converted to RAAX SalesOrder #SO-2026-4412.</span></div>
        </div>
    </div>

    <!-- EDI Transmission Log Directory Table -->
    <div class="card stagger-3">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-list-check"></i> Recent EDI Message Transmission Logs</div>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead><tr><th>Control #</th><th>EDI Transaction</th><th>Partner ID</th><th>Direction</th><th>Timestamp</th><th>Status</th></tr></thead>
                <tbody>
                    <tr>
                        <td class="mono">000000044</td>
                        <td>850 Purchase Order</td>
                        <td>ZZ:APEXCORP</td>
                        <td><span style="color:var(--status-blue); font-weight:700;">INBOUND</span></td>
                        <td>2026-07-25 11:45</td>
                        <td><span class="status-chip active">ACKNOWLEDGED</span></td>
                    </tr>
                    <tr>
                        <td class="mono">000000045</td>
                        <td>855 PO Acknowledgment</td>
                        <td>ZZ:APEXCORP</td>
                        <td><span style="color:var(--orange-brand); font-weight:700;">OUTBOUND</span></td>
                        <td>2026-07-25 11:46</td>
                        <td><span class="status-chip active">SENT_OK</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
