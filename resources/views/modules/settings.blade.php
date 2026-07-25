<!-- Admin-Only System Settings View Component -->
<div id="view-settings" class="view-panel">
    <!-- Header & Auto Setup Tool -->
    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Admin System & Database Configuration</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Logged in as: <span class="mono" style="color:var(--orange-brand);">adminRAAX (System Owner)</span> | Active Engine: <span class="mono" id="current-db-engine">PostgreSQL 16 (RLS Active)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-sm" onclick="runAutoDbSetup()"><i class="fa-solid fa-wand-magic-sparkles"></i> [Auto Setup Database]</button>
        </div>
    </div>

    <!-- Database Connection Form & Terminal Output Grid -->
    <div class="grid-2">
        <!-- Left: Database Connection Parameters Form -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-database"></i> Local & Hosted Database Connection Credentials</div>
            </div>
            <div class="card-body">
                <form id="dbConfigForm" onsubmit="handleSaveDbConfig(event)">
                    <div class="form-group">
                        <label class="form-label">Database Target Driver</label>
                        <select id="dbDriver" class="form-select">
                            <option value="pgsql">PostgreSQL 16 (Enterprise RLS - Default)</option>
                            <option value="mongodb">MongoDB 6.0 / Atlas Cluster (Hosted Nosql)</option>
                            <option value="sqlite">Embedded SQLite (Offline Local Buffer)</option>
                        </select>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Database Host / Connection URI</label>
                            <input type="text" id="dbHost" class="form-input mono" value="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Port</label>
                            <input type="text" id="dbPort" class="form-input mono" value="5432">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Database Name</label>
                        <input type="text" id="dbName" class="form-input mono" value="raax_erp_production">
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Database Username</label>
                            <input type="text" id="dbUser" class="form-input mono" value="postgres">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Password</label>
                            <input type="password" id="dbPassword" class="form-input mono" value="••••••••••••">
                        </div>
                    </div>

                    <div style="display:flex; gap:8px; margin-top:10px;">
                        <button type="submit" class="btn" style="flex:1; justify-content:center;"><i class="fa-solid fa-floppy-disk"></i> Save Credentials</button>
                        <button type="button" class="btn btn-outline" onclick="runAutoDbSetup()"><i class="fa-solid fa-bolt"></i> Run Auto DB Setup</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Live Terminal Console Output -->
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-terminal"></i> Database Setup Terminal Console Log</div>
            </div>
            <div class="card-body">
                <div id="dbSetupTerminal" class="terminal-box" style="height:320px;"><span class="hl-orange">[RAAX System Setup Tool]</span> Ready for Database Auto-Setup.
Target Environment: Local / Cloud Hosted DB
Owner Context: adminRAAX

Click "[Auto Setup Database]" to verify connection, execute migrations, and seed initial master ledgers.</div>
            </div>
        </div>
    </div>
</div>
