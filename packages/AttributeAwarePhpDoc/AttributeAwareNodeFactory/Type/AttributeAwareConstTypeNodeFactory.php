<?php

declare(strict_types=1);

namespace Rector\AttributeAwarePhpDoc\AttributeAwareNodeFactory\Type;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use Rector\AttributeAwarePhpDoc\Ast\Type\AttributeAwareConstTypeNode;
use Rector\AttributeAwarePhpDoc\Contract\AttributeNodeAwareFactory\AttributeNodeAwareFactoryInterface;

final class AttributeAwareConstTypeNodeFactory implements AttributeNodeAwareFactoryInterface
{
    public function isMatch(Node $node): bool
    {
        return is_a($node, ConstTypeNode::class, true);
    }

    /**
     * @param ConstTypeNode $node
     */
    public function create(Node $node, string $docContent): AttributeAwareConstTypeNode
    {
        return new AttributeAwareConstTypeNode($node->constExpr);
    }
}
