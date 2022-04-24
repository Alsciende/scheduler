<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle\Exporter;

use Alsciende\Scheduler\TaskInterface;

class CronExporter
{
    public function export(TaskInterface $task): string
    {
        $cron = $task->schedule()->getCronDefinition();
        $name = $task->getName();
        $description = $task->getDescription();

         return <<<EOF
# $description
$cron php bin/console $name

EOF;

    }
}