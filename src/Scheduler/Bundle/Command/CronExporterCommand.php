<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle\Command;

use Alsciende\Scheduler\Bundle\Exporter\CronExporter;
use Alsciende\Scheduler\Bundle\ScheduledTasksCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'scheduler:export:cron',
    description: 'Export cron table for your scheduled tasks',
)]
class CronExporterCommand extends Command
{
    public function __construct(private ScheduledTasksCollection $collection, private CronExporter $exporter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->collection->getTasks() as $task) {
            $io->writeln($this->exporter->export($task));
        }

        return Command::SUCCESS;
    }
}