<?php

namespace Tests\Scheduler\Schedule;

use PHPUnit\Framework\TestCase;
use Alsciende\Scheduler\Schedule\HourlySchedule;
use Tests\DateTimeFactory;

class HourlyScheduleTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testNext(int $minute, string $afterDate, string $afterTz, string $expected): void
    {
        $underTest = new HourlySchedule($minute);

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
            [0, '2022-03-26T23:59:59+0100', 'Europe/Paris', '2022-03-27T00:00:00+0100'], // in one second next day
            [0, '2022-03-27T00:00:00+0100', 'Europe/Paris', '2022-03-27T01:00:00+0100'], // in one full hour
            [0, '2022-03-27T01:30:00+0100', 'Europe/Paris', '2022-03-27T03:00:00+0200'] // Daylight saving time
        ];
    }
}
