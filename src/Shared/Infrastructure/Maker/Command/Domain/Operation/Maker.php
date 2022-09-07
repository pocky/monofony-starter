<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Domain\Operation;

use function App\Shared\Infrastructure\Maker\Command\DomainOperation\mb_strtolower;
use App\Shared\Infrastructure\Maker\Enum\Operation;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Webmozart\Assert\Assert;

final class Maker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:domain:operation';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a domain operation';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->addArgument('package', InputArgument::REQUIRED, 'Where is located our package?')
            ->addArgument('name', InputArgument::REQUIRED, 'What\'s the name of the operation?')
            ->addOption('operation', null, InputOption::VALUE_OPTIONAL, 'What type of operation?')
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (null === $input->getOption('operation')) {
            $question = new ChoiceQuestion('Please enter the name of the operation', Operation::getValues());
            $question->setAutocompleterValues(Operation::getValues());

            $answer = $io->askQuestion($question);
            $input->setOption('operation', $answer);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): int
    {
        if (false === in_array($input->getOption('operation'), Operation::getValues())) {
            throw new \RuntimeException(sprintf(
                'This operation is not valid one! Should be one of this list: %s',
                implode(', ', Operation::getValues()),
            ));
        }

        $model = $io->askQuestion(new Question('Please enter the name of your model'));

        $identifier = $io->choice(
            'What is your identifier?',
            $this->identifierChoices(),
        );

        $configuration = new Configuration(
            $input->getArgument('package'),
            $input->getArgument('name'),
            Operation::from($input->getOption('operation')),
            $model,
            $identifier,
        );

        $this->generateException($configuration, $generator, $io);
        $this->generateModel($configuration, $generator, $io);
        $this->generateFactory($configuration, $generator, $io);
        $this->generatePersistence($configuration, $generator, $io);
        $this->generateEntryClass($configuration, $generator, $io);

        $this->writeSuccessMessage($io);

        return Command::SUCCESS;
    }

    private function generateException(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        if (Operation::BROWSE === $configuration->getOperation()) {
            return;
        }

        $identifier = new \ReflectionClass($configuration->getIdentifierName());

        $modelDetails = $generator->createClassNameDetails(
            $configuration->getModelName(),
            $configuration->getModelPrefix(),
        );

        $useStatements = new UseStatementGenerator([
            $identifier->getName(),
            $modelDetails->getFullName(),
        ]);

        $exceptionDetails = $generator->createClassNameDetails(
            $configuration->getExceptionName(),
            $configuration->getExceptionPrefix(),
        );

        $generator->generateClass(
            $exceptionDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getExceptionTemplate()),
            [
                'entry_method' => $configuration->getOperation()->entryMethod(),
                'use_statements' => $useStatements,
                'identifier_name' => $identifier->getShortName(),
                'model_name' => $modelDetails->getShortName(),
            ],
        );

        $generator->writeChanges();
    }

    private function generateModel(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $isFirstField = true;
        $currentFields = [];

        while (true) {
            $newField = $this->askForNextProperty($io, $currentFields, $isFirstField);
            $isFirstField = false;

            if (null === $newField) {
                break;
            }

            if (\is_array($newField)) {
                $currentFields[] = $newField;
            } else {
                throw new \Exception('Invalid value');
            }
        }

        $identifier = new \ReflectionClass($configuration->getIdentifierName());

        $useStatements = new UseStatementGenerator([
            $identifier->getName(),
        ]);

        $classNameDetails = $generator->createClassNameDetails(
            $configuration->getModelName(),
            $configuration->getModelPrefix(),
        );

        $generator->generateClass(
            $classNameDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getModelTemplate()),
            [
                'use_statements' => $useStatements,
                'identifier_name' => $identifier->getShortName(),
                'fields' => $currentFields,
            ],
        );

        if (Operation::BROWSE === $configuration->getOperation()) {
            $classListNameDetails = $generator->createClassNameDetails(
                $configuration->getModelName(),
                $configuration->getModelPrefix(),
                'List',
            );

            $generator->generateClass(
                $classListNameDetails->getFullName(),
                sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getModelListTemplate()),
                [
                    'list_name' => $configuration->getModelName(),
                ],
            );
        }

        $generator->writeChanges();
    }

    private function generateFactory(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $modelDetails = $generator->createClassNameDetails(
            $configuration->getModelName(),
            $configuration->getModelPrefix(),
        );

        $model = new \ReflectionClass($modelDetails->getFullName());
        $properties = array_map(static fn (\ReflectionProperty $property) => $property->getName(), $model->getProperties());

        $factoryDetails = $generator->createClassNameDetails(
            $configuration->getFactoryName(),
            $configuration->getFactoryPrefix(),
        );

        $useStatements = new UseStatementGenerator([
            $model->getName(),
            Assert::class,
        ]);

        $generator->generateClass(
            $factoryDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getFactoryTemplate()),
            [
                'use_statements' => $useStatements,
                'model_name' => $configuration->getModelName(),
                'properties' => $properties,
            ],
        );

        $generator->writeChanges();
    }

    private function generatePersistence(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $model = null;
        $identifier = null;
        $useStatements = [];

        $persistenceDetails = $generator->createClassNameDetails(
            $configuration->getPersistenceName(),
            $configuration->getPersistencePrefix(),
        );

        if (true === in_array($configuration->getOperation(), [
            Operation::ADD,
            Operation::READ,
            Operation::DELETE,
        ])) {
            $modelDetails = $generator->createClassNameDetails(
                $configuration->getModelName(),
                $configuration->getModelPrefix(),
            );

            $model = $modelDetails->getShortName();
            $useStatements[] = $modelDetails->getFullName();
        }

        if (true === in_array($configuration->getOperation(), [
                Operation::READ,
                Operation::DELETE,
        ])) {
            $identifier = new \ReflectionClass($configuration->getIdentifierName());

            $useStatements[] = $identifier->getName();
            $identifier = $identifier->getShortName();
        }

        $useStatements = new UseStatementGenerator(array_merge(
            $useStatements,
            [],
        ));

        $generator->generateClass(
            $persistenceDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getPersistenceTemplate()),
            [
                'use_statements' => $useStatements,
                'entry_method' => $configuration->getOperation()->entryMethod(),
                'model_name' => $model,
                'identifier' => $identifier,
            ],
        );

        $generator->writeChanges();
    }

    private function generateEntryClass(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $constructorArguments = [];
        $statements = [];
        $entryModel = null;
        $exception = null;
        $identifier = null;

        $persistenceDetails = $generator->createClassNameDetails(
            $configuration->getPersistenceName(),
            $configuration->getPersistencePrefix(),
        );

        $modelDetails = $generator->createClassNameDetails(
            $configuration->getModelName(),
            $configuration->getModelPrefix(),
        );

        $model = new \ReflectionClass($modelDetails->getFullName());
        $properties = array_map(static fn (\ReflectionProperty $property) => $property->getName(), $model->getProperties());

        if (true === in_array($configuration->getOperation(), [
            Operation::BROWSE,
            Operation::READ,
            Operation::DELETE,
        ])) {
            $identifier = new \ReflectionClass($configuration->getIdentifierName());
            $statements[] = $identifier->getName();
            $identifier = $identifier->getShortName();
        }

        if (Operation::BROWSE === $configuration->getOperation()) {
            $factoryDetails = $generator->createClassNameDetails(
                $configuration->getFactoryName(),
                $configuration->getFactoryPrefix(),
            );

            $modelListDetails = $generator->createClassNameDetails(
                $configuration->getModelName(),
                $configuration->getModelPrefix(),
                'List',
            );

            $constructorArguments[] = [
                'class_name' => $factoryDetails->getShortName(),
                'argument_name' => 'builder',
            ];

            $entryModel = $modelListDetails->getShortName();
            $statements[] = $modelListDetails->getFullName();
            $statements[] = $factoryDetails->getFullName();
        }

        if (null === $entryModel) {
            $entryModel = $model->getShortName();
        }

        if (Operation::BROWSE !== $configuration->getOperation()) {
            $exceptionDetails = $generator->createClassNameDetails(
                $configuration->getExceptionName(),
                $configuration->getExceptionPrefix(),
            );

            $exception = $exceptionDetails->getShortName();
            $statements[] = $exceptionDetails->getFullName();
        }

        $constructorArguments[] = [
            'class_name' => $persistenceDetails->getShortName(),
            'argument_name' => mb_strtolower($configuration->getOperation()->type()),
        ];

        $useStatements = new UseStatementGenerator(array_merge($statements, [
            $persistenceDetails->getFullName(),
            $modelDetails->getFullName(),
        ]));

        $entryDetails = $generator->createClassNameDetails(
            $configuration->getEntryName(),
            $configuration->getEntryPrefix(),
        );

        $generator->generateClass(
            $entryDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getEntryTemplate()),
            [
                'use_statements' => $useStatements,
                'constructor_arguments' => $constructorArguments,
                'entry_method' => $configuration->getOperation()->entryMethod(),
                'entry_model' => $entryModel,
                'properties' => $properties,
                'identifier' => $identifier,
                'exception' => $exception,
            ],
        );

        $generator->writeChanges();
    }

    private function askForNextProperty(
        ConsoleStyle $io,
        array $fields,
        bool $isFirstField,
    ): array|null {
        $io->writeln('');

        if ($isFirstField) {
            $questionText = 'New property name (press <return> to stop adding properties)';
        } else {
            $questionText = 'Add another property? Enter the property name (or press <return> to stop adding properties)';
        }

        $fieldName = $io->ask($questionText, null, function ($name) use ($fields) {
            // allow it to be empty
            if (!$name) {
                return $name;
            }

            if (\in_array($name, $fields)) {
                throw new \InvalidArgumentException(sprintf('The "%s" property already exists.', $name));
            }

            return Validator::validatePropertyName($name);
        });

        if (!$fieldName) {
            return null;
        }

        $type = null;
        while (null === $type) {
            $type = $io->ask('Which typehint?');
        }

        $arguments = $this->askForNextArgument($io, []);

        return ['fieldName' => $fieldName, 'type' => $type, 'arguments' => $arguments];
    }

    private function askForNextArgument(
        ConsoleStyle $io,
        array $arguments = [],
    ): array|null {
        $io->writeln('');

        while ([] === $arguments) {
            $argumentName = $io->ask('Argument name? (press <return> to stop adding arguments)');
            if (null === $argumentName) {
                break;
            }

            $argumentType = $io->ask('Argument type?');
            $arguments[] = ['argumentName' => $argumentName, 'argumentType' => $argumentType];
        }

        return $arguments;
    }

    private function identifierChoices(): array
    {
        $choices = [];
        $finder = new Finder();
        $finder->in('src/**/Shared/Domain/Identifier');


        foreach ($finder as $file) {
            $builder = new PhpFileManipulator($file->getContents());
            $choices[] = $builder->getFqdn();
        }

        \sort($choices);

        if (empty($choices)) {
            throw new RuntimeCommandException('No identifier found.');
        }

        return $choices;
    }
}
