<?php

namespace Alsciende\Scheduler\Schedule;

interface ScheduleInterface
{
    public function getCronDefinition(): string;

    /**
     * Return the next occurrence of this schedule immediately following $after
     */
    public function next(\DateTimeInterface $after): \DateTimeInterface;
}