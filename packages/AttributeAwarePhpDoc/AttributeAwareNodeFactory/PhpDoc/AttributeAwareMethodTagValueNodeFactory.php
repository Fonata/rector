<?php

declare(strict_types=1);

namespace Rector\AttributeAwarePhpDoc\AttributeAwareNodeFactory\PhpDoc;

use PHPStan\PhpDocParser\Ast\BaseNode;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use Rector\AttributeAwarePhpDoc\Ast\PhpDoc\AttributeAwareMethodTagValueNode;
use Rector\AttributeAwarePhpDoc\Contract\AttributeNodeAwareFactory\AttributeAwareNodeFactoryAwareInterface;
use Rector\AttributeAwarePhpDoc\Contract\AttributeNodeAwareFactory\AttributeNodeAwareFactoryInterface;
use Rector\BetterPhpDocParser\Attributes\Ast\AttributeAwareNodeFactory;

final class AttributeAwareMethodTagValueNodeFactory implements AttributeNodeAwareFactoryInterface, AttributeAwareNodeFactoryAwareInterface
{
    /**
     * @var AttributeAwareNodeFactory
     */
    private $attributeAwareNodeFactory;

    public function isMatch(Node $node): bool
    {
        return is_a($node, MethodTagValueNode::class, true);
    }

    /**
     * @param MethodTagValueNode $node
     */
    public function create(Node $node, string $docContent): AttributeAwareMethodTagValueNode
    {
        $returnType = $this->attributizeReturnType($node, $docContent);

        foreach ($node->parameters as $key => $parameter) {
            $node->parameters[$key] = $this->attributeAwareNodeFactory->createFromNode($parameter, $docContent);
        }

        return new AttributeAwareMethodTagValueNode(
            $node->isStatic,
            $returnType,
            $node->methodName,
            $node->parameters,
            $node->description
        );
    }

    public function setAttributeAwareNodeFactory(AttributeAwareNodeFactory $attributeAwareNodeFactory): void
    {
        $this->attributeAwareNodeFactory = $attributeAwareNodeFactory;
    }

    private function attributizeReturnType(MethodTagValueNode $methodTagValueNode, string $docContent): ?BaseNode
    {
        if ($methodTagValueNode->returnType !== null) {
            return $this->attributeAwareNodeFactory->createFromNode($methodTagValueNode->returnType, $docContent);
        }

        return null;
    }
}
