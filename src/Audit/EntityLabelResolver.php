<?php

namespace App\Audit;

use App\Entity\Comment;
use DH\Auditor\Contract\EntityLabelResolverInterface;

/**
 * entity_label_resolver example: the label of an entity written in the "source" and "target" blocks of the diffs.
 *
 * A comment has no title, and its __toString() holds its id, still unknown when it is inserted: the history
 * would read "created Comment “Comment #? by Jane”". Returning null keeps the default label (__toString()).
 */
final class EntityLabelResolver implements EntityLabelResolverInterface
{
    public function __invoke(object $entity): ?string
    {
        if ($entity instanceof Comment) {
            return \sprintf('%s’s comment', $entity->getAuthorName() ?? 'Anonymous');
        }

        return null;
    }
}
