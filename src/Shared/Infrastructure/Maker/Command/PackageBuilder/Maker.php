<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\PackageBuilder;

use App\Shared\Infrastructure\Maker\Builder\PackageBuilder;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class Maker extends AbstractMaker
{
    public function __construct(
        private readonly string $projectDir,
        private readonly FileManager $fileManager,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:package';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a package for your application with default configuration';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addArgument('package', InputArgument::REQUIRED, 'The package of the context')
        ;

        $inputConfig->setArgumentAsNonInteractive('entity');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $configuration = new Configuration(
            $input->getArgument('package'),
        );

        $fs = new Filesystem();

        if ($fs->exists(sprintf('%s/src/%s', $this->projectDir, $configuration->getPackage()))) {
            $this->writeErrorMessage($io, sprintf('The package "%s" already exists', $configuration->getPackage()));

            return;
        }

        $fs->mkdir(sprintf('%s/src/%s/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity', $this->projectDir, $configuration->getPackage()));
        $fs->mkdir(sprintf('%s/src/%s/Shared/Domain', $this->projectDir, $configuration->getPackage()));

        $fs->copy(
            sprintf('%s/src/Shared/Infrastructure/Maker/Resources/skeleton/config/packages.tpl.php', $this->projectDir),
            sprintf('%s/config/packages/%s.php', $this->projectDir, Str::asTwigVariable($configuration->getPackage())),
        );

        $this->manipulateApiPlatform($configuration, $io);
        $this->manipulateDoctrine($configuration, $io);
        $this->manipulateSyliusResource($configuration, $io);

        $this->writeSuccessMessage($io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // no dependencies
    }

    private function getPackageFile(string $path, ConsoleStyle $io): string
    {
        $contents = null;
        if ($this->fileManager->fileExists($path)) {
            $contents = $this->fileManager->getFileContents($path);
        }

        $io->text('We found your package configuration file!');

        return $contents;
    }

    private function getFile(string $filename): string
    {
        return sprintf('%s/config/%s.php', $this->fileManager->getRootDirectory(), $filename);
    }

    private function manipulateDoctrine(Configuration $configuration, ConsoleStyle $io): void
    {
        $path = $this->getFile('packages/doctrine');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $manipulator->setIo($io);

        $builder = new PackageBuilder();
        $builder->addMappingToDoctrine($manipulator, $configuration);

        $code = str_replace('\\\\', '\\', $manipulator->getSourceCode());
        $this->fileManager->dumpFile($path, $code);
    }

    private function manipulateApiPlatform(Configuration $configuration, ConsoleStyle $io): void
    {
        $path = $this->getFile('packages/api_platform');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $manipulator->setIo($io);

        $builder = new PackageBuilder();
        $builder->addMappingPathToApiPlatform($manipulator, $configuration);
        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function manipulateSyliusResource(Configuration $configuration, ConsoleStyle $io): void
    {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $manipulator->setIo($io);

        $builder = new PackageBuilder();
        $builder->addMappingPathToSylius($manipulator, $configuration);
        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function writeErrorMessage(ConsoleStyle $io, string $message): void
    {
        $io->newLine();
        $io->error($message);
        $io->newLine();
    }
}
