using System;
using System.Diagnostics;
using System.IO;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;
using System.Windows.Forms;
using System.Drawing;

namespace RAAX.ERP.Launcher
{
    static class Program
    {
        private static Mutex mutex = new Mutex(true, "{RAAX-ERP-ENTERPRISE-MUTEX-2026}");
        private static Process phpProcess = null;
        private static Process electronProcess = null;
        private static int activePort = 8000;

        [STAThread]
        static void Main()
        {
            // Ensure Single Instance Application
            if (!mutex.WaitOne(TimeSpan.Zero, true))
            {
                MessageBox.Show("RAAX ERP Enterprise Platform is already running.", "RAAX ERP", MessageBoxButtons.OK, MessageBoxIcon.Information);
                return;
            }

            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);

            string appDir = AppDomain.CurrentDomain.BaseDirectory;
            Directory.SetCurrentDirectory(appDir);

            SplashForm splash = new SplashForm();
            splash.Show();
            Application.DoEvents();

            try
            {
                splash.UpdateProgress(15, "Checking Environment & Runtime...");

                // 1. Check & Copy .env
                string envPath = Path.Combine(appDir, ".env");
                string envExample = Path.Combine(appDir, ".env.example");
                if (!File.Exists(envPath) && File.Exists(envExample))
                {
                    File.Copy(envExample, envPath);
                    RunHiddenCommand("php", "artisan key:generate --force");
                }

                // 2. Check Database Storage
                string dbPath = Path.Combine(appDir, "database", "database.sqlite");
                string dbDir = Path.GetDirectoryName(dbPath);
                if (!Directory.Exists(dbDir)) Directory.CreateDirectory(dbDir);
                if (!File.Exists(dbPath))
                {
                    splash.UpdateProgress(30, "Initializing Local Enterprise Database...");
                    File.Create(dbPath).Close();
                    RunHiddenCommand("php", "artisan migrate --force --seed");
                }

                // 3. Find Free Port
                splash.UpdateProgress(45, "Allocating Server TCP Port...");
                activePort = GetFreeTcpPort(8000);

                // 4. Boot PHP Server Engine Silently
                splash.UpdateProgress(60, "Booting RAAX Monolith Engine on 127.0.0.1:" + activePort + "...");
                StartPhpEngine(activePort);

                // 5. Wait for Server Health
                splash.UpdateProgress(80, "Verifying Engine REST Endpoints...");
                bool ready = WaitUntilServerReady("http://127.0.0.1:" + activePort + "/api/v1/system/health", 30);

                splash.UpdateProgress(100, "Launching Native Desktop Interface...");
                Thread.Sleep(500);
                splash.Close();

                // 6. Launch Electron Desktop Shell or Web View
                StartElectronShell(activePort);
            }
            catch (Exception ex)
            {
                splash.Close();
                MessageBox.Show("RAAX ERP Desktop Startup Error:\n" + ex.Message, "RAAX ERP Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                KillPhpEngine();
            }
        }

        private static int GetFreeTcpPort(int startPort) {
            int port = startPort;
            while (port < startPort + 100) {
                try {
                    TcpListener l = new TcpListener(IPAddress.Loopback, port);
                    l.Start();
                    l.Stop();
                    return port;
                } catch {
                    port++;
                }
            }
            return startPort;
        }

        private static void StartPhpEngine(int port) {
            ProcessStartInfo psi = new ProcessStartInfo();
            psi.FileName = "php";
            psi.Arguments = "artisan serve --host=127.0.0.1 --port=" + port;
            psi.CreateNoWindow = true;
            psi.UseShellExecute = false;
            psi.WindowStyle = ProcessWindowStyle.Hidden;

            phpProcess = Process.Start(psi);
        }

        private static void KillPhpEngine() {
            if (phpProcess != null && !phpProcess.HasExited) {
                try { phpProcess.Kill(); } catch {}
            }
        }

        private static bool WaitUntilServerReady(string url, int timeoutSeconds) {
            int attempts = 0;
            while (attempts < timeoutSeconds * 2) {
                try {
                    HttpWebRequest req = (HttpWebRequest)WebRequest.Create(url);
                    req.Timeout = 1000;
                    using (HttpWebResponse resp = (HttpWebResponse)req.GetResponse()) {
                        if (resp.StatusCode == HttpStatusCode.OK) return true;
                    }
                } catch {}
                Thread.Sleep(500);
                attempts++;
            }
            return true;
        }

        private static void StartElectronShell(int port) {
            ProcessStartInfo psi = new ProcessStartInfo();
            psi.FileName = "cmd.exe";
            psi.Arguments = "/c npx electron .";
            psi.CreateNoWindow = true;
            psi.UseShellExecute = false;

            electronProcess = Process.Start(psi);
            electronProcess.WaitForExit();
        }

        private static void RunHiddenCommand(string exe, string args) {
            ProcessStartInfo psi = new ProcessStartInfo(exe, args);
            psi.CreateNoWindow = true;
            psi.UseShellExecute = false;
            Process p = Process.Start(psi);
            p.WaitForExit();
        }
    }

    public class SplashForm : Form
    {
        private ProgressBar progressBar;
        private Label lblStatus;
        private Label lblTitle;

        public SplashForm()
        {
            this.FormBorderStyle = FormBorderStyle.None;
            this.StartPosition = FormStartPosition.CenterScreen;
            this.Size = new Size(420, 240);
            this.BackColor = Color.FromArgb(9, 9, 11);
            this.ShowInTaskbar = false;
            this.TopMost = true;

            lblTitle = new Label();
            lblTitle.Text = "RAAX ENTERPRISE ERP";
            lblTitle.Font = new Font("Segoe UI", 16, FontStyle.Bold);
            lblTitle.ForeColor = Color.FromArgb(255, 94, 0);
            lblTitle.Location = new Point(30, 40);
            lblTitle.AutoSize = true;
            this.Controls.Add(lblTitle);

            Label lblSub = new Label();
            lblSub.Text = "Native Windows Commercial Edition";
            lblSub.Font = new Font("Segoe UI", 9, FontStyle.Regular);
            lblSub.ForeColor = Color.FromArgb(161, 161, 170);
            lblSub.Location = new Point(32, 70);
            lblSub.AutoSize = true;
            this.Controls.Add(lblSub);

            progressBar = new ProgressBar();
            progressBar.Location = new Point(35, 130);
            progressBar.Size = new Size(350, 10);
            progressBar.Style = ProgressBarStyle.Blocks;
            this.Controls.Add(progressBar);

            lblStatus = new Label();
            lblStatus.Text = "Initializing Application...";
            lblStatus.Font = new Font("Consolas", 9, FontStyle.Regular);
            lblStatus.ForeColor = Color.FromArgb(212, 212, 216);
            lblStatus.Location = new Point(35, 150);
            lblStatus.Size = new Size(350, 40);
            this.Controls.Add(lblStatus);
        }

        public void UpdateProgress(int value, string text)
        {
            if (this.InvokeRequired)
            {
                this.Invoke(new Action(() => UpdateProgress(value, text)));
                return;
            }
            progressBar.Value = Math.Min(100, value);
            lblStatus.Text = text;
            Application.DoEvents();
        }
    }
}
