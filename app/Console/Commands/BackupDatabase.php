<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Sauvegarde de la base de données (Steve 05.08 : « dernière sauvegarde le 31 juillet »).
 * Crée backups/cirkle-db-AAAAMMJJ-HHMM.sql.gz et conserve les N plus récentes.
 *
 *   php artisan cirkle:backup            (garde les 14 dernières)
 *   php artisan cirkle:backup --keep=30
 */
class BackupDatabase extends Command
{
	protected $signature = 'cirkle:backup {--keep=14 : Nombre de sauvegardes à conserver}';

	protected $description = 'Sauvegarde la base de données dans backups/ (format .sql.gz)';

	public function handle(): int
	{
		$dir = base_path('backups');
		if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
			$this->error("Impossible de créer le dossier {$dir}");
			return self::FAILURE;
		}

		$file = $dir . '/cirkle-db-' . now()->format('Ymd-Hi') . '.sql.gz';

		$command = sprintf(
			'mysqldump --single-transaction --quick --routines -h%s -P%s -u%s -p%s %s | gzip > %s',
			escapeshellarg(config('database.connections.mysql.host')),
			escapeshellarg((string) config('database.connections.mysql.port')),
			escapeshellarg(config('database.connections.mysql.username')),
			escapeshellarg(config('database.connections.mysql.password')),
			escapeshellarg(config('database.connections.mysql.database')),
			escapeshellarg($file)
		);

		$process = Process::fromShellCommandline($command);
		$process->setTimeout(900);
		$process->run();

		if (!$process->isSuccessful() || !is_file($file) || filesize($file) < 1024) {
			$this->error('Sauvegarde échouée : ' . trim($process->getErrorOutput()));
			@unlink($file);
			return self::FAILURE;
		}

		$this->info('Sauvegarde créée : ' . $file . ' (' . round(filesize($file) / 1024) . ' Ko)');

		// Rotation : on ne garde que les N plus récentes.
		$keep = max(1, (int) $this->option('keep'));
		$files = glob($dir . '/cirkle-db-*.sql.gz') ?: [];
		usort($files, static fn ($a, $b) => filemtime($b) <=> filemtime($a));
		foreach (array_slice($files, $keep) as $old) {
			@unlink($old);
			$this->line('Ancienne sauvegarde supprimée : ' . basename($old));
		}

		return self::SUCCESS;
	}
}
