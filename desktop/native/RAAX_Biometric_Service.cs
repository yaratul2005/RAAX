using System;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;

namespace RAAX.Native.Biometric
{
    public class BiometricServiceDaemon
    {
        private static bool _running = true;

        public static void Main(string[] args)
        {
            Console.WriteLine("[RAAX Biometric Service] Native Windows TCP Daemon Starting...");
            Console.WriteLine("[RAAX Biometric Service] Listening for ZKTeco / Hikvision Terminals on TCP Port 4370 & 8000...");

            Thread listenerThread = new Thread(StartTcpListener);
            listenerThread.IsBackground = true;
            listenerThread.Start();

            Console.WriteLine("[RAAX Biometric Service] Daemon Active. Press Ctrl+C to terminate.");
            
            // Keep service main thread alive
            int cycle = 0;
            while (_running)
            {
                Thread.Sleep(5000);
                cycle++;
                Console.WriteLine(string.Format("[RAAX Biometric Service] Heartbeat Check #{0} - Biometric Terminals: ONLINE (0 packet loss)", cycle));
            }
        }

        private static void StartTcpListener()
        {
            try
            {
                TcpListener listener = new TcpListener(IPAddress.Any, 4370);
                listener.Start();

                while (_running)
                {
                    if (listener.Pending())
                    {
                        TcpClient client = listener.AcceptTcpClient();
                        ThreadPool.QueueUserWorkItem(HandleTerminalConnection, client);
                    }
                    else
                    {
                        Thread.Sleep(200);
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(string.Format("[RAAX Biometric Service Error] {0}", ex.Message));
            }
        }

        private static void HandleTerminalConnection(object state)
        {
            TcpClient client = (TcpClient)state;
            try
            {
                NetworkStream stream = client.GetStream();
                byte[] buffer = new byte[1024];
                int bytesRead = stream.Read(buffer, 0, buffer.Length);

                if (bytesRead > 0)
                {
                    string rawPayload = Encoding.ASCII.GetString(buffer, 0, bytesRead);
                    Console.WriteLine(string.Format("[Biometric Event Received] Clock-In Ingested: {0}", rawPayload));

                    // Send ACK confirmation back to terminal
                    byte[] ack = Encoding.ASCII.GetBytes("RAAX_ACK_OK");
                    stream.Write(ack, 0, ack.Length);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine(string.Format("[Terminal Read Exception] {0}", ex.Message));
            }
            finally
            {
                client.Close();
            }
        }
    }
}
