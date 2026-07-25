<!-- Module View: Devices & Peripherals -->
<div id="view-telemetry" class="view-panel">
    <!-- ESC/POS Receipt Print Tester & Barcode HID Listener Bar -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">ESC/POS Thermal Receipt Spooler</div>
                <div style="font-size:12px; color:var(--text-pure); margin-top:2px;">winspool.drv Native DLL Binding</div>
            </div>
            <button class="btn btn-sm" onclick="showToast('Dispatched test thermal receipt via winspool.drv native DLL!')"><i class="fa-solid fa-print"></i> Test Print Receipt</button>
        </div>

        <div style="background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">USB HID Barcode Scanner Listener</div>
                <div style="font-size:12px; color:var(--text-pure); margin-top:2px;">Listen for COM / HID Scan Data</div>
            </div>
            <button class="btn btn-outline btn-sm" onclick="showToast('Barcode scanner listener ACTIVE! Scanned: SKU-FASTENER-A')"><i class="fa-solid fa-barcode"></i> Listen Scanner</button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-desktop"></i> Devices & Peripherals (ESC/POS Printer & Barcode Scanner Integration)</div>
            <button class="btn btn-outline btn-sm" onclick="loadHardwareMetrics()"><i class="fa-solid fa-rotate"></i> Query Hardware Diagnostics</button>
        </div>
        <div class="card-body">
            <div id="telemetryOutput" class="terminal-box">Querying Windows hardware diagnostics & native RAAX_Native_Hardware.dll...</div>
        </div>
    </div>
</div>
