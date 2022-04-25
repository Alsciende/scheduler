# Scheduler

This package provides:
- a way for Symfony Console commands to declare their cron schedule.
- a Console command to export these schedules in a format understandable by cron.

## Installation

```
composer require alsciende/scheduler
```

## Usage

### Setup

Implement the interface `\Alsciende\Scheduler\TaskInterface` in your Console commands. 
This interface requires your class to return a `\Alsciende\Scheduler\Schedule\ScheduleInterface`.
The package provides 2 classes implementing that interface that you can use:
- `\Alsciende\Scheduler\Schedule\HourlySchedule` for tasks that run every hour at a given minute
- `\Alsciende\Scheduler\Schedule\DailySchedule` for tasks that run every day at a given hour and minute
- `\Alsciende\Scheduler\Schedule\WeeklySchedule` for tasks that run every week at a given day, hour and minute
- `\Alsciende\Scheduler\Schedule\MonthlySchedule` for tasks that run every month at a given day, hour and minute

### List

To list all the scheduled commands, execute  `php bin/console scheduler:list`

### Test

To list all the future executions of the scheduled commands, execute `php bin/console scheduler:schedule`

### Export

To export your schedule as a crontab, execute `php bin/console scheduler:export:cron`

## Example

This command
```
<?php

namespace App\Command;

use Alsciende\Scheduler\Schedule\DailySchedule;
use Alsciende\Scheduler\Schedule\ScheduleInterface;
use Alsciende\Scheduler\TaskInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:daily',
    description: 'A job that needs to run every day',
)]
class DailyCommand extends Command implements TaskInterface
{
    public function schedule(): ScheduleInterface
    {
        return new DailySchedule(7, 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // do something...
        
        return Command::SUCCESS;
    }
}

```
will generate this crontab with `scheduler:export:cron`
```
# A job that needs to run every day
10 7 * * * php bin/console app:daily
```