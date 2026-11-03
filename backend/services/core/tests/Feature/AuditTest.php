<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Exception;
use Tests\TestCase;

class AuditTest extends TestCase
{
    public function test_audit_log_creation(): void
    {
        $log = AuditLog::create([
            'user_id' => '1',
            'service' => 'core-service',
            'action' => 'user.login',
            'entity_type' => 'user',
            'entity_id' => '1',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'notes' => 'Test log',
        ]);

        $this->assertDatabaseHas('audit_logs', ['id' => $log->id, 'action' => 'user.login']);
    }

    public function test_audit_log_is_immutable_and_cannot_be_updated(): void
    {
        $log = AuditLog::create([
            'user_id' => '1',
            'service' => 'core-service',
            'action' => 'critical.action',
            'ip_address' => '127.0.0.1',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Audit logs are strictly immutable and cannot be updated.');

        $log->update(['notes' => 'Tampered note']);
    }

    public function test_audit_log_is_immutable_and_cannot_be_deleted(): void
    {
        $log = AuditLog::create([
            'user_id' => '1',
            'service' => 'core-service',
            'action' => 'critical.action',
            'ip_address' => '127.0.0.1',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Audit logs are strictly immutable and cannot be deleted.');

        $log->delete();
    }

    public function test_sensitive_access_query_scope(): void
    {
        AuditLog::create([
            'user_id' => '2',
            'service' => 'hrd-service',
            'action' => 'audit.sensitive_data_view',
            'entity_type' => 'salary',
            'is_sensitive_access' => true,
        ]);

        AuditLog::create([
            'user_id' => '2',
            'service' => 'crm-service',
            'action' => 'lead.view',
            'entity_type' => 'lead',
            'is_sensitive_access' => false,
        ]);

        $sensitiveLogs = AuditLog::sensitive()->get();
        $this->assertCount(1, $sensitiveLogs);
        $this->assertEquals('audit.sensitive_data_view', $sensitiveLogs->first()->action);
    }
}

