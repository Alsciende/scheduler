<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle;

use Alsciende\Scheduler\TaskInterface;
use Symfony\Component\Console\Command\Command;

class ScheduledTasksCollection
{
    /** @var TaskInterface[] */
    private array $tasks;

    public function __construct(iterable $services)
    {
        foreach ($services as $service) {
            if ($service instanceof TaskInterface) {
                if (false === $service instanceof Command) {
                    throw new \LogicException(get_class($service) . ' is not a Console Command. '. TaskInterface::class .' must only be implemented by Console Commands.');
                }

                $this->tasks[] = $service;
            }
        }
    }

    /**
     * @return TaskInterface[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}