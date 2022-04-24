<?php

declare(strict_types=1);

namespace Alsciende\Scheduler;

class TaskExecution
{
    public function __construct(public TaskInterface $task, public \DateTimeInterface $dateTime)
    {
    }
}