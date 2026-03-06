<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        // ====================================================================
        // LOT 1 — Création initiale
        // Users, Authors, Tags, Posts (sans author ni tags)
        // ====================================================================

        // Users
        $admin = new User();
        $admin->setEmail('admin@blog.com')
              ->setName('Admin User')
              ->setRoles(['ROLE_ADMIN'])
              ->setPassword($this->hasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        $editor = new User();
        $editor->setEmail('editor@blog.com')
               ->setName('Editor User')
               ->setRoles(['ROLE_EDITOR'])
               ->setPassword($this->hasher->hashPassword($editor, 'password'));
        $manager->persist($editor);

        // Authors
        $alice = new Author();
        $alice->setName('Alice Martin')->setEmail('alice@example.com');
        $manager->persist($alice);

        $bob = new Author();
        $bob->setName('Bob Dupont')->setEmail('bob@example.com');
        $manager->persist($bob);

        $carol = new Author();
        $carol->setName('Carol Smith')->setEmail('carol@example.com');
        $manager->persist($carol);

        // Tags
        $tagPhp = new Tag();
        $tagPhp->setName('php')->setColor('#8b5cf6');
        $manager->persist($tagPhp);

        $tagSymfony = new Tag();
        $tagSymfony->setName('symfony')->setColor('#3b82f6');
        $manager->persist($tagSymfony);

        $tagDoctrine = new Tag();
        $tagDoctrine->setName('doctrine')->setColor('#10b981');
        $manager->persist($tagDoctrine);

        $tagAuditing = new Tag();
        $tagAuditing->setName('auditing')->setColor('#f59e0b');
        $manager->persist($tagAuditing);

        $tagBestPractices = new Tag();
        $tagBestPractices->setName('best-practices')->setColor('#ef4444');
        $manager->persist($tagBestPractices);

        // Posts (no author, no tags, draft)
        $post1 = new Post();
        $post1->setTitle('Getting Started with auditor-bundle v7')
              ->setBody('The auditor-bundle v7 is a major release that brings full Symfony 8 support. It provides seamless audit trail capabilities for your Doctrine entities with zero configuration overhead. In this post, we explore the core features including insert, update, remove, associate and dissociate events, all captured automatically.')
              ->setStatus('draft');
        $manager->persist($post1);

        $post2 = new Post();
        $post2->setTitle('Doctrine ORM 3.x: What\'s New')
              ->setBody('Doctrine ORM 3.x brings significant improvements to the ORM layer including better type handling, improved query builder API, and enhanced performance for large datasets. Combined with auditor-bundle, you get full audit trails on all entity changes with minimal overhead.')
              ->setStatus('draft');
        $manager->persist($post2);

        $post3 = new Post();
        $post3->setTitle('Best Practices for Audit Trails in Symfony')
              ->setBody('Building robust audit trails in Symfony applications requires careful thought about what to track, how to store it, and how to query it efficiently. The auditor-bundle takes care of most of this complexity, leaving you to focus on your business logic.')
              ->setStatus('draft');
        $manager->persist($post3);

        $manager->flush(); // LOT 1: insert ×13

        // ====================================================================
        // LOT 2 — Associations author ↔ posts
        // ====================================================================

        $post1->setAuthor($alice)->setCoauthor($bob);
        $post2->setAuthor($bob);
        $post3->setAuthor($carol);

        $manager->flush(); // LOT 2: update ×3, associate ×4

        // ====================================================================
        // LOT 3 — Tags ManyToMany
        // ====================================================================

        $post1->addTag($tagPhp)->addTag($tagSymfony)->addTag($tagDoctrine);
        $post2->addTag($tagDoctrine)->addTag($tagAuditing);
        $post3->addTag($tagSymfony)->addTag($tagBestPractices);

        $manager->flush(); // LOT 3: associate ×7

        // ====================================================================
        // LOT 4 — Commentaires + publication
        // ====================================================================

        $commentToDelete = null;
        $comments = [
            ['post' => $post1, 'author' => 'John Doe', 'body' => 'Great introduction! The auditor-bundle really is a game-changer for tracking changes.', 'markForDelete' => true],
            ['post' => $post1, 'author' => 'Jane Smith', 'body' => 'I have been using v6, looking forward to migrating to v7!'],
            ['post' => $post1, 'author' => 'Pierre Martin', 'body' => 'The Symfony 8 support is fantastic. Any ETA on the docs update?'],
            ['post' => $post1, 'author' => 'Marie Curie', 'body' => 'Excellent write-up. The examples are very clear.'],
            ['post' => $post2, 'author' => 'Alan Turing', 'body' => 'Doctrine ORM 3.x is a huge step forward. Thanks for covering this.'],
            ['post' => $post2, 'author' => 'Ada Lovelace', 'body' => 'The type handling improvements alone make it worth upgrading.'],
            ['post' => $post2, 'author' => 'John Doe', 'body' => 'Good comparison with the previous version.'],
            ['post' => $post3, 'author' => 'Bob Builder', 'body' => 'Very practical advice. I will apply this to my project right away.'],
            ['post' => $post3, 'author' => 'Alice Wonder', 'body' => 'The section on what to track is especially useful.'],
        ];

        foreach ($comments as $commentData) {
            $comment = new Comment();
            $comment->setPost($commentData['post'])
                    ->setAuthorName($commentData['author'])
                    ->setBody($commentData['body']);
            $manager->persist($comment);
            if (!empty($commentData['markForDelete'])) {
                $commentToDelete = $comment;
            }
        }

        $post1->setStatus('published');
        $post2->setStatus('published');

        $manager->flush(); // LOT 4: insert ×9, update ×2

        // ====================================================================
        // LOT 5 — Modifications (update audit)
        // ====================================================================

        $alice->setBio('Alice is a senior PHP developer and Symfony expert with over 10 years of experience building enterprise web applications. She is passionate about clean code, DDD, and developer experience.');

        $post1->setTitle('Getting Started with auditor-bundle v7 — Complete Guide')
              ->setExcerpt('A comprehensive introduction to auditor-bundle v7 with Symfony 8: automatic audit trails for all your Doctrine entities.');

        $post2->setCoauthor($carol);

        $manager->flush(); // LOT 5: update ×3

        // ====================================================================
        // LOT 6 — Dissociation + suppression
        // ====================================================================

        $post3->removeTag($tagBestPractices);
        $post3->setAuthor($alice);

        // Remove a comment from post1 (demonstrating the remove audit event)
        if ($commentToDelete instanceof Comment) {
            $manager->remove($commentToDelete);
        }

        $manager->flush(); // LOT 6: dissociate ×1, update ×1, remove ×1
    }
}
