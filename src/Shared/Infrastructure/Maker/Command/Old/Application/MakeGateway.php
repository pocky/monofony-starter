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

final class MakeGateway extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:application:gateway';
    }

    public static function getCommandDescription(): string
    {
        return 'Create an empty application gateway';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'The package of the context')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the gateway')
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        parent::interact($input, $io, $command);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $directory = sprintf(
            '%s\\Application\\Gateway\\%s',
            $input->getArgument('package'),
            $input->getArgument('name'),
        );

        $template = 'templates/skeleton/application/gateway';
        $signal = sprintf(
            '%s_%s',
            str_replace('\\', '_', (string) $input->getArgument('package')),
            $input->getArgument('name'),
        );

        $this->generateClass('Request', $directory, $template, $generator);
        $this->generateClass('Response', $directory, $template, $generator);
        $this->generateClass('Instrumentation', $directory, $template, $generator, [
            'instrumentation_signal' => strtolower($signal),
        ]);
        $this->generateClass('Gateway', $directory, $template, $generator);

        $template = 'templates/skeleton/application/gateway/Middleware';
        $this->generateClass('ErrorHandler', sprintf('%s\\Middleware', $directory), $template, $generator, [
            'parent' => sprintf('App\\%s', $directory),
        ]);
        $this->generateClass('Logger', sprintf('%s\\Middleware', $directory), $template, $generator, [
            'parent' => sprintf('App\\%s', $directory),
        ]);
        $this->generateClass('Processor', sprintf('%s\\Middleware', $directory), $template, $generator, [
            'parent' => sprintf('App\\%s', $directory),
        ]);

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
