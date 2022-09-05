<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Shared\Infrastructure\Maker\Command\Doctrine\Form;

use App\Shared\Infrastructure\Maker\Builder\SyliusBuilder;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassDetails;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Validation;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Ryan Weaver <weaverryan@gmail.com>
 */
final class Maker extends AbstractMaker
{
    public function __construct(
        private readonly DoctrineHelper $entityHelper,
        private readonly FormTypeRenderer $formTypeRenderer,
        private readonly FileManager $fileManager,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:ui:doctrine:form';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a new form class for a given entity';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'The package of the context')
            ->addArgument('name', InputArgument::REQUIRED, sprintf('The name of the form class (e.g. <fg=yellow>%sType</>)', Str::asClassName(Str::getRandomTerm())))
            ->addArgument('bound-class', InputArgument::REQUIRED, 'The name of Entity or fully qualified model class name that the new form will be bound to (empty for none)')
        ;

        $inputConfig->setArgumentAsNonInteractive('bound-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if ($input->getArgument('bound-class')) {
            return;
        }

        $argument = $command->getDefinition()->getArgument('bound-class');
        $entity = $io->choice($argument->getDescription(), $this->entityChoices());

        $input->setArgument('bound-class', $entity);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $configuration = new Configuration(
            $input->getArgument('name'),
            $input->getArgument('package'),
            $input->getArgument('bound-class'),
        );

        $this->generateFormType($configuration, $generator, $io);

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Add fields to your form and start using it.',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/forms.html</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(
            AbstractType::class,
            // technically only form is needed, but the user will *probably* also want validation
            'form',
        );

        $dependencies->addClassDependency(
            Validation::class,
            'validator',
            // add as an optional dependency: the user *probably* wants validation
            false,
        );

        $dependencies->addClassDependency(
            DoctrineBundle::class,
            'orm',
            false,
        );
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

        if (empty($choices)) {
            throw new RuntimeCommandException('No entities found.');
        }

        return $choices;
    }

    private function generateFormType(
        Configuration $configuration,
        Generator     $generator,
        ConsoleStyle  $io,
    ): void {
        $formClassNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getFormPath(),
            'Type',
        );

        $entity = new \ReflectionClass($configuration->getEntity());

        $boundClassDetails = $generator->createClassNameDetails(
            $entity->getShortName(),
            str_replace('App\\', '', $entity->getNamespaceName()),
        );

        $doctrineEntityDetails = $this->entityHelper->createDoctrineDetails($boundClassDetails->getFullName());

        if (null !== $doctrineEntityDetails) {
            $formFields = $doctrineEntityDetails->getFormFields();
        } else {
            $classDetails = new ClassDetails($boundClassDetails->getFullName());
            $formFields = $classDetails->getFormFields();
        }

        $this->formTypeRenderer->render(
            $formClassNameDetails,
            $formFields,
            $boundClassDetails,
        );

        $generator->writeChanges();

        $this->manipulateSyliusForm(
            $configuration,
            $formClassNameDetails,
            $io,
        );
    }

    private function manipulateSyliusForm(
        Configuration $configuration,
        classNameDetails $classNameDetails,
        ConsoleStyle $io,
    ): void {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $manipulator->setIo($io);

        $builder = new SyliusBuilder();
        $builder->addResource(
            $manipulator,
            $configuration,
            [
                'type' => 'form',
                'value' => $classNameDetails->getFullName(),
            ],
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function getFile(string $filename): string
    {
        return sprintf('%s/config/%s.php', $this->fileManager->getRootDirectory(), $filename);
    }
}
