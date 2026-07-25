import { app, BrowserWindow, Tray, Menu, ipcMain, dialog, Notification, clipboard, shell, nativeImage } from 'electron';
import { spawn, execSync } from 'child_process';
import http from 'http';
import path from 'path';
import fs from 'fs';
import os from 'os';
import net from 'net';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');

let mainWindow = null;
let splashWindow = null;
let setupWindow = null;
let phpProcess = null;
let tray = null;
let activePort = 8000;
let serverUrl = `http://127.0.0.1:8000`;

// High DPI & hardware acceleration switches
app.commandLine.appendSwitch('high-dpi-support', '1');
app.commandLine.appendSwitch('force-device-scale-factor', '1');

// Locate PHP executable on Windows
function findPhpExecutable() {
    // Check bundled PHP executable if packed
    const bundledPhp = path.join(process.resourcesPath, 'php', 'php.exe');
    if (fs.existsSync(bundledPhp)) return bundledPhp;

    // Common Windows PHP installation locations
    const candidatePaths = [
        'php',
        'C:\\php\\php.exe',
        'C:\\xampp\\php\\php.exe',
        'C:\\wamp64\\bin\\php\\php8.3.0\\php.exe',
        'C:\\wamp64\\bin\\php\\php8.2.0\\php.exe',
        'C:\\Program Files\\php\\php.exe'
    ];

    for (const phpPath of candidatePaths) {
        try {
            execSync(`"${phpPath}" -v`, { stdio: 'ignore' });
            return phpPath;
        } catch (e) {
            // Continue searching
        }
    }
    return 'php'; // Fallback
}

// Find free available TCP port starting from 8000
function getFreePort(startPort) {
    return new Promise((resolve) => {
        const checkPort = (port) => {
            const server = net.createServer();
            server.listen(port, '127.0.0.1', () => {
                server.once('close', () => resolve(port));
                server.close();
            });
            server.on('error', () => checkPort(port + 1));
        };
        checkPort(startPort);
    });
}

// Helper: Poll server readiness
function checkServerReady(url, maxAttempts = 40, interval = 500) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const timer = setInterval(() => {
            attempts++;
            updateBootStatus(Math.min(95, Math.floor((attempts / maxAttempts) * 100)), `Connecting to backend on port ${activePort}... (${attempts})`);
            http.get(url, (res) => {
                if (res.statusCode === 200 || res.statusCode === 302) {
                    clearInterval(timer);
                    resolve(true);
                }
            }).on('error', () => {
                if (attempts >= maxAttempts) {
                    clearInterval(timer);
                    reject(new Error(`Backend failed to start on ${url}`));
                }
            });
        }, interval);
    });
}

// Update boot splash screen
function updateBootStatus(percent, text) {
    if (splashWindow && !splashWindow.isDestroyed()) {
        splashWindow.webContents.send('boot-status', { percent, text });
    }
}

// Start PHP Artisan Serve process
function startPhpServer(phpPath, port) {
    console.log(`[RAAX Desktop] Booting background PHP server on port ${port}...`);
    phpProcess = spawn(phpPath, ['artisan', 'serve', '--host=127.0.0.1', `--port=${port}`], {
        cwd: rootDir,
        shell: true,
        stdio: 'ignore'
    });

    phpProcess.on('error', (err) => console.error('[RAAX Desktop] PHP process error:', err));
    phpProcess.on('exit', (code) => console.log(`[RAAX Desktop] PHP process exited code ${code}`));
}

// Kill background PHP process safely
function killPhpServer() {
    if (phpProcess) {
        console.log('[RAAX Desktop] Terminating PHP server process...');
        if (process.platform === 'win32') {
            try { spawn('taskkill', ['/pid', phpProcess.pid, '/f', '/t']); } catch (e) {}
        } else {
            phpProcess.kill('SIGTERM');
        }
        phpProcess = null;
    }
}

// Create Splash Loading Window
function createSplashWindow() {
    splashWindow = new BrowserWindow({
        width: 420,
        height: 320,
        resizable: false,
        frame: false,
        alwaysOnTop: true,
        backgroundColor: '#09090b',
        webPreferences: { nodeIntegration: true, contextIsolation: false }
    });
    splashWindow.loadFile(path.join(__dirname, 'splash.html'));
}

// Build System Tray Menu
function createTray() {
    const iconPath = path.join(__dirname, 'icon.png');
    let trayImage;

    if (fs.existsSync(iconPath)) {
        trayImage = nativeImage.createFromPath(iconPath);
    } else {
        // Fallback 16x16 orange icon as Data URL PNG
        const fallbackPng = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAERJREFUOE9jZKAQMFKon4FqBhSg0TBJ0kRRNYB0v2BgYGBg+M/AwEC2AWQZAEsz0tTjNACT01A04DYAdQMImUFWoIsCADWpEA212b0hAAAAAElFTkSuQmCC';
        trayImage = nativeImage.createFromDataURL(fallbackPng);
    }

    try {
        tray = new Tray(trayImage);
        
        const contextMenu = Menu.buildFromTemplate([
            { label: 'RAAX ERP Platform v2.0', enabled: false },
            { type: 'separator' },
            { label: 'Show Application', click: () => mainWindow && mainWindow.show() },
            { label: 'Create Purchase Order', click: () => mainWindow && mainWindow.webContents.send('tray-action', 'create-po') },
            { label: 'Create Sales Order', click: () => mainWindow && mainWindow.webContents.send('tray-action', 'create-so') },
            { label: 'System Telemetry', click: () => mainWindow && mainWindow.webContents.send('tray-action', 'telemetry') },
            { type: 'separator' },
            { label: 'Exit Application', click: () => app.quit() }
        ]);

        tray.setToolTip('RAAX Enterprise Resource Planning');
        tray.setContextMenu(contextMenu);
        tray.on('double-click', () => mainWindow && mainWindow.show());
    } catch (err) {
        console.warn('[RAAX Desktop] Warning initializing tray:', err.message);
    }
}

// Create Main Application Window
async function createMainWindow() {
    createSplashWindow();
    updateBootStatus(20, 'Locating PHP 8.3 Runtime...');

    const phpPath = findPhpExecutable();
    updateBootStatus(40, 'Allocating background server port...');

    activePort = await getFreePort(8000);
    serverUrl = `http://127.0.0.1:${activePort}`;

    updateBootStatus(60, `Starting RAAX Monolith on 127.0.0.1:${activePort}...`);
    startPhpServer(phpPath, activePort);

    try {
        await checkServerReady(serverUrl);
        updateBootStatus(100, 'Loading Desktop Interface...');

        mainWindow = new BrowserWindow({
            width: 1366,
            height: 850,
            minWidth: 1024,
            minHeight: 700,
            title: 'RAAX Enterprise Resource Planning Platform',
            backgroundColor: '#09090b',
            show: false,
            autoHideMenuBar: true,
            webPreferences: {
                preload: path.join(__dirname, 'preload.js'),
                nodeIntegration: false,
                contextIsolation: true
            }
        });

        mainWindow.loadURL(serverUrl);

        mainWindow.once('ready-to-show', () => {
            if (splashWindow && !splashWindow.isDestroyed()) splashWindow.close();
            mainWindow.show();
            createTray();
        });

        // Global shortcuts within window
        mainWindow.webContents.on('before-input-event', (event, input) => {
            if (input.control && input.key.toLowerCase() === 'p') {
                event.preventDefault();
                mainWindow.webContents.print({ printBackground: true });
            }
        });

        mainWindow.on('closed', () => { mainWindow = null; });
    } catch (err) {
        if (splashWindow && !splashWindow.isDestroyed()) splashWindow.close();
        dialog.showErrorBox('RAAX ERP Startup Error', err.message);
    }
}

// Register Native IPC Handlers
function setupIpcHandlers() {
    // Hardware & System Telemetry
    ipcMain.handle('get-hardware-info', async () => {
        return {
            platform: process.platform,
            arch: process.arch,
            cpus: os.cpus().map(c => c.model),
            cpuCount: os.cpus().length,
            totalMemGB: (os.totalmem() / (1024 ** 3)).toFixed(2),
            freeMemGB: (os.freemem() / (1024 ** 3)).toFixed(2),
            hostname: os.hostname(),
            uptimeHours: (os.uptime() / 3600).toFixed(1),
            osRelease: os.release(),
            serverPort: activePort
        };
    });

    // Native Notifications
    ipcMain.on('show-native-notification', (event, { title, body }) => {
        if (Notification.isSupported()) {
            new Notification({ title: title || 'RAAX ERP', body: body || '' }).show();
        }
    });

    // Native Printer Dialog
    ipcMain.handle('print-document', async (event, options) => {
        if (!mainWindow) return false;
        mainWindow.webContents.print(options || { printBackground: true });
        return true;
    });

    // Save File Dialog
    ipcMain.handle('show-save-dialog', async (event, options) => {
        if (!mainWindow) return null;
        const res = await dialog.showSaveDialog(mainWindow, options);
        return res.filePath || null;
    });

    // Open File Dialog
    ipcMain.handle('show-open-dialog', async (event, options) => {
        if (!mainWindow) return null;
        const res = await dialog.showOpenDialog(mainWindow, options);
        return res.filePaths || null;
    });

    // Windows Clipboard
    ipcMain.on('copy-to-clipboard', (event, text) => clipboard.writeText(text));
    ipcMain.handle('read-clipboard', () => clipboard.readText());

    // Backend Status
    ipcMain.handle('get-backend-status', async () => {
        return { port: activePort, url: serverUrl, status: 'running' };
    });
}

app.whenReady().then(() => {
    setupIpcHandlers();
    createMainWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) createMainWindow();
    });
});

app.on('window-all-closed', () => {
    killPhpServer();
    if (process.platform !== 'darwin') app.quit();
});

app.on('before-quit', () => killPhpServer());
