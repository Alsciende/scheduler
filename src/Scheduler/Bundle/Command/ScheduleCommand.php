<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle\Command;

use Alsciende\Scheduler\Bundle\ScheduledTasksCollection;
use Alsciende\Scheduler\Scheduler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'scheduler:schedule',
    description: 'Check the validity of your scheduled commands',
)]
class ScheduleCommand extends Command
{
    public function __construct(private ScheduledTasksCollection $collector)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Maximum number of executions to list', 10)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $limit = intval($input->getOption('limit'));

        $formatter = $this->getHelper('formatter');

        $scheduler = new Scheduler($this->collector->getTasks(), new \DateTime());

        $count = 0;
        foreach ($scheduler as $datetime => $executions) {
            foreach ($executions as $execution) {
                $formattedLine = $formatter->formatSection(
                    $execution->dateTime->format(\DateTimeInterface::ISO8601),
                    $execution->task->getName()
                );
                $io->writeln($formattedLine);
            }

            if (++$count >= $limit - 1) {
                break;
            }
        }

        return Command::SUCCESS;
    }
}