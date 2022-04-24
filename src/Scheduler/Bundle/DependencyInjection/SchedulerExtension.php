<?php

declare(strict_types=1);

namespace Alsciende\Scheduler\Bundle\DependencyInjection;

use Alsciende\Scheduler\TaskInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SchedulerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(TaskInterface::class)
            ->addTag('scheduler.command')
        ;
    }
}