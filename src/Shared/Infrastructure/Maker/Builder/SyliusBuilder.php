<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Builder;

use App\Shared\Infrastructure\Maker\Configuration\NameInterface;
use App\Shared\Infrastructure\Maker\Configuration\PackageInterface;
use App\Shared\Infrastructure\Maker\Util\PhpFileManipulator;
use PhpParser\Node;
use Symfony\Bundle\MakerBundle\Str;
use Webmozart\Assert\Assert;

final class SyliusBuilder
{
    public function addResource(
        PhpFileManipulator $manipulator,
        NameInterface & PackageInterface $configuration,
        array $element,
    ): void {
        Assert::keyExists($element, 'type');
        Assert::keyExists($element, 'value');

        $package = Str::asTwigVariable($configuration->getPackage());
        $key = sprintf('%s.%s', str_replace('backend_', '', $package), Str::asTwigVariable($configuration->getName()));
        $nodes = $manipulator->findArrayItemNodes();

        $targetNodes = [];
        foreach ($nodes as $item) {
            if ($item->key instanceof Node\Scalar\String_ && null !== $item->key && $item->key->value === $key) {
                $targetNodes[] = $item->value->items[0];
            }
        }

        if ([] === $targetNodes) {
            foreach ($nodes as $item) {
                if ($item->key instanceof Node\Scalar\String_ && $item->key->value === 'resources') {
                    $classes = new Node\Expr\ArrayItem(
                        new Node\Expr\Array_([], [
                            'kind' => Node\Expr\Array_::KIND_SHORT,
                        ]),
                        new Node\Scalar\String_('classes'),
                    );

                    $item->value->items[] = new Node\Expr\ArrayItem(
                        new Node\Expr\Array_([$classes], [
                            'kind' => Node\Expr\Array_::KIND_SHORT,
                        ]),
                        new Node\Scalar\String_($key),
                    );
                }
            }

            $nodes = $manipulator->findArrayItemNodes();

            foreach ($nodes as $item) {
                if ($item->key instanceof Node\Scalar\String_ && $item->key->value === $key) {
                    $targetNodes[] = $item->value->items[0];
                }
            }
        }

        $present = false;
        foreach ($targetNodes as $item) {
            if ($item->key instanceof Node\Scalar\String_ && $item->key->value === 'classes') {
                foreach ($item->value->items as $value) {
                    if ($element['type'] !== $value->key->value) {
                        continue;
                    }

                    if ($element['type'] === $value->key->value) {
                        $present = true;
                    }
                }

                if ($present === false) {
                    $shortName = $manipulator->addUseStatementIfNecessary($element['value']);

                    $item->value->items[] = new Node\Expr\ArrayItem(
                        new Node\Expr\ConstFetch(new Node\Name(sprintf('%s::class', $shortName))),
                        new Node\Scalar\String_($element['type']),
                    );
                }
            }
        }

        $manipulator->updateSourceCodeFromNewStmts();
    }
}
