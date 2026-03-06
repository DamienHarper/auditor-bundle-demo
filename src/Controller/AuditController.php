<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/history')]
class AuditController extends AbstractController
{
    private const AUDITABLE_ENTITIES = [
        'Author' => Author::class,
        'Post' => Post::class,
        'Comment' => Comment::class,
        'Tag' => Tag::class,
    ];

    private const AUDIT_TYPES = ['insert', 'update', 'remove', 'associate', 'dissociate'];

    #[Route('', name: 'app_audit_history', methods: ['GET'])]
    public function index(Request $request, Reader $reader): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $entityFilter = $request->query->get('entity', '');
        $typeFilter = $request->query->get('type', '');
        $pageSize = 25;

        $allEntries = [];

        $entitiesToQuery = '' !== $entityFilter && isset(self::AUDITABLE_ENTITIES[$entityFilter])
            ? [$entityFilter => self::AUDITABLE_ENTITIES[$entityFilter]]
            : self::AUDITABLE_ENTITIES;

        foreach ($entitiesToQuery as $label => $entityClass) {
            try {
                $options = ['page_size' => null, 'page' => null];
                if ('' !== $typeFilter) {
                    $options['type'] = $typeFilter;
                }
                $query = $reader->createQuery($entityClass, $options);
                $entries = $query->execute();
                foreach ($entries as $entry) {
                    $allEntries[] = ['entity' => $label, 'entry' => $entry];
                }
            } catch (\Throwable) {
                // audit table may not exist yet
            }
        }

        // Sort by created_at DESC
        usort($allEntries, static function (array $a, array $b): int {
            $dateA = $a['entry']->createdAt;
            $dateB = $b['entry']->createdAt;
            if ($dateA === $dateB) {
                return $b['entry']->id <=> $a['entry']->id;
            }
            if (null === $dateA) {
                return 1;
            }
            if (null === $dateB) {
                return -1;
            }

            return $dateB <=> $dateA;
        });

        $total = count($allEntries);
        $numPages = max(1, (int) ceil($total / $pageSize));
        $offset = ($page - 1) * $pageSize;
        $entries = array_slice($allEntries, $offset, $pageSize);

        return $this->render('audit/index.html.twig', [
            'entries' => $entries,
            'entity_filter' => $entityFilter,
            'type_filter' => $typeFilter,
            'auditable_entities' => array_keys(self::AUDITABLE_ENTITIES),
            'audit_types' => self::AUDIT_TYPES,
            'current_page' => $page,
            'num_pages' => $numPages,
            'total' => $total,
            'has_previous' => $page > 1,
            'has_next' => $page < $numPages,
        ]);
    }
}
