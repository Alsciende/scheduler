<?php

namespace Scheduler\Schedule;

use Alsciende\Scheduler\Schedule\DayOfWeek;
use Alsciende\Scheduler\Schedule\WeeklySchedule;
use PHPUnit\Framework\TestCase;
use Tests\DateTimeFactory;

class WeeklyScheduleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testNext(DayOfWeek $dayOfWeek, int $hour, int $minute, string $afterDate, string $afterTz, string $expected): void
    {
        $underTest = new WeeklySchedule($dayOfWeek, $hour, $minute);

        $after = DateTimeFactory::iso8601($afterDate, $afterTz);

        $next = $underTest->next($after);

        $this->assertEquals(
            $expected,
            $next->format(\DateTimeInterface::ISO8601)
        );
    }

    public function dataProvider(): array
    {
        return [
            [DayOfWeek::SUNDAY, 0, 0, '2022-03-27T00:00:00+0100', 'Europe/Paris', '2022-04-03T00:00:00+0200'], // Daylight saving time
            [DayOfWeek::SUNDAY, 2, 30, '2022-03-27T01:30:00+0100', 'Europe/Paris', '2022-03-27T03:30:00+0200'], // Daylight saving time
            [DayOfWeek::THURSDAY, 12, 00, '2022-03-23T00:00:00+0100', 'Europe/Paris', '2022-03-24T12:00:00+0100'],
            [DayOfWeek::WEDNESDAY, 12, 00, '2022-03-24T00:00:00+0100', 'Europe/Paris', '2022-03-30T12:00:00+0200'], // Daylight saving time
        ];
    }
}
