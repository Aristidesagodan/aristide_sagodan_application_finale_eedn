<?php

namespace App\Command;

use App\Service\DatatourismeImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-datatourisme',
    description: 'Importe les données Datatourisme depuis les fichiers CSV',
)]
class ImportDatatourismeCommand extends Command
{
    private DatatourismeImporter $importer;

    public function __construct(DatatourismeImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        ini_set('memory_limit', '2048M');

        $io->title('🚀 Import Datatourisme');

        try {
            $stats = $this->importer->import('var/data/*.csv');

            $io->success([
                "Import terminé !",
                "Total lignes: {$stats['total']}",
                "✅ Importés: {$stats['success']}",
                "⚠️ Ignorés: {$stats['ignored']}",
                "❌ Erreurs: {$stats['errors']}"
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Erreur: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}