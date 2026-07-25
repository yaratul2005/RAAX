<!-- Admin-Only System Settings View Component -->
<div id="view-settings" class="view-panel">
    <!-- Header & Auto Setup Tool -->
    <div class="stagger-1" style="display:flex; justify-content:space-between; align-items:center; background:var(--card-bg); border:1px solid var(--border-subtle); padding:0.85rem 1rem; border-radius:6px; margin-bottom:1.25rem; flex-wrap:wrap; gap:10px;">
        <div>
            <div style="font-size:10.5px; font-weight:700; color:var(--text-dim); text-transform:uppercase;">Admin System Control & User RBAC Management</div>
            <div style="font-size:12.5px; font-weight:600; color:var(--text-pure); margin-top:2px;">Logged in as: <span class="mono" style="color:var(--orange-brand);">adminRAAX (System Owner)</span> | Active Engine: <span class="mono" id="current-db-engine">PostgreSQL 16 (RLS Active)</span></div>
        </div>
        <div style="display:flex; gap:6px;">
            <button class="btn btn-sm" onclick="runAutoDbSetup()"><i class="fa-solid fa-wand-magic-sparkles"></i> [Auto Setup Database]</button>
        </div>
    </div>

    <!-- User Account Creation Tool for Owner (adminRAAX) -->
    <div class="card stagger-2" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-user-plus"></i> Owner Tool: Create New User Account & Assign Roles</div>
        </div>
        <div class="card-body">
            <form id="createUserAccountForm" onsubmit="handleCreateUser(event)">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Full Employee Name</label>
                        <input type="text" id="newUserName" class="form-input" placeholder="e.g. Tariq Hasan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Corporate Email Address</label>
                        <input type="email" id="newUserEmail" class="form-input mono" placeholder="e.g. t.hasan@raax.com" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Login Username</label>
                        <input type="text" id="newUserUsername" class="form-input mono" placeholder="e.g. thasan" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Password</label>
                        <input type="password" id="newUserPassword" class="form-input mono" value="RAAX2026pass" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Assigned Role</label>
                        <select id="newUserRole" class="form-select mono">
                            <option value="Super Admin">Super Admin (Full System Access)</option>
                            <option value="CFO / Finance Head">CFO / Finance Head (General Ledger & VAT)</option>
                            <option value="Procurement Manager">Procurement Manager (POs & 3-Way Match)</option>
                            <option value="Warehouse Clerk">Warehouse Clerk (Stock & Bin Transfers)</option>
                            <option value="Compliance Auditor">Compliance Auditor (Read-Only Audit Logs)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Branch Tenant Partition</label>
                        <select id="newUserTenant" class="form-select mono">
                            <option value="aca9ea90-0d0f-4ed9-98ed-398af6b67efd">RAAX HQ Holding Entity</option>
                            <option value="b1000000-0000-0000-0000-000000000002">RAAX Chittagong Branch</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn" style="width:100%; justify-content:center;"><i class="fa-solid fa-user-check"></i> Provision User Profile & Enable Credentials</button>
            </form>
        </div>
    </div>

    <!-- Granular Roles & Permissions Capability Matrix Card -->
    <div class="card stagger-3" style="margin-bottom:1.25rem;">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-shield-halved"></i> Granular Roles & Permissions Capability Matrix</div>
            <button class="btn btn-outline btn-sm" onclick="showToast('RBAC Matrix saved cleanly to database!')"><i class="fa-solid fa-floppy-disk"></i> Save Permissions Matrix</button>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead><tr><th>System Role</th><th>Sales CRUD</th><th>PO Approval</th><th>GL Ledger Post</th><th>VAT Mushak Sign</th><th>Audit Export</th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>Super Admin (Owner)</strong></td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                    </tr>
                    <tr>
                        <td><strong>CFO / Finance Head</strong></td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Read-Only</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Full</td>
                    </tr>
                    <tr>
                        <td><strong>Procurement Officer</strong></td>
                        <td><i class="fa-solid fa-circle-xmark" style="color:var(--text-dim);"></i> None</td>
                        <td><i class="fa-solid fa-circle-check" style="color:var(--status-green);"></i> Create/Edit</td>
                        <td><i class="fa-solid fa-circle-xmark" style="color:var(--text-dim);"></i> None</td>
                        <td><i class="fa-solid fa-circle-xmark" style="color:var(--text-dim);"></i> None</td>
                        <td><i class="fa-solid fa-circle-xmark" style="color:var(--text-dim);"></i> None</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Database Connection Form & Terminal Output Grid -->
    <div class="grid-2 stagger-3">
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
