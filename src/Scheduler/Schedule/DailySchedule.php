<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Schedule;

/**
 * Schedule occurring at a fixed time every day
 */
class DailySchedule implements ScheduleInterface
{
    public function __construct(
        public int $hour,
        public int $minute
    ) {
    }

    public function getCronDefinition(): string
    {
        return sprintf(
            "%d %d * * *",
            $this->minute,
            $this->hour
        );
    }

    public function next(\DateTimeInterface $after): \DateTimeInterface
    {
        $next = \DateTime::createFromInterface($after);

        // since the schedule is daily, let's compute when it occurs today
        $sameDayExecution = \DateTime::createFromInterface($after);
        $sameDayExecution->setTime($this->hour, $this->minute, 0, 0);

        // our starting point is already past (or at) today's occurrence, so the next occurrence is tomorrow
        if ($next >= $sameDayExecution) {
            $next->add(new \DateInterval("P1D"));
        }

        $next->setTime($this->hour, $this->minute, 0, 0);

        return $next;
    }
}