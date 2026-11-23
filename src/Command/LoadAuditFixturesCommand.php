<?php

declare(strict_types=1);

namespace App\Command;

use App\DataFixtures\AppFixtures;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Charge les fixtures de démo en préservant les transactions d'audit distinctes.
 *
 * La commande doctrine:fixtures:load enveloppe tout le chargement dans un
 * wrapInTransaction(), ce qui fait que tous les flush() partagent la même
 * transaction SQL et donc le même transaction_id dans l'audit.
 *
 * Cette commande appelle AppFixtures::load() directement, sans transaction
 * englobante, afin que chaque flush() dispose de sa propre transaction SQL
 * et de son propre transaction_id d'audit.
 */
#[AsCommand(
    name: 'app:fixtures:load',
    description: 'Charge les fixtures de démo avec des transactions d\'audit distinctes (16 lots).',
)]
class LoadAuditFixturesCommand extends Command
{
    /** Tables d'audit à vider avant le rechargement */
    private const AUDIT_TABLES = ['author_audit', 'post_audit', 'comment_audit', 'tag_audit'];

    /** Index des racines d'audit (#[AuditRoot]), alimenté à l'écriture des entrées */
    private const AUDIT_ROOT_TABLE = 'audit_root';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AppFixtures $fixtures,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('no-reset', null, InputOption::VALUE_NONE, 'Ne pas vider la base avant le chargement');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Chargement des fixtures de démo');

        if (!$input->getOption('no-reset')) {
            $this->purge($io);
        }

        $io->section('Chargement des fixtures (16 lots)');

        // Appel DIRECT sans wrapInTransaction() : chaque flush() sera
        // sa propre transaction SQL et générera un transaction_id unique.
        $this->fixtures->load($this->em);

        $io->success('Fixtures chargées avec succès !');

        $this->printStats($io);

        return Command::SUCCESS;
    }

    private function purge(SymfonyStyle $io): void
    {
        $io->section('Vidage de la base de données');

        // Vider les tables ORM (entités métier)
        $purger = new ORMPurger($this->em);
        $purger->purge();

        // Vider les tables d'audit (non gérées par l'ORM)
        $connection = $this->em->getConnection();
        foreach ([...self::AUDIT_TABLES, self::AUDIT_ROOT_TABLE] as $table) {
            $connection->executeStatement('DELETE FROM ' . $table);
        }

        $io->text('Base vidée (entités + audit).');
    }

    private function printStats(SymfonyStyle $io): void
    {
        $connection = $this->em->getConnection();

        $entities = [
            'user'         => '"user"',
            'author'       => 'author',
            'tag'          => 'tag',
            'post'         => 'post',
            'comment'      => 'comment',
        ];

        $io->section('Statistiques');
        $rows = [];
        foreach ($entities as $label => $table) {
            $n = $connection->fetchOne('SELECT count(*) FROM ' . $table);
            $rows[] = [$label, $n];
        }

        $auditRows = [];
        foreach (self::AUDIT_TABLES as $table) {
            $total = $connection->fetchOne('SELECT count(*) FROM ' . $table);
            $txCount = $connection->fetchOne('SELECT count(DISTINCT transaction_id) FROM ' . $table);
            $auditRows[] = [$table, $total, $txCount];
        }

        $io->table(['Table', 'Enregistrements'], $rows);
        $io->table(['Table d\'audit', 'Entrées totales', 'Transactions distinctes'], $auditRows);
        $io->text(\sprintf('Index des racines (%s) : %d lignes.', self::AUDIT_ROOT_TABLE, $connection->fetchOne('SELECT count(*) FROM ' . self::AUDIT_ROOT_TABLE)));
    }
}
