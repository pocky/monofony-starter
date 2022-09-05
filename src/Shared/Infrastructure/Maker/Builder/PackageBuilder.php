<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Builder;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use PhpParser\Node;
use Symfony\Bundle\MakerBundle\Str;
use Webmozart\Assert\Assert;

final class PackageBuilder
{
    public function addMappingPathToApiPlatform(
        PhpFileManipulator $manipulator,
        PackageInterface $configuration,
    ): void {
        $node = new Node\Scalar\String_(
            sprintf('%s/src/%s/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity', '%kernel.project_dir%', $configuration->getPackage()),
        );

        $manipulator->addValueToArrayItemNode('paths', $node);
        $manipulator->updateSourceCodeFromNewStmts();
    }

    public function addMappingPathToSylius(
        PhpFileManipulator $manipulator,
        PackageInterface $configuration,
    ): void {
        $node = new Node\Scalar\String_(
            sprintf('%s/src/%s/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity', '%kernel.project_dir%', $configuration->getPackage()),
        );

        $manipulator->addValueToArrayItemNode('paths', $node);
        $manipulator->updateSourceCodeFromNewStmts();
    }

    public function addMappingToDoctrine(
        PhpFileManipulator $manipulator,
        PackageInterface $configuration,
    ): void {
        $alias = new Node\Expr\ArrayItem(
            new Node\Scalar\String_($configuration->getPackage()),
            new Node\Scalar\String_('alias'),
        );

        $prefix = new Node\Expr\ArrayItem(
            new Node\Scalar\String_(sprintf('App\%s\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity', $configuration->getPackage())),
            new Node\Scalar\String_('prefix'),
        );

        $dir = new Node\Expr\ArrayItem(
            new Node\Scalar\String_(sprintf('%s/src/%s/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity', '%kernel.project_dir%', $configuration->getPackage())),
            new Node\Scalar\String_('dir'),
        );

        $type = new Node\Expr\ArrayItem(
            new Node\Scalar\String_('attribute'),
            new Node\Scalar\String_('type'),
        );

        $bundle = new Node\Expr\ArrayItem(
            new Node\Expr\ConstFetch(new Node\Name('false')),
            new Node\Scalar\String_('is_bundle'),
        );

        $node = new Node\Expr\ArrayItem(
            new Node\Expr\Array_([
                $bundle,
                $type,
                $dir,
                $prefix,
                $alias,
            ], ['kind' => Node\Expr\Array_::KIND_SHORT]),
            new Node\Scalar\String_($configuration->getPackage()),
        );

        $manipulator->addValueToArrayItemNode('mappings', $node);
        $manipulator->updateSourceCodeFromNewStmts();
    }

    public function createSyliusFactoryService(
        PhpFileManipulator $manipulator,
        PackageInterface & NameInterface $configuration,
        array $element,
    ): void {
        Assert::keyExists($element, 'factory');
        Assert::keyExists($element, 'entity');
        Assert::keyExists($element, 'generator');

        $serviceName = sprintf('%s.factory.%s', Str::asTwigVariable($configuration->getPackage()), Str::asTwigVariable($configuration->getName()));

        $serviceNodes = $manipulator->findExistingStringNodes($serviceName);
        if ([] !== $serviceNodes) {
            return;
        }

        $nodes = $manipulator->findClosureNodes();
        $factory = $manipulator->addUseStatementIfNecessary($element['factory']);
        $entity = $manipulator->addUseStatementIfNecessary($element['entity']);
        $generator = $manipulator->addUseStatementIfNecessary($element['generator']);

        $expression[] = new Node\Stmt\Expression(new Node\Expr\Variable(
            '__EXTRA__LINE',
        ));

        $expression[] = new Node\Stmt\Expression(new Node\Expr\MethodCall(
            new Node\Expr\MethodCall(
                new Node\Expr\Variable(new Node\Name('services')),
                new Node\Name('set'),
                [
                new Node\Scalar\String_($serviceName),
                new Node\Name(sprintf('%s::class', $factory)),
            ],
            ),
            new Node\Name('args'),
            [
                new Node\Expr\Array_([
                    new Node\Expr\ArrayItem(new Node\Expr\ConstFetch(new Node\Name(sprintf('%s::class', $entity)))),
                    new Node\Expr\ArrayItem(new Node\Expr\FuncCall(new Node\Name('service'), [
                        new Node\Arg(new Node\Expr\ConstFetch(new Node\Name(sprintf('%s::class', $generator)))),
                    ])),
                ], ['kind' => Node\Expr\Array_::KIND_SHORT]),
        ],
        ));

        array_push($nodes[0]->stmts, ...$expression);

        $manipulator->updateSourceCodeFromNewStmts();
    }
}
