<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Old\Application;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class MakeOperation extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:application:operation';
    }

    public static function getCommandDescription(): string
    {
        return 'Create an empty application gateway';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'The package of the gateway')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the gateway')
            ->addArgument('operation', InputArgument::REQUIRED, 'The type of the operation (Read | Write)')
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        parent::interact($input, $io, $command);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $operation = $input->getArgument('operation');

        $directory = sprintf(
            '%s\\Application\\Operation\\%s\\%s',
            $input->getArgument('package'),
            $input->getArgument('operation'),
            $input->getArgument('name'),
        );

        $template = sprintf('templates/skeleton/application/operation/%s', $operation);

        if ('Read' === $operation) {
            $this->generateClass('Handler', $directory, $template, $generator);
            $this->generateClass('Query', $directory, $template, $generator);
        }

        if ('Write' === $operation) {
            $this->generateClass('Event', $directory, $template, $generator);
            $this->generateClass('Handler', $directory, $template, $generator);
            $this->generateClass('Command', $directory, $template, $generator);
        }

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    private function generateClass(
        string $type,
        string $directory,
        string $template,
        Generator $generator,
        array $variables = [],
    ) {
        $classNameDetails = $generator->createClassNameDetails(
            $type,
            $directory,
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/%s.tpl.php', $template, $type),
            $variables,
        );

        $generator->writeChanges();
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }
}
