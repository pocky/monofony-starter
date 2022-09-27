<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Application\Gateway;

use App\Shared\Application\Gateway\GatewayException;
use App\Shared\Application\Gateway\GatewayRequest;
use App\Shared\Application\Gateway\GatewayResponse;
use App\Shared\Application\Gateway\Instrumentation\AbstractGatewayInstrumentation;
use App\Shared\Application\Gateway\Middleware\Pipe;
use App\Shared\Infrastructure\Instrumentation\Instrumentation;
use App\Shared\Infrastructure\Maker\Enum\Operation;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use App\Shared\Infrastructure\MessageBus\CommandBusInterface;
use App\Shared\Infrastructure\MessageBus\QueryBusInterface;
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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Webmozart\Assert\Assert;

final class Maker extends AbstractMaker
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
            ->addArgument('package', InputArgument::REQUIRED, 'Where is located our package?')
            ->addArgument('name', InputArgument::REQUIRED, 'What\'s the name of the gateway?')
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
        $dependencies->addClassDependency(Pipe::class, 'pipe');
        $dependencies->addClassDependency(Instrumentation::class, 'instrumentation');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): int
    {
        if (false === in_array($input->getOption('operation'), Operation::getValues())) {
            throw new \RuntimeException(sprintf(
                'This operation is not valid one! Should be one of this list: %s',
                implode(', ', Operation::getValues()),
            ));
        }

        $entryChoices = $this->entryChoices($input->getArgument('package'));
        $entry = $entryChoices[0];

        if (1 !== count($entryChoices)) {
            $entry = $io->choice(
                'This gateway is connected to which operation?',
                $entryChoices,
            );
        }

        $configuration = new Configuration(
            $input->getArgument('package'),
            $input->getArgument('name'),
            $entry,
            Operation::from($input->getOption('operation')),
        );

        //$this->generateInstrumentation($configuration, $generator, $io);
        //$this->generateErrorHandler($configuration, $generator, $io);
        //$this->generateLogger($configuration, $generator, $io);
        $this->generateProcessor($configuration, $generator, $io, $input);
        //$this->generateGateway($configuration, $generator, $io);

        $this->writeSuccessMessage($io);

        return Command::SUCCESS;
    }

    private function generateErrorHandler(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $instrumentationDetails = $generator->createClassNameDetails(
            'Instrumentation',
            $configuration->getGatewayPrefix(),
        );

        $errorDetails = $generator->createClassNameDetails(
            'ErrorHandler',
            $configuration->getMiddlewarePrefix(),
        );

        $useStatements = new UseStatementGenerator([
            $instrumentationDetails->getFullName(),
            GatewayException::class,
            GatewayRequest::class,
            GatewayResponse::class,
        ]);

        $generator->generateClass(
            $errorDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getErrorTemplate()),
            [
                'use_statements' => $useStatements,
            ],
        );

        $generator->writeChanges();
    }

    private function generateLogger(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $instrumentationDetails = $generator->createClassNameDetails(
            'Instrumentation',
            $configuration->getGatewayPrefix(),
        );

        $loggerDetails = $generator->createClassNameDetails(
            'Logger',
            $configuration->getMiddlewarePrefix(),
        );

        $useStatements = new UseStatementGenerator([
            $instrumentationDetails->getFullName(),
            GatewayRequest::class,
            GatewayResponse::class,
        ]);

        $generator->generateClass(
            $loggerDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getLoggerTemplate()),
            [
                'use_statements' => $useStatements,
            ],
        );

        $generator->writeChanges();
    }

    private function generateInstrumentation(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $instrumentationDetails = $generator->createClassNameDetails(
            'Instrumentation',
            $configuration->getGatewayPrefix(),
        );

        $useStatements = new UseStatementGenerator([
            AbstractGatewayInstrumentation::class,
        ]);

        $generator->generateClass(
            $instrumentationDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getInstrumentationTemplate()),
            [
                'use_statements' => $useStatements,
                'event_name' => sprintf(
                    '%s.%s',
                    str_replace('_', '.', Str::asTwigVariable($configuration->getPackage())),
                    Str::asTwigVariable($configuration->getName()),
                ),
            ],
        );

        $generator->writeChanges();
    }

    private function generateProcessor(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
        InputInterface $input,
    ): void {
        $methods = [];
        $use = [];
        $responseUse = [];
        $identityGenerator = null;

        $operation = new \ReflectionClass($configuration->getEntry());
        foreach ($operation->getMethods() as $method) {
            if ('__construct' === $method->getName()) {
                continue;
            }

            $methods[] = $method;
        }

        Assert::notEmpty($methods);
        $method = null;

        if (1 !== count($methods)) {
            $response = $io->choice(
                'Which method is your entry point?',
                array_map(static fn (\ReflectionMethod $method) => $method->getName(), $methods),
            );

            $method = $operation->getMethod($response);
        }

        if (null === $method) {
            $method = $methods[0];
        }

        $requestParameters = [
            'required' => [],
            'optional' => [],
        ];

        foreach ($method->getParameters() as $parameter) {
            if (null === $parameter->getType()) {
                continue;
            }

            if (true === str_contains($parameter->getType()->getName(), 'Query')) {
                $use[] = QueryBusInterface::class;
                $use[] = $parameter->getType()->getName();
            }

            if (true === str_contains($parameter->getType()->getName(), 'Command')) {
                $use[] = CommandBusInterface::class;
                $use[] = $parameter->getType()->getName();
            }

            $class = new \ReflectionClass($parameter->getType()->getName());
            $parameters = $class->getMethod('__construct')->getParameters();

            foreach ($parameters as $param) {
                if (null === $param->getType()) {
                    continue;
                }

                $type = $param->getType();
                Assert::notNull($type);

                $field = false === $type->allowsNull() ? 'required' : 'optional';

                $requestParameters[$field][$param->getName()] = $type->getName();
            }
        }

        $responseParameters = [
            'required' => [],
            'optional' => [],
        ];

        if (null !== $method->getReturnType() && 'void' !== $method->getReturnType()->getName()) {
            $field = true === $method->getReturnType()->allowsNull() ? 'optional' : 'required';
            $returnType = $method->getReturnType()->getName();
            $responseParameters[$field]['model'] = $returnType;

            if (false === $method->getReturnType()->isBuiltin()) {
                $returnTypeDetails = $generator->createClassNameDetails(
                    $method->getReturnType()->getName(),
                    '',
                );

                $returnType = $returnTypeDetails->getShortName();
                $responseUse[] = $method->getReturnType()->getName();
                unset($responseParameters[$field]['model']);

                $responseParameters[$field][Str::asLowerCamelCase($returnTypeDetails->getShortName())] = $returnType;
            }
        }

        if (Operation::ADD === $configuration->getOperation()) {
            $generatorChoices = $this->generatorChoices($input->getArgument('package'));
            $identityGenerator = $generatorChoices[0];

            if (1 !== count($generatorChoices)) {
                $identityGenerator = $io->choice(
                    'Which generator should be used for generate identity?',
                    $generatorChoices,
                );
            }

            $identityGeneratorDetails = $generator->createClassNameDetails(
                $identityGenerator,
                '',
            );

            $use[] = $identityGenerator;
            $identityGenerator = $identityGeneratorDetails->getShortName();
        }

        $requestDetails = $generator->createClassNameDetails(
            'Request',
            $configuration->getGatewayPrefix(),
        );

        $useStatements = new UseStatementGenerator([
            PropertyAccess::class,
            GatewayRequest::class,
        ]);

        $generator->generateClass(
            $requestDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getRequestTemplate()),
            [
                'use_statements' => $useStatements,
                'request_parameters' => $requestParameters,
            ],
        );

        $responseDetails = $generator->createClassNameDetails(
            'Response',
            $configuration->getGatewayPrefix(),
        );

        $useStatements = new UseStatementGenerator(array_merge($responseUse, [
            GatewayResponse::class,
        ]));

        $generator->generateClass(
            $responseDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getResponseTemplate()),
            [
                'use_statements' => $useStatements,
                'response_parameters' => $responseParameters,
            ],
        );

        $processorDetails = $generator->createClassNameDetails(
            'Processor',
            $configuration->getMiddlewarePrefix(),
        );

        $useStatements = new UseStatementGenerator(array_merge($use, [
            $requestDetails->getFullName(),
            $responseDetails->getFullName(),
        ]));

        $generator->generateClass(
            $processorDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getProcessorTemplate()),
            [
                'use_statements' => $useStatements,
                'request_parameters' => $requestParameters,
                'generator' => $identityGenerator,
            ],
        );

        $generator->writeChanges();
    }

    private function generateGateway(
        Configuration $configuration,
        Generator $generator,
        ConsoleStyle $io,
    ): void {
        $errorDetails = $generator->createClassNameDetails(
            'ErrorHandler',
            $configuration->getMiddlewarePrefix(),
        );

        $loggerDetails = $generator->createClassNameDetails(
            'Logger',
            $configuration->getMiddlewarePrefix(),
        );

        $processorDetails = $generator->createClassNameDetails(
            'Processor',
            $configuration->getMiddlewarePrefix(),
        );

        $gatewayDetails = $generator->createClassNameDetails(
            'Gateway',
            $configuration->getGatewayPrefix(),
        );

        $useStatements = new UseStatementGenerator([
            $errorDetails->getFullName(),
            $loggerDetails->getFullName(),
            $processorDetails->getFullName(),
            GatewayRequest::class,
            GatewayResponse::class,
            Pipe::class,
        ]);

        $generator->generateClass(
            $gatewayDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/%s.tpl.php', __DIR__, $configuration->getGatewayTemplate()),
            [
                'use_statements' => $useStatements,
            ],
        );

        $generator->writeChanges();
    }

    private function entryChoices(string $package): array
    {
        $choices = [];
        $package = str_replace('\\', '/', $package);

        $finder = new Finder();
        $finder
            ->name(['Handler.php'])
            ->in(sprintf('src/%s/Application/Operation/*/*/', $package))
            ->depth(0);

        foreach ($finder as $file) {
            $builder = new PhpFileManipulator($file->getContents());
            $choices[] = $builder->getFqdn();
        }

        \sort($choices);

        if ($choices === []) {
            throw new RuntimeCommandException('No operation found.');
        }

        return $choices;
    }

    private function generatorChoices(string $package): array
    {
        $choices = [];
        $package = explode('\\', $package);

        $finder = new Finder();
        $finder
            ->name(['*.php'])
            ->in(sprintf('src/%s/Shared/Infrastructure/Identity/', $package[0]))
            ->depth(0);

        foreach ($finder as $file) {
            $builder = new PhpFileManipulator($file->getContents());
            $choices[] = $builder->getFqdn();
        }

        \sort($choices);

        if ($choices === []) {
            throw new RuntimeCommandException('No generator found.');
        }

        return $choices;
    }
}
