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

namespace App\Shared\Infrastructure\Maker\Command\DoctrineEntity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Shared\Infrastructure\Maker\Builder\SyliusBuilder;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Doctrine\DBAL\Types\Types;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Annotation\SyliusCrudRoutes;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final class SyliusEntityClassGenerator
{
    public function __construct(
        private readonly Generator $generator,
        private readonly DoctrineHelper $doctrineHelper,
        private readonly FileManager $fileManager,
    ) {
    }

    public function generateEntityClass(
        Configuration $configuration,
        ClassNameDetails $entityClassDetails,
        ClassNameDetails $identifierClassDetails,
        bool $apiResource,
        bool $syliusCrud,
        bool $generateRepositoryClass = false,
    ): string {
        $repoClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            $configuration->getORMPath(),
            'Repository',
        );

        $package = Str::asTwigVariable($configuration->getPackage());
        $package = str_replace('_shared', '', $package);
        $tableName = $this->doctrineHelper->getPotentialTableName($entityClassDetails->getFullName());

        $useStatements = new UseStatementGenerator([
            $repoClassDetails->getFullName(),
            $identifierClassDetails->getFullName(),
            Types::class,
            ['Doctrine\\ORM\\Mapping' => 'ORM'],
        ]);

        if ($apiResource) {
            $useStatements->addUseStatement(class_exists(ApiResource::class) ? ApiResource::class : \ApiPlatform\Core\Annotation\ApiResource::class);
        }

        if ($syliusCrud) {
            $useStatements->addUseStatement(class_exists(SyliusCrudRoutes::class) ? SyliusCrudRoutes::class : \Sylius\Component\Resource\Annotation\SyliusCrudRoutes::class);
            $useStatements->addUseStatement(class_exists(ResourceInterface::class) ? ResourceInterface::class : \Sylius\Component\Resource\Model\ResourceInterface::class);
        }

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            sprintf('%s/../../Resources/skeleton/infrastructure/doctrine/Entity.tpl.php', __DIR__),
            [
                'use_statements' => $useStatements,
                'repository_class_name' => $repoClassDetails->getShortName(),
                'api_resource' => $apiResource,
                'sylius_crud' => $syliusCrud,
                'table_name' => sprintf('%s_%s', $package, $tableName),
                'crud_route_package' => $package,
                'crud_route_alias' => sprintf('%s.%s', $package, $tableName),
                'crud_route_path' => Str::asRoutePath($entityClassDetails->getRelativeName()),
                'crud_route_grid' => sprintf('%s_%s', $package, $tableName),
                'crud_route_entity' => $tableName,
                'identifier_name' => $identifierClassDetails->getShortName(),
            ],
        );

        if ($generateRepositoryClass) {
            $this->generateRepositoryClass(
                $repoClassDetails->getFullName(),
                $entityClassDetails->getFullName(),
            );
        }

        $this->generator->writeChanges();

        return $entityPath;
    }


    public function generateRepositoryClass(string $repositoryClass, string $entityClass): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);

        $useStatements = new UseStatementGenerator([
            $entityClass,
            EntityRepository::class,
        ]);

        $this->generator->generateClass(
            $repositoryClass,
            sprintf('%s/../../Resources/skeleton/infrastructure/doctrine/Repository.tpl.php', __DIR__),
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
            ],
        );
    }

    public function manipulateSyliusModel(
        Configuration $configuration,
        ClassNameDetails $classNameDetails,
        ConsoleStyle $io
    ): void {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path)
        );

        $manipulator->setIo($io);

        $builder = new SyliusBuilder();
        $builder->addResource(
            $manipulator,
            $configuration,
            [
                'type' => 'model',
                'value' => $classNameDetails->getFullName(),
            ]
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    public function manipulateSyliusRepository(
        Configuration $configuration,
        ClassNameDetails $classNameDetails,
        ConsoleStyle $io
    ): void {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path)
        );

        $manipulator->setIo($io);

        $builder = new SyliusBuilder();
        $builder->addResource(
            $manipulator,
            $configuration,
            [
                'type' => 'repository',
                'value' => $classNameDetails->getFullName(),
            ]
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function getFile(string $filename): string
    {
        return sprintf('%s/config/%s.php', $this->fileManager->getRootDirectory(), $filename);
    }
}
