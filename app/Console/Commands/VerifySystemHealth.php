<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Laravel\Horizon\Contracts\MetricsRepository;

class VerifySystemHealth extends Command
{
    protected $signature = 'raax:health';
    protected $description = 'Verify system boundaries, RLS, and critical dependencies';

    public function handle(MetricsRepository $metrics): int
    {
        $hasErrors = false;

        $this->info("Running RAAX System Health Checks...\n");

        // 1. PostgreSQL & RLS Verification
        try {
            $this->info('Checking Database Connection & RLS Engine...');
            // Check if connection works
            DB::select('SELECT 1');

            // Verify if app_user context logic is generally functional.
            // Since this runs in CLI, current_tenant_id might not be set.
            // We just ensure we can set and query it if needed.
            if (config('database.default') !== 'sqlite') {
                DB::statement("SET app.current_tenant_id = '00000000-0000-0000-0000-000000000000'");
                $tenantCheck = DB::select("SELECT current_setting('app.current_tenant_id', TRUE) as tenant_id");
                if ($tenantCheck[0]->tenant_id !== '00000000-0000-0000-0000-000000000000') {
                    $this->error('Failed to set or read PostgreSQL session variable for RLS.');
                    $hasErrors = true;
                } else {
                    $this->info('[OK] PostgreSQL Connection and RLS session variables are functional.');
                }
            } else {
                $this->info('[SKIP] SQLite does not support RLS session variables.');
            }
        } catch (\Exception $e) {
            $this->error('[FAIL] Database check failed: ' . $e->getMessage());
            $hasErrors = true;
        }

        $this->info('');

        // 2. Redis Latency
        try {
            $this->info('Checking Redis Memory Pool Latency...');
            $start = microtime(true);
            Redis::ping();
            $latencyMs = (int) ((microtime(true) - $start) * 1000);

            if ($latencyMs > 50) {
                $this->warn("[WARN] Redis responded, but latency is high ({$latencyMs}ms). Threshold is <50ms.");
            } else {
                $this->info("[OK] Redis latency is optimal ({$latencyMs}ms).");
            }
        } catch (\Exception $e) {
            $this->error('[FAIL] Redis check failed: ' . $e->getMessage());
            $hasErrors = true;
        }

        $this->info('');

        // 3. Queue Depths (Horizon)
        try {
            $this->info('Checking Queue Channel Backlogs...');
            // In a real environment, we'd query Horizon metrics. For this CLI we can use Redis queue sizes.
            $queues = ['high', 'default', 'notifications'];
            foreach ($queues as $queue) {
                $size = Redis::llen("queues:{$queue}");
                if ($size > 100) {
                    $this->warn("[WARN] Queue '{$queue}' has a backlog of {$size} pending jobs (Threshold >100).");
                } else {
                    $this->info("[OK] Queue '{$queue}' backlog is healthy ({$size} jobs).");
                }
            }
        } catch (\Exception $e) {
            $this->error('[FAIL] Queue depth check failed: ' . $e->getMessage());
            $hasErrors = true;
        }

        $this->info("\nHealth check complete.");

        return $hasErrors ? 1 : 0;
    }
}
