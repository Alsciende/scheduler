<?php

namespace Alsciende\Scheduler\Scheduler;

use Alsciende\Scheduler\TaskInterface;

/**
 * One activation of the Scheduler with the commands to execute at a given date
 */
class SchedulerActivation
{
    /**
     * @param \DateTimeInterface $dateTime
     * @param TaskInterface[] $tasks
     */
    public function __construct(public \DateTimeInterface $dateTime, public array $tasks)
    {
    }
}