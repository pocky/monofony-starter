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

namespace App\Shared\Infrastructure\Maker\Command\Doctrine\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Shared\Infrastructure\Maker\Builder\SyliusBuilder;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use Doctrine\DBAL\Types\Types;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Annotation\SyliusCrudRoutes;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;

final readonly class SyliusEntityClassGenerator
{
    public function __construct(
        private Generator $generator,
        private DoctrineHelper $doctrineHelper,
        private FileManager $fileManager,
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
            [
                'Doctrine\\ORM\\Mapping' => 'ORM',
            ],
        ]);

        if ($apiResource) {
            if (!class_exists(ApiResource::class)) {
                throw new \RuntimeException('You must install api-platform/core to use the API Platform integration.');
            }

            $useStatements->addUseStatement('ApiPlatform\Metadata');
        }

        if ($syliusCrud) {
            $useStatements->addUseStatement(SyliusCrudRoutes::class);
            $useStatements->addUseStatement(ResourceInterface::class);
        }

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            sprintf('%s/../../../Resources/skeleton/infrastructure/doctrine/Entity.tpl.php', __DIR__),
            [
                'use_statements' => $useStatements,
                'repository_class_name' => $repoClassDetails->getShortName(),
                'api_resource' => $apiResource,
                'sylius_crud' => $syliusCrud,
                'table_name' => sprintf('%s_%s', $package, $tableName),
                'crud_route_package' => $package,
                'crud_route_alias' => sprintf('app.%s_%s', $package, $tableName),
                'crud_route_path' => sprintf('%s%s', $package, Str::asRoutePath($entityClassDetails->getRelativeName())),
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
            sprintf('%s/../../../Resources/skeleton/infrastructure/doctrine/Repository.tpl.php', __DIR__),
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
    ): void {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $builder = new SyliusBuilder();
        $builder->addResource(
            $manipulator,
            $configuration,
            [
                'type' => 'model',
                'value' => $classNameDetails->getFullName(),
            ],
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    public function manipulateSyliusRepository(
        Configuration $configuration,
        ClassNameDetails $classNameDetails,
    ): void {
        $path = $this->getFile('sylius/resources');
        $manipulator = new PhpFileManipulator(
            $this->fileManager->getFileContents($path),
        );

        $builder = new SyliusBuilder();
        $builder->addResource(
            $manipulator,
            $configuration,
            [
                'type' => 'repository',
                'value' => $classNameDetails->getFullName(),
            ],
        );

        $this->fileManager->dumpFile($path, $manipulator->getSourceCode());
    }

    private function getFile(string $filename): string
    {
        return sprintf('%s/config/%s.php', $this->fileManager->getRootDirectory(), $filename);
    }
}
