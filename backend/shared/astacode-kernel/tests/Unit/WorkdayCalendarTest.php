<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Support\WorkdayCalendar;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class WorkdayCalendarTest extends TestCase
{
    public function test_distinguishes_working_days_and_weekends(): void
    {
        // 2026-11-04 is Wednesday (Working day)
        $this->assertTrue(WorkdayCalendar::isWorkingDay('2026-11-04'));

        // 2026-11-06 is Friday (Working day)
        $this->assertTrue(WorkdayCalendar::isWorkingDay('2026-11-06'));

        // 2026-11-07 is Saturday (Weekend)
        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-11-07'));

        // 2026-11-08 is Sunday (Weekend)
        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-11-08'));
    }

    public function test_recognizes_indonesian_public_holidays(): void
    {
        // 2026-08-17 is Hari Proklamasi Kemerdekaan RI (Monday)
        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-08-17'));

        // 2026-01-01 is Tahun Baru (Thursday)
        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-01-01'));

        // 2026-05-01 is Hari Buruh (Friday)
        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-05-01'));
    }

    public function test_add_working_days_skips_weekends_and_holidays(): void
    {
        // From Friday 2026-11-06, add 1 working day -> Monday 2026-11-09
        $result = WorkdayCalendar::addWorkingDays('2026-11-06', 1);
        $this->assertSame('2026-11-09', $result->format('Y-m-d'));

        // From Friday 2026-11-06, add 5 working days -> Friday 2026-11-13
        $result5 = WorkdayCalendar::addWorkingDays('2026-11-06', 5);
        $this->assertSame('2026-11-13', $result5->format('Y-m-d'));

        // 14 working days SLA test
        $result14 = WorkdayCalendar::addWorkingDays('2026-11-02', 14);
        // Nov 2 (Mon): 14 working days = 2 full weeks (10 days) + 4 days = Friday Nov 20
        $this->assertSame('2026-11-20', $result14->format('Y-m-d'));
    }

    public function test_counts_working_days_between_two_dates(): void
    {
        // Monday Nov 2 to Friday Nov 6 = 5 working days
        $days = WorkdayCalendar::getWorkingDaysBetween('2026-11-02', '2026-11-06');
        $this->assertSame(5, $days);

        // Monday Nov 2 to Monday Nov 9 = 6 working days (skips Sat & Sun)
        $daysAcrossWeekend = WorkdayCalendar::getWorkingDaysBetween('2026-11-02', '2026-11-09');
        $this->assertSame(6, $daysAcrossWeekend);
    }

    public function test_custom_holiday_registration(): void
    {
        // Mark 2026-11-10 as custom holiday
        WorkdayCalendar::addHoliday('2026-11-10', 'Hari Libur Khusus Perusahaan ASTACODE');

        $this->assertFalse(WorkdayCalendar::isWorkingDay('2026-11-10'));
    }
}

