<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Application\Operation;

use App\Shared\Infrastructure\Maker\Enum\Operation;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class Maker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:application:operation';
    }

    public static function getCommandDescription(): string
    {
        return 'Create an application operation';
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
            $question = new ChoiceQuestion('What\'s the name of the operation', Operation::getValues());
            $question->setAutocompleterValues(Operation::getValues());

            $answer = $io->askQuestion($question);
            $input->setOption('operation', $answer);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(MessageHandlerInterface::class, 'messenger');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): int
    {
        if (!in_array($input->getOption('operation'), Operation::getValues())) {
            throw new \RuntimeException(sprintf(
                'This operation is not valid one! Should be one of this list: %s',
                implode(', ', Operation::getValues()),
            ));
        }

        $domainChoices = $this->domainChoices($input->getArgument('package'), $input->getOption('operation'));
        $domain = $domainChoices[0];

        if (1 !== count($domainChoices)) {
            $domain = $io->choice(
                'This operation is connected to which domain model?',
                $domainChoices,
            );
        }

        $configuration = new Configuration(
            $input->getArgument('package'),
            $input->getArgument('name'),
            Operation::from($input->getOption('operation')),
            $domain,
        );

        $this->generateDTO($configuration, $generator, $io);
        $this->generateHandler($configuration, $generator, $io);

        $this->writeSuccessMessage($io);

        return Command::SUCCESS;
    }

    private function generateDTO(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $fields = [];

        $domain = new \ReflectionClass($configuration->getDomain());
        $method = $domain->getMethod($configuration->getOperation()->entryMethod());

        foreach ($method->getParameters() as $parameter) {
            $type = $parameter->getType();
            Assert::notNull($type);

            if (strpos((string) $type->getName(), 'Identifier')) {
                $fields[] = [
                    'fieldName' => sprintf('get%s', ucfirst($parameter->getName())),
                    'class' => $type->getName(),
                    'short_name' => Str::getShortClassName($type->getName()),
                    'argument_type' => 'string',
                ];

                continue;
            }

            if (in_array($type->getName(), [
                    'string',
                    'array',
                    'bool',
                    'int',
                ])) {
                $fields[] = [
                    'fieldName' => sprintf('get%s', ucfirst($parameter->getName())),
                    'class' => $type->getName(),
                    'short_name' => Str::getShortClassName($type->getName()),
                    'argument_type' => $type->getName(),
                ];

                continue;
            }

            $model = new \ReflectionClass($type->getName());

            foreach ($model->getMethods() as $method) {
                if ('__construct' === $method->getName()) {
                    continue;
                }

                if ('getId' === $method->getName()) {
                    $fields[] = [
                        'fieldName' => $method->getName(),
                        'class' => $method->getReturnType()->getName(),
                        'short_name' => Str::getShortClassName($method->getReturnType()->getName()),
                        'argument_type' => 'string',
                    ];

                    continue;
                }

                $fields[] = [
                    'fieldName' => $method->getName(),
                    'class' => $method->getReturnType()->getName(),
                    'short_name' => Str::getShortClassName($method->getReturnType()->getName()),
                    'argument_type' => $method->getReturnType()->getName(),
                ];
            }
        }

        $dtoDetails = $generator->createClassNameDetails(
            $configuration->getOperation()->operationDTO(),
            $configuration->getOperationPrefix(),
        );

        $uses = [];
        foreach ($fields as $field) {
            if (!in_array($field['class'], [
                    'string',
                    'array',
                    'bool',
                    'int',
                ])) {
                $uses[] = $field['class'];
            }
        }
        $useStatements = new UseStatementGenerator($uses);

        $generator->generateClass(
            $dtoDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getOperationDataTemplate()),
            [
                'use_statements' => $useStatements,
                'fields' => $fields,
            ],
        );

        $generator->writeChanges();
    }

    private function generateHandler(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $fields = [];
        $methods = [];
        $uses = [];
        $constructor = [];
        $domainReturnType = null;
        $event = [];
        $factory = [];

        $domain = new \ReflectionClass($configuration->getDomain());

        foreach ($domain->getMethods() as $method) {
            if ('__construct' === $method->getName()) {
                continue;
            }

            $methods[] = $method;
        }

        if (1 < count($methods)) {
            $method = $io->choice(
                'This handler is connected to which method of domain model?',
                array_map(static fn (\ReflectionMethod $method) => $method->getName(), $methods),
            );
        } else {
            $method = $methods[0]->getName();
        }

        $domainMethod = $domain->getMethod($method);

        if ('Write' === $configuration->getOperation()->operationType()) {
            $eventDetails = $generator->createClassNameDetails(
                sprintf('%s%s', $configuration->getName(), $configuration->getOperation()->event()),
                $configuration->getOperationPrefix(),
            );

            $generator->generateClass(
                $eventDetails->getFullName(),
                sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getOperationEventTemplate()),
            );

            $generator->writeChanges();

            $event = [
                'name' => $eventDetails->getShortName(),
            ];

            $uses[] = DispatchAfterCurrentBusStamp::class;
            $uses[] = Envelope::class;
            $uses[] = MessageBusInterface::class;

            $constructor[] = [
                'short_name' => 'MessageBusInterface',
                'argument_name' => 'eventBus',
            ];

            if (Operation::DELETE !== $configuration->getOperation()) {
                $dtoDetails = $generator->createClassNameDetails(
                    $configuration->getOperation()->operationDTO(),
                    $configuration->getOperationPrefix(),
                );

                $dto = new \ReflectionClass($dtoDetails->getFullName());
                $arguments = [];
                foreach ($dto->getMethods() as $method) {
                    if ('__construct' === $method->getName()) {
                        continue;
                    }

                    $arguments[] = $method;
                }

                foreach ($arguments as $argument) {
                    $factory['arguments'][str_replace('get', '', strtolower($argument->getName()))] = sprintf('$command->%s()', $argument->getName());
                }

                $factoryClass = new \ReflectionClass(
                    sprintf('%s%s', $configuration->getFactoryPrefix(), 'Builder'),
                );

                $constructor[] = [
                    'short_name' => $factoryClass->getShortName(),
                    'argument_name' => 'builder',
                ];

                $uses[] = $factoryClass->getName();
            }
        }

        $domainReturnType = $domainMethod->getReturnType();

        if (!in_array($domainMethod->getReturnType(), [
                'string',
                'array',
                'bool',
                'int',
            ])) {
            $uses[] = false === $domainMethod->getReturnType()->isBuiltin() ? $domainMethod->getReturnType()->getName() : false;
            $uses[] = $domainMethod->class;
            $domainReturnType = Str::getShortClassName($domainMethod->getReturnType()->getName());

            $constructor[] = [
                'short_name' => Str::getShortClassName($domainMethod->class),
                'argument_name' => Str::asLowerCamelCase(Str::getShortClassName($domainMethod->class)),
            ];
        }

        $useStatements = new UseStatementGenerator(array_merge(array_filter($uses), [
            MessageHandlerInterface::class,
        ]));

        $handlerDetails = $generator->createClassNameDetails(
            'Handler',
            $configuration->getOperationPrefix(),
        );

        $generator->generateClass(
            $handlerDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getOperationHandlerTemplate()),
            [
                'use_statements' => $useStatements,
                'constructor_arguments' => $constructor,
                'domain_argument' => Str::asLowerCamelCase(Str::getShortClassName($domainMethod->class)),
                'domain_method' => $domainMethod->getName(),
                'domain_return_type' => $domainReturnType,
                'factory' => $factory,
                'event' => $event,
            ],
        );

        $generator->writeChanges();
    }

    private function generateEvent(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $handlerDetails = $generator->createClassNameDetails(
            'Handler',
            $configuration->getOperationPrefix(),
        );

        $generator->generateClass(
            $handlerDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getOperationHandlerTemplate()),
        );

        $generator->writeChanges();
    }

    private function domainChoices(string $package, string $operation): array
    {
        $choices = [];
        $package = str_replace('\\', '/', $package);
        $operation = Operation::from($operation);

        $finder = new Finder();
        $finder
            ->name('*.php')
            ->in(sprintf('src/%s/Domain/*', $package))
            ->contains(sprintf('public function %s', $operation->entryMethod()))
            ->depth(0);

        foreach ($finder as $file) {
            $builder = new PhpFileManipulator($file->getContents());
            $choices[] = $builder->getFqdn();
        }

        \sort($choices);

        if ($choices === []) {
            throw new RuntimeCommandException('No domain operation found.');
        }

        return $choices;
    }
}
