<?php

declare(strict_types=1);

namespace Rector\Symfony3\Rector\MethodCall;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\ValueObject\MethodName;
use Rector\Symfony3\NodeAnalyzer\FormAddMethodCallAnalyzer;
use Rector\Symfony3\NodeAnalyzer\FormCollectionAnalyzer;
use Rector\Symfony3\NodeAnalyzer\FormOptionsArrayMatcher;
use Rector\Symfony3\NodeFactory\BuilderFormNodeFactory;
use Rector\Symfony3\NodeFactory\ConfigureOptionsNodeFactory;
use ReflectionMethod;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Best resource with clear example:
 * @see https://stackoverflow.com/questions/34027711/passing-data-to-buildform-in-symfony-2-8-3-0
 *
 * @see https://github.com/symfony/symfony/commit/adf20c86fb0d8dc2859aa0d2821fe339d3551347
 * @see http://www.keganv.com/passing-arguments-controller-file-type-symfony-3/
 * @see https://github.com/symfony/symfony/blob/2.8/UPGRADE-2.8.md#form
 *
 * @see \Rector\Tests\Symfony3\Rector\MethodCall\FormTypeInstanceToClassConstRector\FormTypeInstanceToClassConstRectorTest
 */
final class FormTypeInstanceToClassConstRector extends AbstractRector
{
    /**
     * @var ObjectType[]
     */
    private $controllerObjectTypes = [];

    /**
     * @var BuilderFormNodeFactory
     */
    private $builderFormNodeFactory;

    /**
     * @var ConfigureOptionsNodeFactory
     */
    private $configureOptionsNodeFactory;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var FormAddMethodCallAnalyzer
     */
    private $formAddMethodCallAnalyzer;

    /**
     * @var FormOptionsArrayMatcher
     */
    private $formOptionsArrayMatcher;

    /**
     * @var FormCollectionAnalyzer
     */
    private $formCollectionAnalyzer;

    public function __construct(
        BuilderFormNodeFactory $builderFormNodeFactory,
        ConfigureOptionsNodeFactory $configureOptionsNodeFactory,
        ReflectionProvider $reflectionProvider,
        FormAddMethodCallAnalyzer $formAddMethodCallAnalyzer,
        FormOptionsArrayMatcher $formOptionsArrayMatcher,
        FormCollectionAnalyzer $formCollectionAnalyzer
    ) {
        $this->builderFormNodeFactory = $builderFormNodeFactory;
        $this->configureOptionsNodeFactory = $configureOptionsNodeFactory;
        $this->reflectionProvider = $reflectionProvider;
        $this->formAddMethodCallAnalyzer = $formAddMethodCallAnalyzer;
        $this->formOptionsArrayMatcher = $formOptionsArrayMatcher;

        $this->controllerObjectTypes = [
            new ObjectType('Symfony\Bundle\FrameworkBundle\Controller\Controller'),
            new ObjectType('Symfony\Bundle\FrameworkBundle\Controller\AbstractController'),
        ];
        $this->formCollectionAnalyzer = $formCollectionAnalyzer;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Changes createForm(new FormType), add(new FormType) to ones with "FormType::class"',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
class SomeController
{
    public function action()
    {
        $form = $this->createForm(new TeamType, $entity);
    }
}
CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
class SomeController
{
    public function action()
    {
        $form = $this->createForm(TeamType::class, $entity);
    }
}
CODE_SAMPLE
                ),
            ]
        );
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
        if ($this->nodeTypeResolver->isObjectTypes($node->var, $this->controllerObjectTypes) && $this->isName(
            $node->name,
            'createForm'
        )) {
            return $this->processNewInstance($node, 0, 2);
        }

        if (! $this->formAddMethodCallAnalyzer->matches($node)) {
            return null;
        }

        // special case for collections
        if ($this->formCollectionAnalyzer->isCollectionType($node)) {
            $this->refactorCollectionOptions($node);
        }

        return $this->processNewInstance($node, 1, 2);
    }

    private function processNewInstance(MethodCall $methodCall, int $position, int $optionsPosition): ?Node
    {
        if (! isset($methodCall->args[$position])) {
            return null;
        }

        $argValue = $methodCall->args[$position]->value;
        if (! $argValue instanceof New_) {
            return null;
        }

        // we can only process direct name
        if (! $argValue->class instanceof Name) {
            return null;
        }

        if ($argValue->args !== []) {
            $methodCall = $this->moveArgumentsToOptions(
                $methodCall,
                $position,
                $optionsPosition,
                $argValue->class->toString(),
                $argValue->args
            );

            if (! $methodCall instanceof MethodCall) {
                return null;
            }
        }

        $methodCall->args[$position]->value = $this->nodeFactory->createClassConstReference(
            $argValue->class->toString()
        );

        return $methodCall;
    }

    private function refactorCollectionOptions(MethodCall $methodCall): void
    {
        $optionsArray = $this->formOptionsArrayMatcher->match($methodCall);
        if (! $optionsArray instanceof Array_) {
            return;
        }

        foreach ($optionsArray->items as $arrayItem) {
            if ($arrayItem === null) {
                continue;
            }

            if ($arrayItem->key === null) {
                continue;
            }

            if (! $this->valueResolver->isValues($arrayItem->key, ['entry', 'entry_type'])) {
                continue;
            }

            if (! $arrayItem->value instanceof New_) {
                continue;
            }

            $newClass = $arrayItem->value->class;

            if (! $newClass instanceof Name) {
                continue;
            }

            $arrayItem->value = $this->nodeFactory->createClassConstReference($newClass->toString());
        }
    }

    /**
     * @param Arg[] $argNodes
     */
    private function moveArgumentsToOptions(
        MethodCall $methodCall,
        int $position,
        int $optionsPosition,
        string $className,
        array $argNodes
    ): ?MethodCall {
        $namesToArgs = $this->resolveNamesToArgs($className, $argNodes);

        // set default data in between
        if ($position + 1 !== $optionsPosition && ! isset($methodCall->args[$position + 1])) {
            $methodCall->args[$position + 1] = new Arg($this->nodeFactory->createNull());
        }

        // @todo decopule and name, so I know what it is
        if (! isset($methodCall->args[$optionsPosition])) {
            $array = new Array_();
            foreach ($namesToArgs as $name => $arg) {
                $array->items[] = new ArrayItem($arg->value, new String_($name));
            }

            $methodCall->args[$optionsPosition] = new Arg($array);
        }

        $formTypeClass = $this->nodeRepository->findClass($className);
        if (! $formTypeClass instanceof Class_) {
            return null;
        }

        $constructorClassMethod = $formTypeClass->getMethod(MethodName::CONSTRUCT);

        // nothing we can do, out of scope
        if (! $constructorClassMethod instanceof ClassMethod) {
            return null;
        }

        $this->addBuildFormMethod($formTypeClass, $constructorClassMethod);
        $this->addConfigureOptionsMethod($formTypeClass, $namesToArgs);

        // remove ctor
        $this->removeNode($constructorClassMethod);

        return $methodCall;
    }

    /**
     * @param Arg[] $argNodes
     * @return Arg[]
     */
    private function resolveNamesToArgs(string $className, array $argNodes): array
    {
        if (! $this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $reflectionClass = $classReflection->getNativeReflection();

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if (! $constructorReflectionMethod instanceof ReflectionMethod) {
            return [];
        }

        $namesToArgs = [];
        foreach ($constructorReflectionMethod->getParameters() as $position => $reflectionParameter) {
            $namesToArgs[$reflectionParameter->getName()] = $argNodes[$position];
        }

        return $namesToArgs;
    }

    private function addBuildFormMethod(Class_ $class, ClassMethod $classMethod): void
    {
        $buildFormClassMethod = $class->getMethod('buildForm');
        if ($buildFormClassMethod !== null) {
            return;
        }

        $class->stmts[] = $this->builderFormNodeFactory->create($classMethod);
    }

    /**
     * @param Arg[] $namesToArgs
     */
    private function addConfigureOptionsMethod(Class_ $class, array $namesToArgs): void
    {
        $configureOptionsClassMethod = $class->getMethod('configureOptions');
        if ($configureOptionsClassMethod !== null) {
            return;
        }

        $class->stmts[] = $this->configureOptionsNodeFactory->create($namesToArgs);
    }
}
