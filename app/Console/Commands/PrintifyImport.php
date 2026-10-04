<?php

namespace App\Console\Commands;

use App\Services\PrintifyProductImporter;
use App\Services\PrintifyService;
use Illuminate\Console\Command;

class PrintifyImport extends Command
{
    protected $signature = 'printify:import';

    protected $description = 'Import products, variants and images from Printify into the catalog';

    public function handle(PrintifyService $printify, PrintifyProductImporter $importer): int
    {
        if (! $printify->enabled()) {
            $this->error('Printify is not configured. Set PRINTIFY_API_TOKEN and PRINTIFY_SHOP_ID in .env.');

            return self::FAILURE;
        }

        $count = 0;

        $printify->eachProduct(function (array $p) use (&$count, $importer) {
            if ($importer->import($p)) {
                $count++;
                $this->line("Imported: {$p['title']}");
            }
        });

        $this->info("Done. {$count} product(s) synced from Printify.");

        return self::SUCCESS;
    }
}
