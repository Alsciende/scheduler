<?php

namespace Scheduler\Schedule;

use Alsciende\Scheduler\Schedule\MonthlySchedule;
use PHPUnit\Framework\TestCase;
use Tests\DateTimeFactory;

class MonthlyScheduleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testNext(int $dayOfMonth, int $hour, int $minute, string $afterDate, string $afterTz, string $expected): void
    {
        $underTest = new MonthlySchedule($dayOfMonth, $hour, $minute);

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
            [1, 0, 0, '2022-03-27T00:00:00+0100', 'Europe/Paris', '2022-04-01T00:00:00+0200'], // Daylight saving time
            [1, 0, 0, '2021-12-01T00:00:00+0100', 'Europe/Paris', '2022-01-01T00:00:00+0100'],
            [30, 0, 0, '2022-01-31T12:00:00+0100', 'Europe/Paris', '2022-03-30T00:00:00+0200'], // Daylight saving time and skipping February
        ];
    }
}
