<?php

namespace Tests\Scheduler\Schedule;

use Alsciende\Scheduler\Schedule\DailySchedule;
use Alsciende\Scheduler\Schedule\HourlySchedule;
use Alsciende\Scheduler\Schedule\ScheduleInterface;
use Alsciende\Scheduler\Scheduler;
use Alsciende\Scheduler\TaskExecution;
use Alsciende\Scheduler\TaskInterface;
use PHPUnit\Framework\TestCase;
use Tests\DateTimeFactory;

class SchedulerTest extends TestCase
{
    /**
     * @return void
     * @dataProvider dataProvider
     */
    public function testScheduler(array $taskData, string $fromDT, string $fromTZ, array $expectedArray): void
    {
        $tasks = array_map(
            fn($item) => new class($item[0], $item[1]) implements TaskInterface {
                public function __construct(private ScheduleInterface $_schedule, private string $name)
                {
                }

                public function schedule(): ScheduleInterface
                {
                    return $this->_schedule;
                }

                public function getName(): ?string
                {
                    return $this->name;
                }

                public function getDescription(): string
                {
                    return '';
                }
            },
            $taskData
        );

        $scheduler = new Scheduler($tasks, DateTimeFactory::iso8601($fromDT, $fromTZ));

        $expectedIndex = 0;

        foreach ($scheduler as $index => $executions) {
            // asserting that yielded values are correctly indexed
            $this->assertEquals($expectedIndex, $index);

            $expectedExecutions = $expectedArray[$expectedIndex];

            $this->assertIsArray($executions);
            /** @var array $executions */

            // asserting that Scheduler returns the expected number of tasks to execute next
            $this->assertEquals(count($expectedExecutions), count($executions));

            foreach ($executions as $executionIndex => $execution) {
                $this->assertInstanceOf(TaskExecution::class, $execution);
                /** @var TaskExecution $execution */

                $this->assertEquals(
                    $expectedExecutions[$executionIndex][0],
                    $execution->dateTime->format(\DateTimeInterface::ISO8601)
                );

                $this->assertEquals(
                    $expectedExecutions[$executionIndex][1],
                    $execution->task->getName()
                );
            }

            if (++$expectedIndex >= count($expectedArray)) {
                // Scheduler will yield many more executions, but we stop here
                break;
            }
        }
    }

    public function dataProvider(): array
    {
        return [
            [
                [
                    [ new DailySchedule(0, 0), 'name' ],
                ],
                '2022-03-27T00:00:00+0100',
                'Europe/Paris',
                [
                    [
                        [ '2022-03-28T00:00:00+0200', 'name' ],
                    ],
                ]
            ],
            [
                [
                    [ new HourlySchedule(0), '00' ],
                    [ new HourlySchedule(0), '00bis' ],
                    [ new HourlySchedule(30), '30' ],
                    [ new DailySchedule(2, 00), 'daily' ],
                ],
                '2022-03-27T00:00:00+0100',
                'Europe/Paris',
                [
                    [
                        [ '2022-03-27T00:30:00+0100', '30' ],
                    ],
                    [
                        [ '2022-03-27T01:00:00+0100', '00' ],
                        [ '2022-03-27T01:00:00+0100', '00bis' ],
                    ],
                    [
                        [ '2022-03-27T01:30:00+0100', '30' ],
                    ],
                    [
                        [ '2022-03-27T03:00:00+0200', 'daily' ], // Daylight saving time
                        [ '2022-03-27T03:00:00+0200', '00' ], // Daylight saving time
                        [ '2022-03-27T03:00:00+0200', '00bis' ], // Daylight saving time
                    ],
                    [
                        [ '2022-03-27T03:30:00+0200', '30' ],
                    ],
                ]
            ]
        ];
    }
}
