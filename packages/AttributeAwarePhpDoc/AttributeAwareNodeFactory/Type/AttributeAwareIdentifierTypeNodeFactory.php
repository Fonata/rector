<?php

declare(strict_types=1);

namespace Rector\AttributeAwarePhpDoc\AttributeAwareNodeFactory\Type;

use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Rector\AttributeAwarePhpDoc\Ast\Type\AttributeAwareIdentifierTypeNode;
use Rector\AttributeAwarePhpDoc\Contract\AttributeNodeAwareFactory\AttributeNodeAwareFactoryInterface;

final class AttributeAwareIdentifierTypeNodeFactory implements AttributeNodeAwareFactoryInterface
{
    public function isMatch(Node $node): bool
    {
        return is_a($node, IdentifierTypeNode::class, true);
    }

    /**
     * @param IdentifierTypeNode $node
     */
    public function create(Node $node, string $docContent): AttributeAwareIdentifierTypeNode
    {
        return new AttributeAwareIdentifierTypeNode($node->name);
    }
}
