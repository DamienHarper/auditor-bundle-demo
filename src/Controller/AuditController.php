<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use DH\Auditor\History\HistoryGrouper;
use DH\Auditor\Model\Entry;
use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use DH\AuditorBundle\Viewer\HistoryRows;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * A journal of the whole blog built in the application: one line per action (a transaction, whatever the tables it
 * wrote to), told as a phrase, with the rows of the viewer.
 */
#[Route('/history')]
class AuditController extends AbstractController
{
    private const array AUDITABLE_ENTITIES = [
        'Author' => Author::class,
        'Post' => Post::class,
        'Comment' => Comment::class,
        'Tag' => Tag::class,
    ];

    private const int PAGE_SIZE = 25;

    #[Route('', name: 'app_audit_history', methods: ['GET'])]
    public function index(Request $request, Reader $reader, Connection $connection, HistoryRows $historyRows): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $entityFilter = $request->query->getString('entity');
        $entities = isset(self::AUDITABLE_ENTITIES[$entityFilter])
            ? [self::AUDITABLE_ENTITIES[$entityFilter]]
            : array_values(self::AUDITABLE_ENTITIES);

        // 1. The transactions of the page, most recent first, across the audit tables.
        $union = implode(' UNION ALL ', array_map(
            static fn (string $entity): string => \sprintf('SELECT transaction_id, created_at FROM %s', $reader->getEntityAuditTableName($entity)),
            $entities,
        ));
        $transactionIds = $connection->fetchFirstColumn(\sprintf(
            'SELECT transaction_id FROM (%s) a WHERE transaction_id IS NOT NULL GROUP BY transaction_id ORDER BY MAX(created_at) DESC LIMIT %d OFFSET %d',
            $union,
            self::PAGE_SIZE + 1,
            ($page - 1) * self::PAGE_SIZE,
        ));
        $hasNext = \count($transactionIds) > self::PAGE_SIZE;
        $transactionIds = \array_slice($transactionIds, 0, self::PAGE_SIZE);

        // 2. Their entries, read through the Reader (roles, timezone, Entry model), most recent first.
        $entries = [];
        if ([] !== $transactionIds) {
            foreach ($entities as $entity) {
                $entries = [...$entries, ...$reader->createQuery($entity, ['transaction_id' => $transactionIds, 'page_size' => null])->execute()];
            }
        }
        usort($entries, static fn (Entry $a, Entry $b): int => strcmp($b->getSortKey(), $a->getSortKey()));

        // 3. One action per transaction, told as a phrase ("alice moved the post from Draft to Published").
        $rows = $historyRows->build(HistoryGrouper::byTransaction($entries), withContext: true);

        return $this->render('audit/index.html.twig', [
            'rows' => $rows,
            'entity_filter' => $entityFilter,
            'auditable_entities' => array_keys(self::AUDITABLE_ENTITIES),
            'current_page' => $page,
            'has_previous' => $page > 1,
            'has_next' => $hasNext,
        ]);
    }
}
