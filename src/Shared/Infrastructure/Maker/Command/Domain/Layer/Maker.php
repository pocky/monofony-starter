<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Command\Domain\Layer;

use App\Shared\Infrastructure\Maker\Enum\Operation;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;

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
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'What type of operation?')
        ;
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (false === $input->getOption('type')) {
            $question = new ChoiceQuestion('Please enter the name of the operation', Operation::getValues());
            $question->setAutocompleterValues(Operation::getValues());

            $answer = $io->askQuestion($question);
            $input->setOption('type', Operation::from($answer));
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): int
    {
        if (false === in_array($input->getOption('type'), Operation::getValues())) {
            throw new \RuntimeException(sprintf(
                'This type is not valid one! Should be one of this list: %s',
                implode(', ', Operation::getValues()),
            ));
        }

        $configuration = new Configuration(
            $input->getArgument('package'),
            $input->getArgument('name'),
            Operation::from($input->getOption('type')),
        );

        $generator->writeChanges();
        $this->writeSuccessMessage($io);

        return Command::SUCCESS;
    }
}
