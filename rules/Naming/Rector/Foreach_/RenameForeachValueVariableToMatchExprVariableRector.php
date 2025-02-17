<?php

declare(strict_types=1);

namespace Rector\Naming\Rector\Foreach_;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Foreach_;
use PHPStan\Type\ThisType;
use Rector\Core\Rector\AbstractRector;
use Rector\Naming\ExpectedNameResolver\InflectorSingularResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Tests\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchExprVariableRector\RenameForeachValueVariableToMatchExprVariableRectorTest
 */
final class RenameForeachValueVariableToMatchExprVariableRector extends AbstractRector
{
    /**
     * @var InflectorSingularResolver
     */
    private $inflectorSingularResolver;

    public function __construct(InflectorSingularResolver $inflectorSingularResolver)
    {
        $this->inflectorSingularResolver = $inflectorSingularResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Renames value variable name in foreach loop to match expression variable', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
public function run()
{
    $array = [];
    foreach ($variables as $foo) {
        $array[] = $property;
    }
}
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
public function run()
{
    $array = [];
    foreach ($variables as $variable) {
        $array[] = $variable;
    }
}
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Foreach_::class];
    }

    /**
     * @param Foreach_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node->expr instanceof Variable && ! $node->expr instanceof PropertyFetch) {
            return null;
        }

        if ($this->isNotThisTypePropertyFetch($node->expr)) {
            return null;
        }

        $exprName = $this->getName($node->expr);
        if ($exprName === null) {
            return null;
        }
        if ($node->keyVar instanceof Node) {
            return null;
        }

        $valueVarName = $this->getName($node->valueVar);
        if ($valueVarName === null) {
            return null;
        }

        $singularValueVarName = $this->inflectorSingularResolver->resolve($exprName);
        if ($this->shouldSkip($valueVarName, $singularValueVarName, $node)) {
            return null;
        }

        return $this->processRename($node, $valueVarName, $singularValueVarName);
    }

    private function isNotThisTypePropertyFetch(Expr $expr): bool
    {
        if ($expr instanceof PropertyFetch) {
            $variableType = $this->getStaticType($expr->var);
            return ! $variableType instanceof ThisType;
        }

        return false;
    }

    private function processRename(Foreach_ $foreach, string $valueVarName, string $singularValueVarName): Foreach_
    {
        $foreach->valueVar = new Variable($singularValueVarName);
        $this->traverseNodesWithCallable($foreach->stmts, function (Node $node) use (
            $singularValueVarName,
            $valueVarName
        ): ?Variable {
            if (! $node instanceof Variable) {
                return null;
            }

            if (! $this->isName($node, $valueVarName)) {
                return null;
            }
            return new Variable($singularValueVarName);
        });

        return $foreach;
    }

    private function shouldSkip(string $valueVarName, string $singularValueVarName, Foreach_ $foreach): bool
    {
        if ($singularValueVarName === $valueVarName) {
            return true;
        }

        $isUsedInStmts = (bool) $this->betterNodeFinder->findFirst($foreach->stmts, function (Node $node) use (
            $singularValueVarName
        ): bool {
            if (! $node instanceof Variable) {
                return false;
            }

            return $this->isName($node, $singularValueVarName);
        });

        if ($isUsedInStmts) {
            return true;
        }

        return (bool) $this->betterNodeFinder->findFirstNext($foreach, function (Node $node) use (
            $singularValueVarName
        ): bool {
            if (! $node instanceof Variable) {
                return false;
            }

            return $this->isName($node, $singularValueVarName);
        });
    }
}
