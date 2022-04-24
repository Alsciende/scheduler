<?php

declare(strict_types=1);

namespace Alsciende\Scheduler;

use Traversable;

/**
 * Compute the schedule of tasks from a starting date
 */
class Scheduler implements \IteratorAggregate
{
    private const MAX_LOOPS = 10_000;

    /**
     * Keys are timestamps, Values are array of TaskExecutions occurring at timestamp
     * Because array_shift reindexes numerical keys but array_pop does not, this array is sorted in reverse order
     * @var array<int, SchedulerActivation>
     */
    private array $scheduleAhead = [];

    /**
     * @param TaskInterface[] $tasks
     */
    public function __construct(array $tasks, \DateTimeInterface $start)
    {
        foreach ($tasks as $task) {
            $this->storeNextExecution($task, $start);
        }
    }

    private function storeNextExecution(TaskInterface $task, \DateTimeInterface $after): void
    {
        $nextExecutionDate = $task->schedule()->next($after);
        $this->updateSchedule($task, $nextExecutionDate);
    }

    private function updateSchedule(TaskInterface $task, \DateTimeInterface $at): void
    {
        $activation = ($this->scheduleAhead[$at->getTimestamp()] ??= new SchedulerActivation($at, []));
        $activation->tasks[] = $task;
        krsort($this->scheduleAhead);
    }

    /**
     * @return \Generator<int, SchedulerActivation>
     */
    public function getIterator(): Traversable
    {
        $count = 0;

        while ($count < self::MAX_LOOPS) {
            $activation = array_pop($this->scheduleAhead);

            foreach ($activation->tasks as $task) {
                $this->storeNextExecution($task, $activation->dateTime);
            }

            yield $count++ => $activation;
        }

        throw new \OverflowException('Cannot schedule more than ' . self::MAX_LOOPS . ' task executions.');
    }
}