<?php

declare(strict_types=1);

namespace Rector\PHPUnit\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Rector\Core\Rector\AbstractRector;
use Rector\PHPUnit\NodeAnalyzer\TestsNodeAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/symfony/symfony/pull/30813/files#r270879504
 * @see \Rector\Tests\PHPUnit\Rector\MethodCall\RemoveExpectAnyFromMockRector\RemoveExpectAnyFromMockRectorTest
 */
final class RemoveExpectAnyFromMockRector extends AbstractRector
{
    /**
     * @var TestsNodeAnalyzer
     */
    private $testsNodeAnalyzer;

    public function __construct(TestsNodeAnalyzer $testsNodeAnalyzer)
    {
        $this->testsNodeAnalyzer = $testsNodeAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove `expect($this->any())` from mocks as it has no added value',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

class SomeClass extends TestCase
{
    public function test()
    {
        $translator = $this->getMock('SomeClass');
        $translator->expects($this->any())
            ->method('trans')
            ->willReturn('translated max {{ max }}!');
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
use PHPUnit\Framework\TestCase;

class SomeClass extends TestCase
{
    public function test()
    {
        $translator = $this->getMock('SomeClass');
        $translator->method('trans')
            ->willReturn('translated max {{ max }}!');
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
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $this->testsNodeAnalyzer->isInTestClass($node)) {
            return null;
        }

        if (! $this->isName($node->name, 'expects')) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $onlyArgument = $node->args[0]->value;
        if (! $onlyArgument instanceof MethodCall) {
            return null;
        }

        if (! $this->isName($onlyArgument->var, 'this')) {
            return null;
        }

        if (! $this->isName($onlyArgument->name, 'any')) {
            return null;
        }

        return $node->var;
    }
}
