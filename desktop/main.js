import { app, BrowserWindow, Tray, Menu, ipcMain } from 'electron';
import { spawn } from 'child_process';
import http from 'http';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

let mainWindow = null;
let phpProcess = null;
let tray = null;
const SERVER_PORT = 8000;
const SERVER_URL = `http://127.0.0.1:${SERVER_PORT}`;

// Helper: Check if Laravel server is running and responding
function checkServerReady(url, maxAttempts = 30, interval = 500) {
    return new Promise((resolve, reject) => {
        let attempts = 0;
        const timer = setInterval(() => {
            attempts++;
            http.get(url, (res) => {
                if (res.statusCode === 200 || res.statusCode === 302) {
                    clearInterval(timer);
                    resolve(true);
                }
            }).on('error', () => {
                if (attempts >= maxAttempts) {
                    clearInterval(timer);
                    reject(new Error('Server failed to start within timeout.'));
                }
            });
        }, interval);
    });
}

// Start PHP Artisan Serve in background
function startPhpServer() {
    const rootDir = path.resolve(__dirname, '..');
    const phpExecutable = 'php'; // Assumes php is in PATH, or bundled php.exe in production

    console.log('[Desktop] Starting background RAAX ERP server...');
    phpProcess = spawn(phpExecutable, ['artisan', 'serve', '--host=127.0.0.1', `--port=${SERVER_PORT}`], {
        cwd: rootDir,
        shell: true,
        stdio: 'ignore'
    });

    phpProcess.on('error', (err) => {
        console.error('[Desktop] Failed to start PHP process:', err);
    });

    phpProcess.on('exit', (code) => {
        console.log(`[Desktop] PHP server process exited with code ${code}`);
    });
}

// Stop PHP Artisan Serve process on quit
function killPhpServer() {
    if (phpProcess) {
        console.log('[Desktop] Terminating background PHP server process...');
        if (process.platform === 'win32') {
            spawn('taskkill', ['/pid', phpProcess.pid, '/f', '/t']);
        } else {
            phpProcess.kill('SIGTERM');
        }
        phpProcess = null;
    }
}

// Create native Electron application window
async function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1366,
        height: 850,
        minWidth: 1024,
        minHeight: 700,
        title: 'RAAX Enterprise Resource Planning Platform',
        backgroundColor: '#080c14',
        show: false,
        autoHideMenuBar: true,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true
        }
    });

    startPhpServer();

    try {
        await checkServerReady(SERVER_URL);
        console.log('[Desktop] Server is ready! Loading window...');
        mainWindow.loadURL(SERVER_URL);
        mainWindow.once('ready-to-show', () => {
            mainWindow.show();
        });
    } catch (err) {
        console.error('[Desktop] Error launching app:', err.message);
        mainWindow.loadURL(`data:text/html,
            <html>
            <body style="background:#080c14;color:#f8fafc;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;">
                <h2>RAAX ERP Desktop Server Error</h2>
                <p>${err.message}</p>
                <p>Ensure PHP 8.3 is installed and accessible in your system PATH.</p>
            </body>
            </html>
        `);
        mainWindow.show();
    }

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

app.whenReady().then(() => {
    createWindow();

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) createWindow();
    });
});

app.on('window-all-closed', () => {
    killPhpServer();
    if (process.platform !== 'darwin') app.quit();
});

app.on('before-quit', () => {
    killPhpServer();
});
