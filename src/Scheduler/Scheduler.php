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
     * @var array<int, TaskExecution[]>
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
        $this->updateSchedule(
            $nextExecutionDate->getTimestamp(),
            new TaskExecution($task, $nextExecutionDate)
        );
    }

    private function updateSchedule(int $timestamp, TaskExecution $taskExecution): void
    {
        $this->scheduleAhead[$timestamp] ??= [];
        $this->scheduleAhead[$timestamp][] = $taskExecution;
        krsort($this->scheduleAhead);
    }

    /**
     * @return \Generator<int, TaskExecution[]>
     */
    public function getIterator(): Traversable
    {
        $count = 0;

        while ($count < self::MAX_LOOPS) {
            $taskExecutions = array_pop($this->scheduleAhead);

            foreach ($taskExecutions as $taskExecution) {
                $this->storeNextExecution($taskExecution->task, $taskExecution->dateTime);
            }

            yield $count++ => $taskExecutions;
        }

        throw new \OverflowException('Cannot schedule more than ' . self::MAX_LOOPS . ' task executions.');
    }
}