<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Maker\Util;

use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\PrettyPrinter;
use PhpParser\Builder;

final class PhpFileManipulator
{
    private Parser\Php7 $parser;
    private Lexer\Emulative $lexer;
    private PrettyPrinter $printer;
    private ?ConsoleStyle $io = null;

    private ?array $oldStmts = null;
    private array $oldTokens = [];
    private array $newStmts = [];

    private array $pendingComments = [];

    public function __construct(
        private string $sourceCode,
    ) {
        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine', 'endLine',
                'startTokenPos', 'endTokenPos',
            ],
        ]);
        $this->parser = new Parser\Php7($this->lexer);
        $this->printer = new PrettyPrinter();

        $this->setSourceCode($sourceCode);
    }

    public function setIo(ConsoleStyle $io): void
    {
        $this->io = $io;
    }

    public function getSourceCode(): string
    {
        return $this->sourceCode;
    }

    public function findArrayItemNodes(): array
    {
        return $this->findAllNodes(function ($node) {
            return $node instanceof Node\Expr\ArrayItem;
        });
    }

    public function findClosureNodes(): array
    {
        return $this->findAllNodes(function($node) {
            return $node instanceof Node\Expr\Closure;
        });
    }

    public function findExistingStringNodes(string $name): array
    {
        return $this->findAllNodes(function($node) use ($name) {
            return $node instanceof Node\Scalar\String_ && $node->value === $name;
        });
    }

    public function updateSourceCodeFromNewStmts(): void
    {
        $newCode = $this->printer->printFormatPreserving(
            $this->newStmts,
            $this->oldStmts,
            $this->oldTokens
        );

        // replace the 3 "fake" items that may be in the code (allowing for different indentation)
        $newCode = preg_replace('/(\ |\t)*private\ \$__EXTRA__LINE;/', '', $newCode);
        $newCode = preg_replace('/use __EXTRA__LINE;/', '', $newCode);
        $newCode = preg_replace('/(\ |\t)*\$__EXTRA__LINE;/', '', $newCode);

        // process comment lines
        foreach ($this->pendingComments as $i => $comment) {
            // sanity check
            $placeholder = sprintf('$__COMMENT__VAR_%d;', $i);
            if (!str_contains($newCode, $placeholder)) {
                // this can happen if a comment is createSingleLineCommentNode()
                // is called, but then that generated code is ultimately not added
                continue;
            }

            $newCode = str_replace($placeholder, '// '.$comment, $newCode);
        }
        $this->pendingComments = [];

        $this->setSourceCode($newCode);
    }

    private function setSourceCode(string $sourceCode): void
    {
        $this->sourceCode = $sourceCode;
        $this->oldStmts = $this->parser->parse($sourceCode);
        $this->oldTokens = $this->lexer->getTokens();

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\CloningVisitor());
        $traverser->addVisitor(new NodeVisitor\NameResolver(null, [
            'replaceNodes' => false,
        ]));
        $this->newStmts = $traverser->traverse($this->oldStmts);
    }

    /**
     * @return Node[]
     */
    private function findAllNodes(callable $filterCallback): array
    {
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNodes();
    }

    public function addValueToArrayItemNode(string $key, Node\Expr $expr): void
    {
        $nodes = $this->findArrayItemNodes();

        foreach ($nodes as $item) {
            if ($item->key instanceof Node\Scalar\String_ && $item->key->value === $key) {
                $item->value->items[] = $expr;
            }
        }
    }

    /**
     * @return string The alias to use when referencing this class
     */
    public function addUseStatementIfNecessary(string $class): string
    {
        $shortClassName = Str::getShortClassName($class);
        $namespaceNode = $this->getNamespaceNode();

        $targetIndex = null;

        foreach ($namespaceNode->uses as $index => $use) {
            if ($use instanceof Node\Stmt\UseUse) {
                $alias = $use->alias ? $use->alias->name : $use->name->getLast();

                // the use statement already exists? Don't add it again
                if ($class === (string) $use->name) {
                    return $alias;
                }

                if ($alias === $shortClassName) {
                    // we have a conflicting alias!
                    // to be safe, use the fully-qualified class name
                    // everywhere and do not add another use statement
                    return '\\'.$class;
                }

                $targetIndex = $index;
            }
        }

        if (null === $targetIndex) {
            throw new \Exception('Could not find a class!');
        }

        $use = new Node\Stmt\UseUse(new Node\Name($class));
        $namespaceNode->uses[] = $use;

        return $shortClassName;
    }

    private function getNamespaceNode()
    {
        $nodes = $this->findFirstNode(function ($node) {
            return $node instanceof Node\Stmt\Use_;
        });

        if (!$nodes) {
            throw new \Exception('Could not find namespace node');
        }

        return $nodes;
    }

    private function findFirstNode(callable $filterCallback): ?Node
    {
        $traverser = new NodeTraverser();
        $visitor = new NodeVisitor\FirstFindingVisitor($filterCallback);
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->newStmts);

        return $visitor->getFoundNode();
    }

}
