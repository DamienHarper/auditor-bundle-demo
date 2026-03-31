<?php

namespace App\Factory;

use App\Entity\Comment;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Comment>
 */
final class CommentFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Comment::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'body'       => self::faker()->paragraph(random_int(2, 4)),
            'authorName' => self::faker()->name(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
