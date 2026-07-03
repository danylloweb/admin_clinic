<?php

namespace App\Console\Commands;

use App\Entities\Patient;
use App\Models\BackupHistory;
use App\Services\AppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup MySQL database as plain text, upload to storage and notify patient via WhatsApp';

    protected AppService $appService;

    public function __construct(AppService $appService)
    {
        parent::__construct();
        $this->appService = $appService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $timestamp = now()->format('Y-m-d');
        $backupFileName = "backup_{$timestamp}.txt";
        $tempPath = storage_path("backups/{$backupFileName}");

        $backup = BackupHistory::create([
            'file_name' => $backupFileName,
            'status' => 'running',
            'backup_date' => now(),
            'started_at' => now(),
            'metadata' => [
                'database' => config('database.connections.mysql.database'),
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
            ],
        ]);

        try {
            $this->info('Starting database backup...');

            if (! is_dir(storage_path('backups'))) {
                mkdir(storage_path('backups'), 0755, true);
            }

            $this->info('Executing mysqldump...');
            $this->executeMysqldump($backupFileName);
            $this->info("Backup created: {$backupFileName}");

            $sizeBytes = file_exists($tempPath) ? (filesize($tempPath) ?: 0) : 0;

            $this->info('Uploading backup...');
            [$disk, $path, $url] = $this->storeBackupFile($backupFileName, $tempPath);
            $this->info("Uploaded to {$disk}: {$path}");

            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }

            $backup->forceFill([
                'status' => 'completed',
                'storage_disk' => $disk,
                'storage_path' => $path,
                'storage_url' => $url,
                'size_bytes' => $sizeBytes,
                'completed_at' => now(),
                'expires_at' => now()->addDays(7),
            ])->save();

            $shareableUrl = route('panel.backups.download', ['id' => (string) $backup->getKey()]);

            $this->cleanOldBackups();
            $this->notifyPatientViaWhatsApp($backup, $shareableUrl);

            $this->info('Database backup completed successfully!');
            Log::info('Database backup completed', [
                'backup_id' => (string) $backup->getKey(),
                'file_name' => $backupFileName,
                'storage_disk' => $disk,
                'storage_path' => $path,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }

            $backup->forceFill([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ])->save();

            $this->error("Backup failed: {$e->getMessage()}");
            Log::error('Database backup failed', [
                'backup_id' => (string) $backup->getKey(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Execute mysqldump with error capture
     */
    private function executeMysqldump(string $backupFileName): bool
    {
        $tempPath = storage_path("backups/{$backupFileName}");
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $binary = $this->resolveDumpBinary();
        $commandOptions = $this->resolveDumpOptions($binary);

        // Use defaults extra file to avoid exposing the password in the process list.
        $passwordFile = tempnam(sys_get_temp_dir(), 'mysql_pwd_');
        file_put_contents($passwordFile, "[client]\npassword={$password}\n");
        chmod($passwordFile, 0600);

        $command = [
            $binary,
            "--defaults-extra-file={$passwordFile}",
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            ...$commandOptions,
            $database,
        ];

        $this->info("Executing: {$binary} -h {$host} -P {$port} -u {$username} {$database}");

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['file', $tempPath, 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (! is_resource($process)) {
            @unlink($passwordFile);
            throw new \Exception('Could not start database dump process.');
        }

        fclose($pipes[0]);
        $errorOutput = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);

        @unlink($passwordFile);

        if ($returnCode !== 0) {
            $errorMsg = trim($errorOutput);

            if ($errorMsg === '' && file_exists($tempPath)) {
                $errorMsg = trim((string) file_get_contents($tempPath));
            }

            $this->error("Mysqldump failed with code {$returnCode}");
            $this->error("Output: {$errorMsg}");
            throw new \Exception("mysqldump command failed: {$errorMsg}");
        }

        if (!file_exists($tempPath) || filesize($tempPath) === 0) {
            throw new \Exception('mysqldump created empty file or no file was created');
        }

        return true;
    }

    /**
     * Resolve an available dump binary in the container.
     */
    private function resolveDumpBinary(): string
    {
        foreach (['mysqldump', 'mariadb-dump'] as $binary) {
            $path = trim((string) shell_exec("command -v {$binary} 2>/dev/null"));

            if ($path !== '') {
                return $path;
            }
        }

        throw new \Exception('Neither mysqldump nor mariadb-dump is available in the container.');
    }

    /**
     * Resolve dump options, including TLS behavior.
     */
    private function resolveDumpOptions(string $binary): array
    {
        $options = [
            '--single-transaction',
            '--quick',
            '--skip-lock-tables',
        ];

        $sslCa = null;
        $configuredOptions = config('database.connections.mysql.options', []);

        foreach ($configuredOptions as $value) {
            if (is_string($value) && trim($value) !== '') {
                $sslCa = $value;
                break;
            }
        }

        if (! empty($sslCa)) {
            $options[] = "--ssl-ca={$sslCa}";

            if ($this->dumpSupportsOption($binary, '--ssl-mode')) {
                $options[] = '--ssl-mode=VERIFY_CA';
            }

            return $options;
        }

        if ($this->dumpSupportsOption($binary, '--ssl-mode')) {
            $options[] = '--ssl-mode=DISABLED';
            return $options;
        }

        if ($this->dumpSupportsOption($binary, '--skip-ssl')) {
            $options[] = '--skip-ssl';

            if ($this->dumpSupportsOption($binary, '--skip-ssl-verify-server-cert')) {
                $options[] = '--skip-ssl-verify-server-cert';
            }

            return $options;
        }

        if ($this->dumpSupportsOption($binary, '--ssl')) {
            $options[] = '--ssl=0';
        }

        return $options;
    }

    /**
     * Check whether the dump binary supports a given CLI option.
     */
    private function dumpSupportsOption(string $binary, string $option): bool
    {
        $helpOutput = shell_exec(escapeshellarg($binary) . ' --help 2>&1');

        if (! is_string($helpOutput) || $helpOutput === '') {
            return false;
        }

        return str_contains($helpOutput, $option);
    }

    /**
     * Store backup file to S3 or public disk
     */
    private function storeBackupFile(string $fileName, string $localPath): array
    {
        $storagePath = "backups/{$fileName}";
        $content = file_get_contents($localPath);

        if ($content === false) {
            throw new \Exception('Could not read backup file for upload.');
        }

        try {
            if ($this->canUseS3Disk()) {
                Storage::disk('s3')->put($storagePath, $content, [
                    'ContentType' => 'text/plain',
                ]);
                return ['s3', $storagePath, Storage::disk('s3')->url($storagePath)];
            }
        } catch (\Throwable $exception) {
            Log::warning('S3 upload unavailable, using public disk fallback: ' . $exception->getMessage());
        }

        Storage::disk('public')->put($storagePath, $content);
        return ['public', $storagePath, Storage::disk('public')->url($storagePath)];
    }

    /**
     * Check if S3 disk is available and configured
     */
    private function canUseS3Disk(): bool
    {
        return class_exists('League\\Flysystem\\AwsS3V3\\PortableVisibilityConverter')
            && config('filesystems.disks.s3.driver') === 's3'
            && filled(config('filesystems.disks.s3.bucket'));
    }

    /**
     * Delete backups that reached expiration, with legacy fallback for old records.
     */
    private function cleanOldBackups(): void
    {
        try {
            $oldBackups = BackupHistory::query()
                ->where(function ($query) {
                    $query->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now());
                })
                ->orWhere(function ($query) {
                    $query->whereNull('expires_at')
                        ->where('backup_date', '<', now()->subDays(5)->startOfDay());
                })
                ->get();

            $deletedCount = 0;

            foreach ($oldBackups as $backup) {
                try {
                    if ($backup->storage_disk && $backup->storage_path) {
                        Storage::disk($backup->storage_disk)->delete($backup->storage_path);
                    }
                } catch (\Throwable $exception) {
                    Log::warning('Failed deleting old backup file', [
                        'backup_id' => (string) $backup->getKey(),
                        'error' => $exception->getMessage(),
                    ]);
                }

                $backup->delete();
                $deletedCount++;
            }

            Log::info('Old backups cleaned', ['count' => $deletedCount]);
            $this->info("Cleaned {$deletedCount} old backups");
        } catch (\Throwable $e) {
            Log::warning('Failed to clean old backups', ['error' => $e->getMessage()]);
            $this->warn("Warning: Could not clean old backups - {$e->getMessage()}");
        }
    }

    /**
     * Send WhatsApp notification to patient ID=1 with backup link
     */
    private function notifyPatientViaWhatsApp(BackupHistory $backup, string $shareableUrl): void
    {
        try {
            $patient = Patient::find(1);

            if (! $patient) {
                $backup->forceFill([
                    'patient_id' => 1,
                    'error_message' => trim(($backup->error_message ? $backup->error_message . ' | ' : '') . 'Patient ID=1 not found'),
                ])->save();
                return;
            }

            if (! $patient->chat_id) {
                $backup->forceFill([
                    'patient_id' => $patient->id,
                    'error_message' => trim(($backup->error_message ? $backup->error_message . ' | ' : '') . 'Patient has no chat_id'),
                ])->save();
                return;
            }

            $message = "📦 *Backup de Banco de Dados*\n\n"
                . "Um novo backup foi gerado com sucesso.\n\n"
                . "📅 *Data:* " . now()->format('d/m/Y H:i:s') . "\n"
                . "📁 *Arquivo:* {$backup->file_name}\n"
                . "⏰ *Arquivo disponível até:* " . optional($backup->expires_at)->format('d/m/Y H:i:s') . "\n\n"
                . "🔗 *Link do Backup:*\n{$shareableUrl}";

            $response = $this->appService->sendMessageToWhatsApp($patient->chat_id, $message);

            $backup->forceFill([
                'patient_id' => $patient->id,
                'patient_chat_id' => $patient->chat_id,
                'whatsapp_sent_at' => now(),
                'whatsapp_response' => $this->normalizeWhatsAppResponse($response),
            ])->save();

            Log::info('Backup notification sent via WhatsApp', [
                'backup_id' => (string) $backup->getKey(),
                'patient_id' => $patient->id,
                'chat_id' => $patient->chat_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send WhatsApp notification', ['error' => $e->getMessage()]);
            $this->warn("Warning: Could not send WhatsApp notification - {$e->getMessage()}");
        }
    }

    /**
     * Normalize WhatsApp response
     */
    private function normalizeWhatsAppResponse(mixed $response): array
    {
        if (is_array($response)) {
            return $response;
        }

        if (is_object($response)) {
            return json_decode(json_encode($response), true) ?: [];
        }

        return ['raw' => (string) $response];
    }

}

