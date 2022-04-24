<?php

namespace Alsciende\Scheduler\Schedule;

class HourlySchedule implements ScheduleInterface
{
    public function __construct(
        public int $minute
    ) {
    }

    public function getCronDefinition(): string
    {
        return sprintf(
            "%d * * * *",
            $this->minute
        );
    }

    public function next(\DateTimeInterface $after): \DateTimeInterface
    {
        $next = \DateTime::createFromInterface($after);

        // since the schedule is hourly, let's compute when it occurs in this hour
        $sameDayExecution = \DateTime::createFromInterface($after);
        $sameDayExecution->setTime(intval($after->format('H')), $this->minute, 0, 0);

        // our starting point is already past (or at) today's occurrence, so the next occurrence is tomorrow
        if ($next >= $sameDayExecution) {
            $next->add(new \DateInterval("PT1H"));
        }

        $next->setTime(intval($next->format('H')), $this->minute, 0, 0);

        return $next;
    }
}