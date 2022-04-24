<?php

namespace Alsciende\Scheduler;

use Alsciende\Scheduler\Schedule\ScheduleInterface;

/**
 * Must be implemented by Console Commands to be executed on a schedule.
 */
interface TaskInterface
{
    public function schedule(): ScheduleInterface;
    public function getName(): ?string;
    public function getDescription(): string;
}