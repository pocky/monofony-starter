<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Domain\Identifier;

use App\Shared\Infrastructure\Generator\GeneratorInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class Maker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:domain:identifier';
    }

    public static function getCommandDescription(): string
    {
        return 'Create an identifier for a domain entity with his generator';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'Where is located our package?')
            ->addArgument('name', InputArgument::REQUIRED, 'What\'s the name of the identifier?')
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        parent::interact($input, $io, $command);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $configuration = new Configuration(
            $input->getArgument('name'),
            $input->getArgument('package'),
        );

        $this->generateIdentifier($configuration, $generator, $io);
        $this->generateGenerator($configuration, $generator, $io);

        $generator->writeChanges();
        $this->writeSuccessMessage($io);
    }

    private function generateIdentifier(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $classNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getIdentifierPath(),
            'Id',
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getTemplatePath()),
        );

        $generator->writeChanges();

        $io->writeln('');
        $io->writeln('Domain identifier is generated');
    }

    private function generateGenerator(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $classNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getGeneratorPath(),
            'IdGenerator',
        );

        $identifierClassDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getIdentifierPath(),
            'Id',
        );

        $useStatements = new UseStatementGenerator([
            $identifierClassDetails->getFullName(),
            GeneratorInterface::class,
        ]);

        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getTemplateGeneratorPath()),
            [
                'use_statements' => $useStatements,
                'identifier_name' => $identifierClassDetails->getShortName(),
            ],
        );

        $generator->writeChanges();

        $io->writeln('');
        $io->writeln('Generator for identifier is generated');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }
}
