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
use function Zenstruck\Foundry\faker;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $faker = faker();

        // ====================================================================
        // LOT 1 -- Utilisateurs (3) + Auteurs (15) + Tags (15)  ~33 inserts
        // ====================================================================

        foreach ([
            ['admin@blog.com',  'Admin User',  ['ROLE_ADMIN']],
            ['editor@blog.com', 'Editor User', ['ROLE_EDITOR']],
            ['viewer@blog.com', 'Viewer User', ['ROLE_USER']],
        ] as [$email, $name, $roles]) {
            $user = new User();
            $user->setEmail($email)->setName($name)->setRoles($roles)
                 ->setPassword($this->hasher->hashPassword($user, 'password'));
            $manager->persist($user);
        }

        $authors = [];
        foreach ([
            ['Alice Martin',   'alice.martin@authors.dev'],
            ['Bob Dupont',     'bob.dupont@authors.dev'],
            ['Carol Smith',    'carol.smith@authors.dev'],
            ['David Chen',     'david.chen@authors.dev'],
            ['Emma Wilson',    'emma.wilson@authors.dev'],
            ['Grace Kim',      'grace.kim@authors.dev'],
            ['Henri Morel',    'henri.morel@authors.dev'],
            ['Isabelle Blanc', 'isabelle.blanc@authors.dev'],
            ['James Okonkwo',  'james.okonkwo@authors.dev'],
            ['Karin Muller',   'karin.muller@authors.dev'],
            ['Luca Rossi',     'luca.rossi@authors.dev'],
            ['Maria Santos',   'maria.santos@authors.dev'],
            ['Nathan Cole',    'nathan.cole@authors.dev'],
            ['Olivia Pham',    'olivia.pham@authors.dev'],
            ['Paul Lemaire',   'paul.lemaire@authors.dev'],
        ] as [$name, $email]) {
            $author = new Author();
            $author->setName($name)->setEmail($email);
            $manager->persist($author);
            $authors[] = $author;
        }

        $tags = [];
        foreach ([
            ['php',            '#8b5cf6'],
            ['symfony',        '#3b82f6'],
            ['doctrine',       '#10b981'],
            ['auditor',        '#f59e0b'],
            ['best-practices', '#ef4444'],
            ['security',       '#dc2626'],
            ['performance',    '#16a34a'],
            ['testing',        '#9333ea'],
            ['api-design',     '#0891b2'],
            ['ddd',            '#b45309'],
            ['architecture',   '#1d4ed8'],
            ['devops',         '#475569'],
            ['javascript',     '#ca8a04'],
            ['database',       '#0f766e'],
            ['clean-code',     '#be185d'],
        ] as [$name, $color]) {
            $tag = new Tag();
            $tag->setName($name)->setColor($color);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        $manager->flush(); // LOT 1 : 33 inserts

        // ====================================================================
        // LOT 2 -- Posts batch A : 30 brouillons (sans auteur, sans tags)
        // ====================================================================

        $posts = [];
        for ($i = 0; $i < 30; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(random_int(5, 9)))
                 ->setBody(implode("\n\n", $faker->paragraphs(random_int(3, 6))))
                 ->setStatus('draft');
            $manager->persist($post);
            $posts[] = $post;
        }

        $manager->flush(); // LOT 2 : 30 inserts

        // ====================================================================
        // LOT 3 -- Posts batch B : 30 brouillons supplementaires
        // ====================================================================

        for ($i = 0; $i < 30; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(random_int(5, 9)))
                 ->setBody(implode("\n\n", $faker->paragraphs(random_int(3, 6))))
                 ->setStatus('draft');
            $manager->persist($post);
            $posts[] = $post;
        }

        $manager->flush(); // LOT 3 : 30 inserts

        // ====================================================================
        // LOT 4 -- Auteur principal sur les 60 posts (60 updates + 60 associate)
        // ====================================================================

        $authorCount = count($authors);
        foreach ($posts as $index => $post) {
            $post->setAuthor($authors[$index % $authorCount]);
        }

        $manager->flush(); // LOT 4 : 60 updates + 60 associate

        // ====================================================================
        // LOT 5 -- Tags ManyToMany sur posts[0..29]  (2-4 tags par post)
        // ====================================================================

        foreach (array_slice($posts, 0, 30) as $post) {
            $num  = random_int(2, 4);
            $keys = (array) array_rand($tags, $num);
            foreach ($keys as $k) {
                $post->addTag($tags[$k]);
            }
        }

        $manager->flush(); // LOT 5 : ~75-120 associate

        // ====================================================================
        // LOT 6 -- Tags ManyToMany sur posts[30..59]  (2-4 tags par post)
        // ====================================================================

        foreach (array_slice($posts, 30, 30) as $post) {
            $num  = random_int(2, 4);
            $keys = (array) array_rand($tags, $num);
            foreach ($keys as $k) {
                $post->addTag($tags[$k]);
            }
        }

        $manager->flush(); // LOT 6 : ~75-120 associate

        // ====================================================================
        // LOT 7 -- Commentaires sur posts[0..19]  (4-6 par post, ~100 inserts)
        // ====================================================================

        $commentsToDelete = [];
        foreach (array_slice($posts, 0, 20) as $idx => $post) {
            for ($c = 0; $c < random_int(4, 6); $c++) {
                $comment = new Comment();
                $comment->setPost($post)
                        ->setAuthorName($faker->name())
                        ->setBody($faker->paragraph(random_int(2, 4)));
                $manager->persist($comment);
                if ($c === 0 && $idx % 5 === 0) {
                    $commentsToDelete[] = $comment;
                }
            }
        }

        $manager->flush(); // LOT 7 : ~100 inserts

        // ====================================================================
        // LOT 8 -- Commentaires sur posts[20..39]  (4-6 par post, ~100 inserts)
        // ====================================================================

        foreach (array_slice($posts, 20, 20) as $post) {
            for ($c = 0; $c < random_int(4, 6); $c++) {
                $comment = new Comment();
                $comment->setPost($post)
                        ->setAuthorName($faker->name())
                        ->setBody($faker->paragraph(random_int(2, 4)));
                $manager->persist($comment);
            }
        }

        $manager->flush(); // LOT 8 : ~100 inserts

        // ====================================================================
        // LOT 9 -- Commentaires sur posts[40..59]  (4-6 par post, ~100 inserts)
        // ====================================================================

        foreach (array_slice($posts, 40, 20) as $post) {
            for ($c = 0; $c < random_int(4, 6); $c++) {
                $comment = new Comment();
                $comment->setPost($post)
                        ->setAuthorName($faker->name())
                        ->setBody($faker->paragraph(random_int(2, 4)));
                $manager->persist($comment);
            }
        }

        $manager->flush(); // LOT 9 : ~100 inserts

        // ====================================================================
        // LOT 10 -- Publication de posts[0..39] + ajout d'extraits (40 updates)
        // ====================================================================

        foreach (array_slice($posts, 0, 40) as $post) {
            $post->setStatus('published')
                 ->setExcerpt($faker->sentence(random_int(15, 25)));
        }

        $manager->flush(); // LOT 10 : 40 updates

        // ====================================================================
        // LOT 11 -- Biographies des 15 auteurs (15 updates)
        // ====================================================================

        foreach ($authors as $author) {
            $author->setBio(implode("\n\n", $faker->paragraphs(random_int(2, 3))));
        }

        $manager->flush(); // LOT 11 : 15 updates

        // ====================================================================
        // LOT 12 -- Co-auteurs posts[5..24] + revision corps posts[0..9]
        //           20 associate + 10 updates
        // ====================================================================

        foreach (array_slice($posts, 5, 20) as $post) {
            $mainAuthor = $post->getAuthor();
            do {
                $coauthor = $authors[array_rand($authors)];
            } while ($coauthor === $mainAuthor);
            $post->setCoauthor($coauthor);
        }

        foreach (array_slice($posts, 0, 10) as $post) {
            $post->setBody($post->getBody() . "\n\n---\n\n" . $faker->paragraphs(2, true));
        }

        $manager->flush(); // LOT 12 : 20 associate + 10 updates

        // ====================================================================
        // LOT 13 -- Revision editoriale : titres posts[0..14] (15 updates)
        // ====================================================================

        foreach (array_slice($posts, 0, 15) as $post) {
            $post->setTitle(rtrim($post->getTitle(), '.') . ' - Revised Edition');
        }

        $manager->flush(); // LOT 13 : 15 updates

        // ====================================================================
        // LOT 14 -- Dissociations de tags + suppression de commentaires
        //           ~10 dissociate + ~4 remove
        // ====================================================================

        foreach (array_slice($posts, 10, 10) as $post) {
            $postTags = $post->getTags()->toArray();
            if (count($postTags) > 1) {
                $post->removeTag(reset($postTags));
            }
        }

        foreach ($commentsToDelete as $comment) {
            $manager->remove($comment);
        }

        $manager->flush(); // LOT 14 : ~10 dissociate + ~4 remove
    }
}
