<?php

namespace Alsciende\Scheduler\Schedule;

class WeeklySchedule implements ScheduleInterface
{
    public function __construct(
        public DayOfWeek $dayOfWeek,
        public int $hour,
        public int $minute
    ) {
    }

    public function getCronDefinition(): string
    {
        return sprintf(
            "%d %d * * %d",
            $this->minute,
            $this->hour,
            $this->getCronDayOfTheWeek()
        );
    }

    public function next(\DateTimeInterface $after): \DateTimeInterface
    {
        $next = \DateTime::createFromInterface($after);

        $todayOfTheWeek = (int) $next->format('w');
        $scheduleDayOfTheWeek = $this->getCronDayOfTheWeek();

        if ($todayOfTheWeek === $scheduleDayOfTheWeek) {
            $sameDayExecution = \DateTime::createFromInterface($after);
            $sameDayExecution->setTime($this->hour, $this->minute, 0, 0);

            // our starting point is already past (or at) today's occurrence, so the next occurrence is in 7 days
            if ($next >= $sameDayExecution) {
                $next->add(new \DateInterval("P7D"));
            }
        } else {
            $daysToAdd = (7 + $scheduleDayOfTheWeek - $todayOfTheWeek) % 7; // add 7 to circumvent negative modulo
            $next->add(new \DateInterval("P" . $daysToAdd . "D"));
        }

        $next->setTime($this->hour, $this->minute, 0, 0);

        return $next;
    }

    private function getCronDayOfTheWeek(): int
    {
        return match($this->dayOfWeek)
        {
            DayOfWeek::SUNDAY => 0,
            DayOfWeek::MONDAY => 1,
            DayOfWeek::TUESDAY => 2,
            DayOfWeek::WEDNESDAY => 3,
            DayOfWeek::THURSDAY => 4,
            DayOfWeek::FRIDAY => 5,
            DayOfWeek::SATURDAY => 6
        };
    }
}