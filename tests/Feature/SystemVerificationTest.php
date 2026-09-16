<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Helpers\LeaveCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_leave_working_days_calculation_and_entitlement()
    {
        $this->seed();

        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();
        $leaveType = LeaveType::where('code', 'CT_TAHUNAN')->first();

        // Test working days calculation (10 - 14 Aug 2026: 5 working days)
        $days = LeaveCalculator::calculateWorkingDays('2026-08-10', '2026-08-14');
        $this->assertEquals(5, $days);

        // Check entitlement before request
        $summaryBefore = LeaveCalculator::getUserEntitlementSummary($user->id);
        $this->assertEquals(12, $summaryBefore['total']);
        $this->assertEquals(0, $summaryBefore['used']);
        $this->assertEquals(12, $summaryBefore['remaining']);

        // Create leave request
        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $leaveType->id,
            'request_number' => 'CUTI/20260810/TEST1',
            'start_date' => '2026-08-10',
            'end_date' => '2026-08-14',
            'working_days_count' => $days,
            'reason' => 'Tes Cuti Tahunan',
            'emergency_contact' => '08123456789',
            'status' => 'pending',
        ]);

        $summaryPending = LeaveCalculator::getUserEntitlementSummary($user->id);
        $this->assertEquals(5, $summaryPending['pending']);
        $this->assertEquals(7, $summaryPending['available_after_pending']);

        // Admin approve
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $summaryApproved = LeaveCalculator::getUserEntitlementSummary($user->id);
        $this->assertEquals(5, $summaryApproved['used']);
        $this->assertEquals(7, $summaryApproved['remaining']);
        $this->assertEquals(7, $summaryApproved['available_after_pending']);
    }
}
