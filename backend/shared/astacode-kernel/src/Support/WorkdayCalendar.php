<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Class WorkdayCalendar
 * Calculates working days, business SLA deadlines, and accounts for Indonesian national holidays.
 * Used for SLA tracking in PM, Finance invoice due dates, and CRM quotation expiry.
 */
final class WorkdayCalendar
{
    /**
     * Standard Indonesian National Holidays & Cuti Bersama.
     * Format: 'YYYY-MM-DD' => 'Description'
     */
    private static array $holidays = [
        // Recurring fixed annual holidays
        '2026-01-01' => 'Tahun Baru Masehi',
        '2026-05-01' => 'Hari Buruh Internasional',
        '2026-06-01' => 'Hari Lahir Pancasila',
        '2026-08-17' => 'Hari Proklamasi Kemerdekaan RI',
        '2026-12-25' => 'Hari Raya Natal',
        // Floating/Lunisolar 2026 references
        '2026-02-17' => 'Tahun Baru Imlek 2577',
        '2026-03-20' => 'Hari Raya Idul Fitri 1447 H',
        '2026-03-21' => 'Hari Raya Idul Fitri Hari Kedua',
        '2026-03-22' => 'Hari Suci Nyepi Tahun Baru Saka 1948',
        '2026-05-14' => 'Kenaikan Yesus Kristus',
        '2026-05-31' => 'Hari Raya Waisak 2570',
        '2026-05-27' => 'Hari Raya Idul Adha 1447 H',
    ];

    /**
     * Check if a given date is a working day (Mon-Fri and not a registered public holiday).
     */
    public static function isWorkingDay(DateTimeInterface|string $date): bool
    {
        $dt = self::normalizeDate($date);
        $dayOfWeek = (int) $dt->format('N'); // 1 (Mon) - 7 (Sun)

        // Weekend check (Saturday = 6, Sunday = 7)
        if ($dayOfWeek >= 6) {
            return false;
        }

        // Holiday check
        $ymd = $dt->format('Y-m-d');
        if (isset(self::$holidays[$ymd])) {
            return false;
        }

        return true;
    }

    /**
     * Add N working days to a start date, skipping weekends and registered holidays.
     * Example: addWorkingDays("2026-11-06" (Friday), 1) -> "2026-11-09" (Monday).
     */
    public static function addWorkingDays(DateTimeInterface|string $startDate, int $days): DateTimeImmutable
    {
        $current = self::normalizeDate($startDate);

        if ($days === 0) {
            return $current;
        }

        $step = $days > 0 ? 1 : -1;
        $remaining = abs($days);

        while ($remaining > 0) {
            $current = $current->modify("{$step} day");
            if (self::isWorkingDay($current)) {
                $remaining--;
            }
        }

        return $current;
    }

    /**
     * Count the number of working days between two dates (inclusive of start and end).
     */
    public static function getWorkingDaysBetween(DateTimeInterface|string $startDate, DateTimeInterface|string $endDate): int
    {
        $start = self::normalizeDate($startDate);
        $end = self::normalizeDate($endDate);

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        $count = 0;
        $cursor = $start;

        while ($cursor <= $end) {
            if (self::isWorkingDay($cursor)) {
                $count++;
            }
            $cursor = $cursor->modify('+1 day');
        }

        return $count;
    }

    /**
     * Register a custom holiday dynamically.
     */
    public static function addHoliday(string $dateYmd, string $description): void
    {
        self::$holidays[$dateYmd] = $description;
    }

    /**
     * Retrieve all registered holidays.
     */
    public static function getHolidays(): array
    {
        return self::$holidays;
    }

    private static function normalizeDate(DateTimeInterface|string $date): DateTimeImmutable
    {
        if ($date instanceof DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($date);
        }

        return new DateTimeImmutable($date);
    }
}

