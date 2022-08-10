<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Webmozart\Assert\Assert;

abstract class ORMRepository
{
    protected ?ObjectManager $manager;

    public function __construct(ManagerRegistry $managerRegistry, protected string $class)
    {
        $this->manager = $managerRegistry->getManager();
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function getClass()
    {
        Assert::notNull($this->manager);

        // @phpstan-ignore-next-line
        return new ($this->manager->getClassMetadata($this->getClassName())->getName());
    }

    public function getClassName(): string
    {
        return $this->class;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        // @phpstan-ignore-next-line
        return $this->manager->createQueryBuilder();
    }

    public function getQuery(string $sql): Query
    {
        // @phpstan-ignore-next-line
        return $this->manager->createQuery($sql);
    }

    public function getNativeQuery(string $sql, ResultSetMapping $resultSetMapping): NativeQuery
    {
        // @phpstan-ignore-next-line
        return $this->manager->createNativeQuery($sql, $resultSetMapping);
    }

    public function getRsm(): Query\ResultSetMappingBuilder
    {
        // @phpstan-ignore-next-line
        return new Query\ResultSetMappingBuilder($this->manager);
    }
}
