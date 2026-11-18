<?php

namespace App\Factory;

use App\Entity\Tag;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Tag>
 */
final class TagFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Tag::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name'  => self::faker()->unique()->slug(1),
            'color' => self::faker()->hexColor(),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
