@echo off
TITLE RAAX Enterprise Resource Planning Platform Launcher
COLOR 0A

echo =========================================================================
echo               RAAX ENTERPRISE RESOURCE PLANNING PLATFORM
echo                           PC DESKTOP SERVER
echo =========================================================================
echo.

cd /d "%~dp0"

:: 1. Verify PHP Environment
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not detected in your system PATH.
    echo Please ensure PHP 8.3 is installed and added to environment PATH variables.
    echo.
    pause
    exit /b 1
)

:: 2. Ensure Database and Env Setup
if not exist ".env" (
    echo [SETUP] Copying environment configuration...
    copy .env.example .env >nul
    php artisan key:generate --force
)

if not exist "database\database.sqlite" (
    echo [SETUP] Creating local database storage...
    type nul > database\database.sqlite
    php artisan migrate --force
)

:: 3. Build Assets if needed
if not exist "public\build\manifest.json" (
    echo [SETUP] Compiling production frontend assets...
    call npm run build
)

echo [SERVER] Launching RAAX ERP Standalone Desktop Application...
echo.

call npm run desktop

pause
