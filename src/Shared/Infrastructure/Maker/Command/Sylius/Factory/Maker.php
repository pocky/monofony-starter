<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Sylius\Factory;

use App\Shared\Infrastructure\Maker\Builder\PackageBuilder;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class Maker extends AbstractMaker
{
    public function __construct(
        private readonly FileManager $fileManager,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:infrastructure:sylius:factory';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a sylius factory for a doctrine entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'Where is located our package?')
            ->addArgument('name', InputArgument::REQUIRED, 'What\'s the name of the entity?')
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

        $this->generateFactory($configuration, $generator, $io);

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }

    private function generateFactory(
        Configuration $configuration,
        Generator     $generator,
        ConsoleStyle  $io,
    ): void {
        $entityNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityPath(),
        );

        $generatorClassDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getIdentityPath(),
            'IdGenerator',
        );

        $useStatements = new UseStatementGenerator([
            $generatorClassDetails->getFullName(),
            FactoryInterface::class,
        ]);

        $classNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getFactoryPath(),
            'Factory',
        );

        $this->generateClass(
            $configuration,
            $classNameDetails,
            $generator,
            [
                'use_statements' => $useStatements,
                'generator_name' => $generatorClassDetails->getShortName(),
            ],
        );

        $generator->writeChanges();

        $this->createFactoryService(
            $configuration,
            $classNameDetails,
            $entityNameDetails,
            $io,
        );

        $io->writeln('');
        $io->writeln('Factory is generated! Don\'t forget to add it in your sylius resources and corresponding service.');
    }

    private function generateClass(
        Configuration    $configuration,
        ClassNameDetails $classNameDetails,
        Generator        $generator,
        array            $variables = [],
    ): void {
        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getTemplatePath()),
            $variables,
        );
    }

    private function createFactoryService(
        Configuration    $configuration,
        ClassNameDetails $classNameDetails,
        ClassNameDetails $entityNameDetails,
        ConsoleStyle     $io,
    ): void {
        $path = $this->getFile(sprintf('packages/app_%s', Str::asTwigVariable($configuration->getPackage())));
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $builder = new PackageBuilder();
        $builder->createSyliusFactoryService(
            $manipulator,
            $configuration,
            [
                'factory' => $classNameDetails->getFullName(),
                'entity' => $entityNameDetails->getFullName(),
            ],
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function getFile(string $filename): string
    {
        return sprintf('%s/config/%s.php', $this->fileManager->getRootDirectory(), $filename);
    }
}
