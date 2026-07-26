<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\StartupRouterService;
use App\Services\BackupService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StartupRouterAndBackupTest extends TestCase
{
    public function test_startup_router_resolves_landing_view_for_roles(): void
    {
        $userOwner = new User(['name' => 'Owner']);
        $userOwner->is_owner = true;
        $this->assertEquals('executive_dashboard', StartupRouterService::resolveLandingView($userOwner));

        $userNull = null;
        $this->assertEquals('my_workspace', StartupRouterService::resolveLandingView($userNull));
    }

    public function test_mongo_connection_test_validates_uri(): void
    {
        $res = BackupService::testMongoConnection('mongodb+srv://user:pass@cluster0.mongodb.net/db');
        $this->assertTrue($res['success']);
        $this->assertStringContainsString('verified successfully', $res['message']);

        $resEmpty = BackupService::testMongoConnection('');
        $this->assertFalse($resEmpty['success']);
    }

    public function test_backup_now_executes_online_snapshot_and_gzip(): void
    {
        $res = BackupService::backupNow('mongodb://localhost:27017', false);
        $this->assertTrue($res['success']);
        $this->assertArrayHasKey('sha256_checksum', $res);
        $this->assertArrayHasKey('backup_id', $res);
    }

    public function test_restore_backup_requires_typed_confirmation(): void
    {
        $resFailed = BackupService::restoreBackup('GRIDFS-123', '', 'NO');
        $this->assertFalse($resFailed['success']);

        $resSuccess = BackupService::restoreBackup('GRIDFS-123', '', 'RESTORE');
        $this->assertTrue($resSuccess['success']);
    }
}
