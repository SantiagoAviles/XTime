<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupDatabaseCommand extends Command
{
    protected $signature = 'altermec:backup-db';
    protected $description = 'Genera un dump SQL de la base de datos y lo almacena en storage/app/backups.';

    public function handle(): int
    {
        $config = config('database.connections.' . config('database.default'));

        if (($config['driver'] ?? null) !== 'mysql') {
            $this->warn('Solo se soporta MySQL. Driver actual: ' . ($config['driver'] ?? 'desconocido'));
            return self::FAILURE;
        }

        $timestamp = now()->format('Ymd_His');
        $filename  = "backups/db_altermec_{$timestamp}.sql";
        $fullPath  = storage_path('app/' . $filename);

        Storage::disk('local')->makeDirectory('backups');

        $cmd = [
            'mysqldump',
            '--host=' . $config['host'],
            '--port=' . ($config['port'] ?? 3306),
            '--user=' . $config['username'],
            '--password=' . $config['password'],
            '--single-transaction',
            '--quick',
            '--routines',
            $config['database'],
        ];

        $process = new Process($cmd);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('mysqldump falló: ' . $process->getErrorOutput());
            activity('seguridad')
                ->withProperties(['error' => $process->getErrorOutput()])
                ->event('backup_db_fallo')
                ->log('backup_db_fallo');
            return self::FAILURE;
        }

        file_put_contents($fullPath, $process->getOutput());

        activity('seguridad')
            ->withProperties(['archivo' => $filename, 'tamano_bytes' => filesize($fullPath)])
            ->event('backup_db_exitoso')
            ->log('backup_db_exitoso');

        $this->info("Backup creado: {$filename}");
        return self::SUCCESS;
    }
}
