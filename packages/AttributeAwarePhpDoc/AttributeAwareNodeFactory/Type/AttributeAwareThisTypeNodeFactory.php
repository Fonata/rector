<?php

declare(strict_types=1);

namespace Rector\AttributeAwarePhpDoc\AttributeAwareNodeFactory\Type;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use Rector\AttributeAwarePhpDoc\Ast\Type\AttributeAwareThisTypeNode;
use Rector\AttributeAwarePhpDoc\Contract\AttributeNodeAwareFactory\AttributeNodeAwareFactoryInterface;

final class AttributeAwareThisTypeNodeFactory implements AttributeNodeAwareFactoryInterface
{
    public function getOriginalNodeClass(): string
    {
        return ThisTypeNode::class;
    }

    public function isMatch(Node $node): bool
    {
        return is_a($node, ThisTypeNode::class, true);
    }

    /**
     * @param ThisTypeNode $node
     */
    public function create(Node $node, string $docContent): AttributeAwareThisTypeNode
    {
        return new AttributeAwareThisTypeNode();
    }
}
