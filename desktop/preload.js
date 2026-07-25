const { contextBridge, ipcRenderer } = require('electron');

// Expose safe native Windows desktop APIs to the web renderer
contextBridge.exposeInMainWorld('raax', {
    // Platform info
    isDesktop: true,
    platform: process.platform,

    // Hardware & System Telemetry
    getHardwareInfo: () => ipcRenderer.invoke('get-hardware-info'),
    getBatteryStatus: () => ipcRenderer.invoke('get-battery-status'),

    // Native Notifications
    notify: (title, body, icon) => ipcRenderer.send('show-native-notification', { title, body, icon }),

    // Native Printer Dialog
    printDocument: (options) => ipcRenderer.invoke('print-document', options),

    // Native File Dialogs (CSV, PDF, Export)
    showSaveDialog: (options) => ipcRenderer.invoke('show-save-dialog', options),
    showOpenDialog: (options) => ipcRenderer.invoke('show-open-dialog', options),

    // Windows Clipboard Integration
    copyToClipboard: (text) => ipcRenderer.send('copy-to-clipboard', text),
    readClipboard: () => ipcRenderer.invoke('read-clipboard'),

    // Window Management
    openModuleWindow: (moduleName) => ipcRenderer.send('open-module-window', moduleName),
    toggleAlwaysOnTop: () => ipcRenderer.invoke('toggle-always-on-top'),

    // Backend Status & Setup IPC
    getBackendStatus: () => ipcRenderer.invoke('get-backend-status'),
    runDatabaseSetup: (config) => ipcRenderer.invoke('run-database-setup', config),
    restartBackend: () => ipcRenderer.invoke('restart-backend'),

    // Event Listeners (e.g. Barcode scanner HID, System Tray actions)
    onBarcodeScan: (callback) => {
        ipcRenderer.on('barcode-scanned', (event, barcode) => callback(barcode));
    },
    onTrayAction: (callback) => {
        ipcRenderer.on('tray-action', (event, action) => callback(action));
    }
});
