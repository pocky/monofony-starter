<?php

declare(strict_types=1);

namespace App\UI\CLI\Command\Installer;

use App\UI\CLI\Command\Helper\CommandsRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'app:install:sample-data',
)]
final class InstallSampleDataCommand extends Command
{
    public function __construct(
        private readonly CommandsRunner $commandsRunner,
        private readonly string $environment,
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Install sample data into AppName.')
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command loads the sample data for AppName.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $io = new SymfonyStyle($input, $output);
        $io->newLine();
        $io->title(sprintf(
            'Loading sample data for environment <info>%s</info>.',
            $this->environment,
        ));

        $io->warning('This action will erase your database.');

        if (!$questionHelper->ask($input, $output, new ConfirmationQuestion('Continue? (y/N) ', false))) {
            $io->writeln('Cancelled loading sample data.');

            return 0;
        }

        $commands = [
            'doctrine:fixtures:load' => [
                '--no-interaction' => true,
            ],
        ];

        $application = $this->getApplication();
        Assert::notNull($application);

        $this->commandsRunner->run($commands, $input, $output, $application);
        $io->newLine(2);

        return 0;
    }
}
