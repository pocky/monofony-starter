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

namespace App\Shared\Infrastructure\Maker\Command\Sylius\Grid;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\GridBundle\Config\GridConfigInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

final class Maker extends AbstractMaker
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:ui:sylius:grid';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a Grid for a Doctrine entity class';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription(self::getCommandDescription())
            ->addArgument('package', InputArgument::REQUIRED, 'The package of the context')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity class to create a grid for')
        ;

        $inputConfig->setArgumentAsNonInteractive('entity');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if ($input->getArgument('entity')) {
            return;
        }

        $argument = $command->getDefinition()->getArgument('entity');
        $entity = $io->choice($argument->getDescription(), $this->entityChoices());

        $input->setArgument('entity', $entity);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $configuration = new Configuration(
            $input->getArgument('package'),
            $input->getArgument('entity'),
        );

        $this->generateGrid($configuration, $generator, $io);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            GridConfigInterface::class,
            'grid',
        );
    }

    private function generateGrid(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        if (!\class_exists($configuration->getEntity())) {
            throw new RuntimeCommandException(\sprintf('Entity "%s" not found.', $configuration->getEntity()));
        }

        $entity = new \ReflectionClass($configuration->getEntity());
        $repository = new \ReflectionClass($this->managerRegistry->getRepository($entity->getName()));

        if (false === \str_starts_with($repository->getName(), $generator->getRootNamespace())) {
            // not using a custom repository
            $repository = null;
        }

        $this->defaultFieldsFor($entity->getName());

        $classNameDetails = $generator->createClassNameDetails(
            $entity->getShortName(),
            $configuration->getGridPath(),
            'Grid',
        );

        $this->generateClass(
            $configuration,
            $classNameDetails,
            $generator,
            [
                'entity' => $entity,
                'short_name' => sprintf('%s/%s', $configuration->getPackage(), $entity->getShortName()),
                'defaultFields' => $this->defaultFieldsFor($entity->getName()),
                'repository' => $repository,
            ],
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);
    }

    private function entityChoices(): array
    {
        $choices = [];

        foreach ($this->managerRegistry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                $choices[] = $metadata->getName();
            }
        }

        \sort($choices);

        if ($choices === []) {
            throw new RuntimeCommandException('No entities found.');
        }

        return $choices;
    }

    private function defaultFieldsFor(string $class): iterable
    {
        $entityManager = $this->managerRegistry->getManagerForClass($class);

        if (!$entityManager instanceof EntityManagerInterface) {
            return [];
        }

        $metadata = $entityManager->getClassMetadata($class);
        $ids = $metadata->getIdentifierFieldNames();

        foreach ($metadata->fieldMappings as $property) {
            // ignore identifiers
            if (\in_array($property['fieldName'], $ids, true)) {
                continue;
            }

            $type = \mb_strtoupper($property['type']);

            yield $property['fieldName'] => $type;
        }

        return [];
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
}
