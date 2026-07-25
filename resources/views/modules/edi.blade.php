<!-- Module View: EDI Integration -->
<div id="view-edi" class="view-panel">
    <!-- EDI X12 Message Inspector Bar -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">EDI Electronic Data Interchange Engine (ANSI X12)</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Inbound Queue: <span class="mono">EDI 850 (PO)</span> | Outbound Queue: <span class="mono">EDI 855 (Ack) & EDI 856 (Ship Notice)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-outline btn-sm" onclick="showToast('Outbound EDI 856 Ship Notice dispatched to TransGlobal partner!')"><i class="fa-solid fa-paper-plane"></i> Send EDI 856</button>
            <button class="btn btn-sm" onclick="showToast('Inbound EDI 850 orders polled and imported cleanly!')"><i class="fa-solid fa-satellite-dish"></i> Poll EDI Receiver</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-network-wired"></i> EDI Electronic Data Interchange Mapper Logs</div>
        </div>
        <div class="card-body">
            <div class="terminal-box"><span class="hl-orange">[EDI X12 Standard Orders Receiver]</span>
- EDI 850 (Purchase Order Inbound): Recv order PO-88912 from Customer TransGlobal
- EDI 855 (PO Ack): Sent confirmation ACK-88912
- EDI 856 (Ship Notice / Manifest): Outbound manifest ready</div>
        </div>
    </div>
</div>
