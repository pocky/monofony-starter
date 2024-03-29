<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Installer\Provider;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\Assert\Assert;

final readonly class DatabaseSetupCommandsProvider implements DatabaseSetupCommandsProviderInterface
{
    public function __construct(
        private ManagerRegistry $doctrineRegistry,
    ) {
    }

    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper): array
    {
        if (!$this->isDatabasePresent()) {
            return [
                'doctrine:database:create',
                'doctrine:migrations:migrate' => [
                    '--no-interaction' => true,
                ],
            ];
        }

        return array_merge($this->setupDatabase($input, $output, $questionHelper), [
            'doctrine:migrations:version' => [
                '--add' => true,
                '--all' => true,
                '--no-interaction' => true,
            ],
        ]);
    }

    /**
     * @throws \Exception
     */
    private function isDatabasePresent(): bool
    {
        $databaseName = $this->getDatabaseName();

        try {
            $schemaManager = $this->getSchemaManager();

            return in_array($databaseName, $schemaManager->listDatabases());
        } catch (\Exception $exception) {
            $message = $exception->getMessage();

            $mysqlDatabaseError = str_contains($message, sprintf("Unknown database '%s'", $databaseName));
            $postgresDatabaseError = str_contains($message, sprintf('database "%s" does not exist', $databaseName));

            if ($mysqlDatabaseError || $postgresDatabaseError) {
                return false;
            }

            throw $exception;
        }
    }

    private function setupDatabase(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
    ): array {
        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('It appears that your database already exists.');
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');

        $question = new ConfirmationQuestion('Would you like to reset it? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:database:drop' => [
                    '--force' => true,
                ],
                'doctrine:database:create',
                'doctrine:migrations:migrate' => [
                    '--no-interaction' => true,

                ],
            ];
        }

        if (!$this->isSchemaPresent()) {
            return [
                'doctrine:migrations:migrate' => [
                    '--no-interaction' => true,

                ],
            ];
        }

        $outputStyle->writeln('Seems like your database contains schema.');
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');
        $question = new ConfirmationQuestion('Do you want to reset it? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:schema:drop' => [
                    '--force' => true,
                ],
                'doctrine:migrations:migrate' => [
                    '--no-interaction' => true,

                ],
            ];
        }

        return [];
    }

    private function isSchemaPresent(): bool
    {
        return [] !== $this->getSchemaManager()->listTableNames();
    }

    /**
     * @throws Exception
     */
    private function getDatabaseName(): string
    {
        $database = $this->getEntityManager()->getConnection()->getDatabase();
        Assert::notNull($database);

        return $database;
    }

    private function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getEntityManager()->getConnection()->getSchemaManager();
    }

    private function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->doctrineRegistry->getManager();
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);

        return $entityManager;
    }
}
