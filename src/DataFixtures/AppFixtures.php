<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // START TRANSACTION #1

        // Authors
        $author1 = new Author();
        $author1
            ->setFullname('John')
            ->setEmail('john.doe@gmail.com')
        ;
        $manager->persist($author1);

        $author2 = new Author();
        $author2
            ->setFullname('Chuck Norris')
            ->setEmail('chuck.norris@gmail.com')
        ;
        $manager->persist($author2);

        $author3 = new Author();
        $author3
            ->setFullname('Luke Skywalker')
            ->setEmail('luke.skywalker@gmail.com')
        ;
        $manager->persist($author3);

        // Posts
        $post1 = new Post();
        $post1
            ->setTitle('First post')
            ->setBody('Here is the body of the first post')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($post1);

        $post2 = new Post();
        $post2
            ->setTitle('Second post')
            ->setBody('Here is another body -- second post')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($post2);

        $post3 = new Post();
        $post3
            ->setTitle('Third post')
            ->setBody('What a nice body for the third post')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($post3);

        $post4 = new Post();
        $post4
            ->setTitle('Fourth post')
            ->setBody('Body of the last, fourth post')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($post4);

        // Comments
        $comment1 = new Comment();
        $comment1
            ->setBody('First comment about post #1')
            ->setAuthor('Dark Vador')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2
            ->setBody('First comment about post #3')
            ->setAuthor('Yoshi')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3
            ->setBody('Second comment about post #3')
            ->setAuthor('Mario')
            ->setCreatedAt(new DateTime())
        ;
        $manager->persist($comment3);

        // Tags
        $tag1 = new Tag();
        $tag1->setTitle('techno');
        $manager->persist($tag1);

        $tag2 = new Tag();
        $tag2->setTitle('house');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setTitle('hardcore');
        $manager->persist($tag3);

        $tag4 = new Tag();
        $tag4->setTitle('jungle');
        $manager->persist($tag4);

        $tag5 = new Tag();
        $tag5->setTitle('gabber');
        $manager->persist($tag5);

        $manager->flush();

        // END TRANSACTION #1

        // START TRANSACTION #2

        // Updates
        $author1->setFullname('John Doe');
        $manager->persist($author1);
        $manager->flush();

        // END TRANSACTION #2

        // START TRANSACTION #3

        // Association author<->post
//        $author1
//            ->addPost($post1)
//            ->addPost($post2)
//        ;
//        $manager->persist($author1);
        $post1->setAuthor($author1);
        $post2->setAuthor($author1);
        $manager->persist($post1);

        $author2->addPost($post3);
        $manager->persist($author2);

        $post1->setCoauthor($author2);
        $manager->persist($post1);

        $post3->setCoauthor($author3);
        $manager->persist($post3);

        $author3->addPost($post4);
        $manager->persist($author3);

        // Association post<->comment
        $post1->addComment($comment1);
        $manager->persist($post1);

        $post3
            ->addComment($comment2)
            ->addComment($comment3)
        ;
        $manager->persist($post3);

        $manager->flush();
        // END TRANSACTION #3

        // START TRANSACTION #4

        // Association post<->tag
        $post1
            ->addTag($tag1)
            ->addTag($tag2)
        ;
        $post3
            ->addTag($tag1)
            ->addTag($tag3)
            ->addTag($tag5)
        ;
        $post4
            ->addTag($tag2)
            ->addTag($tag4)
            ->addTag($tag5)
        ;
        $manager->flush();
        // END TRANSACTION #4

        // START TRANSACTION #5

        // Dissociation post<->tag
        $post4
            ->removeTag($tag4)
            ->removeTag($tag5)
        ;

        // Dissociation author<->post
        $author3->removePost($post4);

        // Dissociation coauthor<->post
        $author3->removePost($post4);
        $post3->setCoauthor(null);

        // Delete author
        $manager->detach($author3);
        $author3 = $manager->find(Author::class, $author3->getId());
        $manager->remove($author3);

        $manager->flush();
        // END TRANSACTION #5
    }
}
