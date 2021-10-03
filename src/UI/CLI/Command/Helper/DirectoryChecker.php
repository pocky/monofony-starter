<?php

declare(strict_types=1);

namespace App\UI\CLI\Command\Helper;

use App\Shared\Infrastructure\Installer\Checker\CommandDirectoryChecker;
use Symfony\Component\Console\Output\OutputInterface;

class DirectoryChecker
{
    private CommandDirectoryChecker $commandDirectoryChecker;

    public function __construct(CommandDirectoryChecker $commandDirectoryChecker)
    {
        $this->commandDirectoryChecker = $commandDirectoryChecker;
    }

    public function ensureDirectoryExistsAndIsWritable(string $directory, OutputInterface $output, string $commandName): void
    {
        $checker = $this->commandDirectoryChecker;
        $checker->setCommandName($commandName);

        $checker->ensureDirectoryExists($directory, $output);
        $checker->ensureDirectoryIsWritable($directory, $output);
    }
}
