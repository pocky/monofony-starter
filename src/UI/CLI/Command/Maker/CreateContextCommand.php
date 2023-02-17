<?php

declare(strict_types=1);

namespace App\UI\CLI\Command\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:create:context',
)]
final class CreateContextCommand extends Command
{
    private array $commands = [
        [
            'name' => 'make:package',
            'message' => 'Create package',
        ],
        [
            'name' => 'make:domain:identifier',
            'message' => 'Create domain identifier',
        ],
        [
            'name' => 'make:infrastructure:persistence:entity',
            'message' => 'Create entity',
        ],
        [
            'name' => 'make:infrastructure:sylius:factory',
            'message' => 'Create factory for entity',
        ],
        [
            'name' => 'make:ui:sylius:grid',
            'message' => 'Create Sylius grid for entity',
        ],
        [
            'name' => 'make:ui:doctrine:form',
            'message' => 'Create form for entity',
        ],
        [
            'name' => 'doctrine:migrations:diff',
            'message' => 'Generate diff',
        ],
        [
            'name' => 'cache:clear',
            'message' => 'Clearing cache',
        ],
    ];

    protected function configure(): void
    {
        $this
            ->addArgument('package', InputArgument::REQUIRED, 'The package.')
            ->addArgument('name', InputArgument::REQUIRED, 'The entity.')
            ->addOption('api-resource', 'a', InputOption::VALUE_NONE, 'Mark this class as an API Platform resource (expose a CRUD API for it)')
            ->addOption('sylius-crud', 's', InputOption::VALUE_NONE, 'Use Sylius crud')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Creating context...');

        foreach ($this->commands as $step => $command) {
            $parameters = [];

            try {
                $io->newLine();
                $io->section(sprintf(
                    'Step %d of %d. <info>%s</info>',
                    $step + 1,
                    count($this->commands),
                    $command['message'],
                ));

                if ('make:package' === $command['name']) {
                    $parameters = [
                        $input->getArgument('package'),
                    ];
                }

                if ('make:domain:identifier' === $command['name']) {
                    $parameters = [
                        $input->getArgument('package'),
                        $input->getArgument('name'),
                    ];
                }

                if ('make:infrastructure:persistence:entity' === $command['name']) {
                    $parameters = [
                        $input->getArgument('package'),
                        $input->getArgument('name'),
                        $input->getOption('api-resource') ? '--api-resource' : '',
                        $input->getOption('sylius-crud') ? '--sylius-crud' : '',
                    ];
                }

                if ('make:infrastructure:sylius:factory' === $command['name']) {
                    $parameters = [
                        $input->getArgument('package'),
                        $input->getArgument('name'),
                    ];
                }

                if ('make:ui:sylius:grid' === $command['name']) {
                    $parameters = [
                        sprintf('Backend\\\%s', $input->getArgument('package')),
                    ];
                }

                if ('make:ui:doctrine:form' === $command['name']) {
                    $parameters = [
                        sprintf('Backend\\\%s', $input->getArgument('package')),
                        $input->getArgument('name'),
                    ];
                }

                $process = Process::fromShellCommandline(
                    sprintf(
                        'bin/console %s %s',
                        $command['name'],
                        implode(' ', $parameters),
                    ),
                );

                $process->setTimeout(3600);
                $process->setTty(true);
                $process->run();

                $output->writeln('');
            } catch (RuntimeException $exception) {
                throw new \LogicException($exception->getMessage());
            }
        }

        $io->newLine(2);
        $io->info('This is magic :\')');

        return Command::SUCCESS;
    }
}
