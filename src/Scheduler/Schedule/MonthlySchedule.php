<?php

namespace Alsciende\Scheduler\Schedule;

/**
 * Executes on a given day-of-the-month every month.
 * @note If that day does not exist in this month, this month is skipped.
 */
class MonthlySchedule implements ScheduleInterface
{
    public function __construct(
        public int $dayOfMonth,
        public int $hour,
        public int $minute
    ) {
    }

    public function getCronDefinition(): string
    {
        return sprintf(
            "%d %d %d * *",
            $this->minute,
            $this->hour,
            $this->dayOfMonth
        );
    }

    public function next(\DateTimeInterface $after): \DateTimeInterface
    {
        $next = \DateTime::createFromInterface($after);

        $isNextMonth = $this->isNextMonth($next);

        // setting to first day in correct month
        $next->setDate(
            (int) $next->format('Y'),
            (int) $next->format('n') + ($isNextMonth ? 1 : 0),
            1
        );

        // condition will fail if month does not have enough days
        while ($this->dayOfMonth !== (int) $next->format('j')) {
            // setting to correct day in month
            $next->setDate(
                (int) $next->format('Y'),
                (int) $next->format('n'),
                $this->dayOfMonth
            );
        }

        $next->setTime($this->hour, $this->minute, 0, 0);

        return $next;
    }

    private function isNextMonth(\DateTimeInterface $next)
    {
        $todayOfTheMonth = (int) $next->format('j');

        if ($todayOfTheMonth === $this->dayOfMonth) {
            $sameDayExecution = \DateTime::createFromInterface($next);
            $sameDayExecution->setTime($this->hour, $this->minute, 0, 0);

            return ($next >= $sameDayExecution);
        } else {
            return $todayOfTheMonth > $this->dayOfMonth;
        }
    }
}