<?php

namespace App\Helpers;

use App\Models\Holiday;
use App\Models\LeaveEntitlement;
use App\Models\LeaveRequest;
use DateTime;
use DateInterval;
use DatePeriod;

class LeaveCalculator
{
    /**
     * Calculate working days between start date and end date (inclusive),
     * excluding Saturdays, Sundays, and official national holidays.
     */
    public static function calculateWorkingDays(string $startDateStr, string $endDateStr): int
    {
        $start = new DateTime($startDateStr);
        $end = new DateTime($endDateStr);
        $end->modify('+1 day'); // Inclusive end date

        $holidays = Holiday::pluck('date')->map(function ($d) {
            return is_string($d) ? substr($d, 0, 10) : $d->format('Y-m-d');
        })->toArray();

        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $workingDays = 0;

        foreach ($period as $date) {
            $dayOfWeek = (int) $date->format('N'); // 1 = Monday, 6 = Saturday, 7 = Sunday
            $dateStr = $date->format('Y-m-d');

            if ($dayOfWeek < 6 && !in_array($dateStr, $holidays)) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    /**
     * Calculate effective end date given a start date and the number of working days approved.
     * Skips Saturdays, Sundays, and national holidays.
     */
    public static function calculateEndDate(string $startDateStr, int $workingDaysCount): string
    {
        $date = new DateTime($startDateStr);

        $holidays = Holiday::pluck('date')->map(function ($d) {
            return is_string($d) ? substr($d, 0, 10) : $d->format('Y-m-d');
        })->toArray();

        $daysCounted = 0;

        if ($workingDaysCount < 1) {
            $workingDaysCount = 1;
        }

        while (true) {
            $dayOfWeek = (int) $date->format('N');
            $dateStr = $date->format('Y-m-d');

            if ($dayOfWeek < 6 && !in_array($dateStr, $holidays)) {
                $daysCounted++;
            }

            if ($daysCounted === $workingDaysCount) {
                break;
            }

            $date->modify('+1 day');
        }

        return $date->format('Y-m-d');
    }

    /**
     * Get user entitlement stats for current year.
     * Returns: [
     *   'total' => 12,
     *   'used' => X (approved),
     *   'pending' => X (pending),
     *   'remaining' => total - used
     * ]
     */
    public static function getUserEntitlementSummary(int $userId, ?int $year = null): array
    {
        $isAutoCalculated = false;
        if ($year === null) {
            $year = (int) date('Y');
            $isAutoCalculated = true;
        }

        if ($isAutoCalculated) {
            $currentDate = date('Y-m-d');
            
            // Periksa apakah pengguna sedang menjalankan cuti yang dimulai tahun lalu dan belum selesai hari ini
            $activeCrossYearLeave = LeaveRequest::where('user_id', $userId)
                ->whereIn('status', ['pending', 'approved'])
                ->whereYear('start_date', $year - 1)
                ->whereDate(\Illuminate\Support\Facades\DB::raw('COALESCE(approved_end_date, end_date)'), '>=', $currentDate)
                ->exists();

            if ($activeCrossYearLeave) {
                $year = $year - 1;
            }
        }

        $entitlement = LeaveEntitlement::where('user_id', $userId)
            ->where('year', $year)
            ->first();

        $totalDays = $entitlement ? $entitlement->total_days : 12;

        $approvedDays = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereYear('start_date', $year)
            ->get()
            ->sum(function ($leave) {
                return $leave->approved_days ?? $leave->working_days_count;
            });

        $pendingDays = LeaveRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereYear('start_date', $year)
            ->sum('working_days_count');

        $remainingDays = max(0, $totalDays - $approvedDays);

        return [
            'total' => $totalDays,
            'used' => $approvedDays,
            'pending' => $pendingDays,
            'remaining' => $remainingDays,
            'available_after_pending' => max(0, $remainingDays - $pendingDays),
        ];
    }
}
