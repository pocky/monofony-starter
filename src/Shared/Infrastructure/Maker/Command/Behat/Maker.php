<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Behat;

use Sylius\Component\Resource\Metadata\RegistryInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class Maker extends AbstractMaker
{
    public function __construct(
        private readonly RegistryInterface $registry,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:behat:crud';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate CRUD Behat tests';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::OPTIONAL, 'Where is located our package?')
            ->addArgument('name', InputArgument::OPTIONAL, 'What\'s the name of the entity?')
            ->addOption('api', 'a', InputOption::VALUE_OPTIONAL, 'Generate API tests ? ', true)
            ->addOption('identifier', 'i', InputOption::VALUE_OPTIONAL, 'Identifier field for entity', 'id')
            ->setHelp('Generate CRUD Behat tests for entity from a package.');
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $configuration = new Configuration(
            $input->getArgument('name'),
            $input->getArgument('package'),
            $input->hasOption('api'),
            $input->getOption('identifier'),
        );

        $this->generateCRUD($generator, $configuration, $io);

        $this->writeSuccessMessage($io);
    }

    private function generateCRUD(
        Generator $generator,
        Configuration $configuration,
        ConsoleStyle $io,
    ): void {
        $pagesUsers = $this->generatePages($generator, $configuration);

        $this->generateContextClass(
            $generator,
            $configuration,
            $pagesUsers,
        );

        if ($configuration->hasApi()) {
            $this->generateApiContextClass(
                $generator,
                $configuration,
            );
        }

        $this->generateSetupClass(
            $generator,
            $configuration,
        );

        $this->generateFeatures(
            $generator,
            $configuration,
            $io,
        );

        $this->generateFeatureConfigFile(
            $generator,
            $configuration,
            $io,
        );

        if ($configuration->hasApi()) {
            $this->generateApiFeatures(
                $generator,
                $configuration,
                $io,
            );

            $this->generateApiFeatureConfigFile(
                $generator,
                $configuration,
                $io,
            );
        }

        $io->info('Done, please check the features carefully.');
    }

    private function generatePages(
        Generator $generator,
        Configuration $configuration,
    ): array {
        $resourceName = $this->getResourceName($generator, $configuration);

        $indexPageClassNameDetails = $generator->createClassNameDetails(
            'Index',
            $configuration->getPagePath() . ucfirst($configuration->getEntityName()),
            'Page',
        );

        $fields = $this->getEntityFields($generator, $configuration);

        $this->generateClass(
            $indexPageClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getIndexPageTemplatePath()),
            [
                'entity_name' => $configuration->getEntityName(),
                'entity_route' => sprintf('app_backend_%s_index', $resourceName),
                'entity_identifier' => $configuration->getIdentifier(),
            ],
        );

        $generator->writeChanges();

        $createPageClassNameDetails = $generator->createClassNameDetails(
            'Create',
            $configuration->getPagePath() . ucfirst($configuration->getEntityName()),
            'Page',
        );

        $this->generateClass(
            $createPageClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getCreatePageTemplatePath()),
            [
                'entity_name' => $configuration->getEntityName(),
                'entity_route' => sprintf('app_backend_%s_create', $resourceName),
                'defined_elements' => $fields,
            ],
        );

        $generator->writeChanges();

        $updatePageClassNameDetails = $generator->createClassNameDetails(
            'Update',
            $configuration->getPagePath() . ucfirst($configuration->getEntityName()),
            'Page',
        );

        $this->generateClass(
            $updatePageClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getUpdatePageTemplatePath()),
            [
                'entity_name' => $configuration->getEntityName(),
                'entity_route' => sprintf('app_backend_%s_update', $resourceName),
                'defined_elements' => $fields,
            ],
        );

        $generator->writeChanges();

        return [
            $indexPageClassNameDetails->getFullName(),
            $createPageClassNameDetails->getFullName(),
            $updatePageClassNameDetails->getFullName(),
        ];
    }

    private function generateContextClass(
        Generator $generator,
        Configuration $configuration,
        array $uses = [],
    ): void {
        $entityNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityPath(),
        );

        $contextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Managing%s', $configuration->getName()),
            $configuration->getContextPath(),
            'Context',
        );

        $useStatements = new UseStatementGenerator([
            $entityNameDetails->getFullName(),
            ...$uses,
        ]);

        $this->generateClass(
            $contextClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getContextTemplatePath()),
            [
                'use_statements' => $useStatements,
                'entity_name' => $configuration->getEntityName(),
                'entity_identifier' => $configuration->getIdentifier(),
            ],
        );

        $generator->writeChanges();
    }

    private function generateApiContextClass(
        Generator $generator,
        Configuration $configuration,
    ): void {
        $entityNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityPath(),
        );

        $contextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Managing%s', $configuration->getName()),
            $configuration->getApiContextPath(),
            'Context',
        );

        $useStatements = new UseStatementGenerator([
            $entityNameDetails->getFullName(),
        ]);

        $this->generateClass(
            $contextClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getApiContextTemplatePath()),
            [
                'use_statements' => $useStatements,
                'entity_name' => $configuration->getEntityName(),
                'entity_identifier' => $configuration->getIdentifier(),
            ],
        );

        $generator->writeChanges();
    }

    private function generateSetupClass(
        Generator $generator,
        Configuration $configuration,
    ): void {
        $entityFactoryNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityFactoryPath(),
            'Factory',
        );

        $setupContextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Setup%s', $configuration->getName()),
            $configuration->getSetupContextPath(),
            'Context',
        );

        $useStatements = new UseStatementGenerator([
            $entityFactoryNameDetails->getFullName(),
        ]);

        $this->generateClass(
            $setupContextClassNameDetails,
            $generator,
            sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getSetupContextTemplatePath()),
            [
                'use_statements' => $useStatements,
                'entity_name' => $configuration->getEntityName(),
                'entity_factory_class' => $entityFactoryNameDetails->getShortName(),
                'entity_identifier' => $configuration->getIdentifier(),
            ],
        );

        $generator->writeChanges();
    }

    private function generateFeatures(
        Generator $generator,
        Configuration $configuration,
        ConsoleStyle $io,
    ): void {
        $featuresFolder = sprintf('%s/../../../../../../features/backend/%s', __DIR__, $configuration->getFeaturesDirectory());
        if (!file_exists($featuresFolder)) {
            mkdir($featuresFolder, 0777, true);
        }

        $fields = '';
        foreach (array_keys($this->getEntityFields($generator, $configuration)) as $key) {
            if ($key !== $configuration->getIdentifier()) {
                $fields .= sprintf('        And I set its %s to "%s %s"', $key, $configuration->getName(), $key) . "\n";
            }
        }

        foreach ($configuration->getFeaturesTemplatePaths() as $file => $featurePath) {
            $content = file_get_contents(sprintf('%s/../../Resources/skeleton/%s', __DIR__, $featurePath));
            $content = str_replace('%entity_name%', $configuration->getEntityName(), $content);
            $content = str_replace('%entity_identifier%', $configuration->getIdentifier(), $content);
            $content = str_replace('%entity_fields%', $fields, $content);
            $pathTo = sprintf('%s/%s_%s.feature', $featuresFolder, $file, $configuration->getEntityName());
            file_put_contents($pathTo, $content);

            $io->writeln(sprintf('created: %s_%s.feature', $file, $configuration->getEntityName()));
        }
    }

    private function generateApiFeatures(
        Generator $generator,
        Configuration $configuration,
        ConsoleStyle $io,
    ): void {
        $featuresFolder = sprintf('%s/../../../../../../features/api/%s', __DIR__, $configuration->getFeaturesDirectory());
        if (!file_exists($featuresFolder)) {
            mkdir($featuresFolder, 0777, true);
        }

        $fields = '';
        foreach (array_keys($this->getEntityFields($generator, $configuration)) as $key) {
            if ($key !== $configuration->getIdentifier()) {
                $fields .= sprintf('        And I set its %s to "%s %s"', $key, $configuration->getName(), $key) . "\n";
            }
        }

        foreach ($configuration->getApiFeaturesTemplatePaths() as $file => $featurePath) {
            $content = file_get_contents(sprintf('%s/../../Resources/skeleton/%s', __DIR__, $featurePath));
            $content = str_replace('%entity_name%', $configuration->getEntityName(), $content);
            $content = str_replace('%entity_identifier%', $configuration->getIdentifier(), $content);
            $content = str_replace('%entity_fields%', $fields, $content);
            $pathTo = sprintf('%s/%s_%s.feature', $featuresFolder, $file, $configuration->getEntityName());
            file_put_contents($pathTo, $content);

            $io->writeln(sprintf('created: API %s_%s.feature', $file, $configuration->getEntityName()));
        }
    }

    private function generateFeatureConfigFile(
        Generator $generator,
        Configuration $configuration,
        ConsoleStyle $io,
    ): void {
        $contextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Managing%s', $configuration->getName()),
            $configuration->getContextPath(),
            'Context',
        );

        $setupContextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Setup%s', $configuration->getName()),
            $configuration->getSetupContextPath(),
            'Context',
        );

        $configFolder = sprintf('%s/../../../../../../config/behat/suites/backend/%s', __DIR__, $configuration->getFeaturesDirectory());
        if (!file_exists($configFolder)) {
            mkdir($configFolder, 0777, true);
        }

        $content = file_get_contents(sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getFeaturesConfigPath()));
        $content = str_replace('%entity_name%', $configuration->getEntityName(), $content);
        $content = str_replace('%setup_context_class%', $setupContextClassNameDetails->getFullName(), $content);
        $content = str_replace('%context_class%', $contextClassNameDetails->getFullName(), $content);

        $pathTo = sprintf('%s/managing_%s.yaml', $configFolder, $configuration->getEntityName());
        file_put_contents($pathTo, $content);

        $io->writeln(sprintf('created: managing_%s.yaml', $configuration->getEntityName()));
    }

    private function generateApiFeatureConfigFile(
        Generator $generator,
        Configuration $configuration,
        ConsoleStyle $io,
    ): void {
        $contextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Managing%s', $configuration->getName()),
            $configuration->getApiContextPath(),
            'Context',
        );

        $setupContextClassNameDetails = $generator->createClassNameDetails(
            \sprintf('Setup%s', $configuration->getName()),
            $configuration->getSetupContextPath(),
            'Context',
        );

        $configFolder = sprintf('%s/../../../../../../config/behat/suites/api/%s', __DIR__, $configuration->getFeaturesDirectory());
        if (!file_exists($configFolder)) {
            mkdir($configFolder, 0777, true);
        }

        $content = file_get_contents(sprintf('%s/../../Resources/skeleton/%s', __DIR__, $configuration->getApiFeaturesConfigPath()));
        $content = str_replace('%entity_name%', $configuration->getEntityName(), $content);
        $content = str_replace('%setup_context_class%', $setupContextClassNameDetails->getFullName(), $content);
        $content = str_replace('%context_class%', $contextClassNameDetails->getFullName(), $content);

        $pathTo = sprintf('%s/managing_%s.yaml', $configFolder, $configuration->getEntityName());
        file_put_contents($pathTo, $content);

        $io->writeln(sprintf('created: API managing_%s.yaml', $configuration->getEntityName()));
    }

    private function getEntityFields(
        Generator $generator,
        Configuration $configuration,
    ): array {
        $entityNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityPath(),
        );

        $reflClass = new \ReflectionClass($entityNameDetails->getFullName());

        return array_reduce($reflClass->getProperties(), static function (array $result, \ReflectionProperty $prop) use ($configuration) {
            if ('id' !== $prop->getName()) {
                $result[$prop->getName()] = sprintf("'#%s_%s'", $configuration->getEntityName(), $prop->getName());
            }

            return $result;
        }, []);
    }

    private function getResourceName(
        Generator $generator,
        Configuration $configuration,
    ): string {
        $entityNameDetails = $generator->createClassNameDetails(
            $configuration->getName(),
            $configuration->getEntityPath(),
        );

        $metadata = $this->registry->getByClass($entityNameDetails->getFullName());
        ['model' => $model] = $metadata->getParameter('classes');
        if ($model === $entityNameDetails->getFullName()) {
            return $metadata->getName();
        }

        return $configuration->getEntityName();
    }

    private function generateClass(
        ClassNameDetails $classNameDetails,
        Generator $generator,
        string $templateName,
        array $variables = [],
    ): void {
        $generator->generateClass(
            $classNameDetails->getFullName(),
            $templateName,
            $variables,
        );
    }
}
