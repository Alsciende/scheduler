<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle\Command;

use Alsciende\Scheduler\Bundle\ScheduledTasksCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'scheduler:list',
    description: 'List your scheduled tasks',
)]
class ListCommand extends Command
{
    public function __construct(private ScheduledTasksCollection $collection)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("List of scheduled tasks");

        $io->listing(
            array_map(
                fn ($scheduler) => get_class($scheduler),
                $this->collection->getTasks()
            )
        );

        return Command::SUCCESS;
    }
}