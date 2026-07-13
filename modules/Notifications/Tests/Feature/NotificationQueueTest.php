<?php

namespace Modules\Notifications\Tests\Feature;

use App\Jobs\SendNotificationJob;
use App\Services\Tenant\TenantContextManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Modules\HR\Events\AttendanceLogged;
use Modules\HR\Models\AttendanceLog;
use Modules\HR\Models\Department;
use Modules\HR\Models\Designation;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Shift;
use Modules\Notifications\Models\Notification;
use Tests\TestCase;

class NotificationQueueTest extends TestCase
{
    use RefreshDatabase;

    protected TenantContextManager $tenantManager;
    protected string $tenantA;
    protected string $tenantB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenantManager = app(TenantContextManager::class);
        $this->tenantA = Str::uuid()->toString();
        $this->tenantB = Str::uuid()->toString();
    }

    public function test_attendance_check_in_fires_event_and_queues_notification_job(): void
    {
        Queue::fake();

        $this->tenantManager->setTenantId($this->tenantA);

        $dept = Department::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'IT', 'code' => 'IT-1']);
        $desig = Designation::create(['id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'title' => 'Dev', 'grade' => 1]);
        $employee = Employee::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'first_name' => 'John', 'last_name' => 'Doe',
            'email' => 'a@t.com', 'department_id' => $dept->id, 'designation_id' => $desig->id, 'joining_date' => '2020-01-01'
        ]);
        $shift = Shift::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'name' => 'Morning',
            'start_time' => '09:00:00', 'end_time' => '17:00:00', 'grace_period_minutes' => 15
        ]);

        $log = AttendanceLog::create([
            'id' => Str::uuid()->toString(),
            'tenant_id' => $this->tenantA,
            'employee_id' => $employee->id,
            'shift_id' => $shift->id,
            'date' => '2024-01-01',
            'check_in' => '2024-01-01 09:00:00',
            'late_minutes' => 0,
            'status' => 'Present',
        ]);

        // Manually fire the event as if AttendanceLogger did it
        event(new AttendanceLogged($log));

        // Assert notification was created
        $this->assertDatabaseHas('notifications', [
            'tenant_id' => $this->tenantA,
            'recipient' => 'a@t.com',
            'status' => 'queued',
        ]);

        // Assert job was pushed to 'notifications' queue
        Queue::assertPushed(SendNotificationJob::class, function ($job) use ($log) {
            return $job->notification->tenant_id === $this->tenantA && $job->queue === 'notifications';
        });
    }

    public function test_tenant_isolation_on_notifications(): void
    {
        if (config('database.default') === 'sqlite') {
            $this->markTestSkipped('SQLite does not support RLS.');
        }

        Notification::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'email', 'recipient' => 'a@a.com', 'body' => 'Test'
        ]);

        Notification::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantB, 'type' => 'email', 'recipient' => 'b@b.com', 'body' => 'Test'
        ]);

        $this->tenantManager->setTenantId($this->tenantA);
        $notificationsA = Notification::all();
        $this->assertCount(1, $notificationsA);
        $this->assertEquals('a@a.com', $notificationsA->first()->recipient);

        $this->tenantManager->clearTenantId();

        $this->tenantManager->setTenantId($this->tenantB);
        $notificationsB = Notification::all();
        $this->assertCount(1, $notificationsB);
        $this->assertEquals('b@b.com', $notificationsB->first()->recipient);
    }

    public function test_send_notification_job_updates_status(): void
    {
        $this->tenantManager->setTenantId($this->tenantA);

        $notification = Notification::create([
            'id' => Str::uuid(), 'tenant_id' => $this->tenantA, 'type' => 'email', 'recipient' => 'a@a.com', 'body' => 'Test', 'status' => 'queued'
        ]);

        $job = new SendNotificationJob($notification);
        $job->handle($this->tenantManager);

        $notification->refresh();
        $this->assertEquals('sent', $notification->status);
        $this->assertNotNull($notification->sent_at);
    }
}
