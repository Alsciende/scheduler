<?php

namespace Tests\Scheduler\Schedule;

use PHPUnit\Framework\TestCase;
use Alsciende\Scheduler\Schedule\DailySchedule;
use Tests\DateTimeFactory;

class DailyScheduleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testNext(int $hour, int $minute, string $afterDate, string $afterTz, string $expected): void
    {
        $underTest = new DailySchedule($hour, $minute);

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
            [0, 0, '2022-03-27T00:00:00+0100', 'Europe/Paris', '2022-03-28T00:00:00+0200'], // Daylight saving time
            [2, 30, '2022-03-27T01:30:00+0100', 'Europe/Paris', '2022-03-27T03:30:00+0200'] // Daylight saving time
        ];
    }
}
