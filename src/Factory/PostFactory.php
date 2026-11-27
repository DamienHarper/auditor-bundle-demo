<?php

namespace App\Factory;

use App\Entity\Post;
use App\Entity\PostStatus;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Post>
 */
final class PostFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Post::class;
    }

    protected function defaults(): array|callable
    {
        $faker = self::faker();

        return [
            'title'   => $faker->sentence(random_int(5, 10)),
            'body'    => implode("\n\n", $faker->paragraphs(random_int(3, 6))),
            'excerpt' => null,
            'status'  => PostStatus::Draft,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
