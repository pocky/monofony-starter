<?php

declare(strict_types=1);

namespace App\UI\CLI\Command\Installer;

use App\UI\CLI\Command\Helper\CommandsRunner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: 'app:install:assets',
)]
class InstallAssetsCommand extends Command
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
        $this->setDescription('Installs all AppName assets.')
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command downloads and installs all AppName media assets.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('Installing AppName assets for environment <info>%s</info>.', $this->environment));

        $application = $this->getApplication();
        Assert::notNull($application);

        $commands = ['assets:install'];
        $this->commandsRunner->run($commands, $input, $output, $application);

        return 0;
    }
}
