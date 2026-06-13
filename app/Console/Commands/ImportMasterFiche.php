<?php

namespace App\Console\Commands;

use App\Imports\ExcelImport;
use Illuminate\Console\Command;

class ImportMasterFiche extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'master:import {path : Chemin du fichier MASTER 2350 (.xlsx)}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Importe une fiche de compétence MASTER 2350 (format 010626).';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle(): int
	{
		$path = $this->argument('path');

		if (!is_file($path)) {
			$this->error("Fichier introuvable : {$path}");
			return self::FAILURE;
		}

		try {
			$stats = (new ExcelImport())->import($path);
		} catch (\Exception $e) {
			$this->error('Import échoué : ' . $e->getMessage());
			return self::FAILURE;
		}

		$this->info(sprintf(
			'Import fait : %s (%s, %s) — %d services, %d capacités, %d mots-clés.',
			$stats['profession'],
			$stats['provider_type'] ?? 'clientèle non précisée',
			$stats['locale'],
			$stats['services'],
			$stats['capabilities'],
			$stats['keywords']
		));

		foreach ($stats['prices'] as $duration => $cost) {
			$this->line("  Forfait {$duration} mois : {$cost}\$");
		}

		foreach ($stats['warnings'] as $warning) {
			$this->warn($warning);
		}

		return self::SUCCESS;
	}
}
