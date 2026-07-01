<?php

namespace App\Http\Controllers;

use App\Models\BackupHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BackupsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $limit = (int) $request->query('limit', 15);
            $limit = $limit > 0 ? min($limit, 100) : 15;

            $search = trim((string) $request->query('search', ''));
            $status = trim((string) $request->query('status', ''));
            $orderBy = $this->normalizeOrderBy((string) $request->query('orderBy', 'created_at'));
            $sortedBy = strtolower((string) $request->query('sortedBy', 'desc')) === 'asc' ? 'asc' : 'desc';

            $query = BackupHistory::query();

            if ($status !== '') {
                $query->where('status', $status);
            }

            if ($search !== '') {
                $query->where(function ($builder) use ($search) {
                    $builder->where('file_name', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('storage_disk', 'like', "%{$search}%")
                        ->orWhere('storage_path', 'like', "%{$search}%");
                });
            }

            $paginator = $query->orderBy($orderBy, $sortedBy)->paginate($limit);

            $data = collect($paginator->items())
                ->map(fn ($backup) => $this->transformBackup($backup))
                ->values()
                ->all();

            return response()->json([
                'data' => $data,
                'meta' => [
                    'pagination' => [
                        'total' => $paginator->total(),
                        'per_page' => $paginator->perPage(),
                        'current_page' => $paginator->currentPage(),
                        'last_page' => $paginator->lastPage(),
                        'from' => $paginator->firstItem(),
                        'to' => $paginator->lastItem(),
                    ],
                ],
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'error' => true,
            ], 500);
        }
    }

    public function download(string $id)
    {
        $backup = BackupHistory::query()->find($id);

        if (! $backup) {
            abort(404, 'Backup não encontrado.');
        }

        if ($backup->status !== 'completed') {
            abort(422, 'Backup ainda não está disponível para download.');
        }

        if ($backup->storage_disk === 'public') {
            if (! Storage::disk('public')->exists($backup->storage_path)) {
                abort(404, 'Arquivo do backup não encontrado.');
            }

            return Storage::disk('public')->download($backup->storage_path, $backup->file_name);
        }

        if ($backup->storage_disk === 's3' && $backup->storage_path) {
            try {
                $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                    $backup->storage_path,
                    now()->addMinutes(10),
                    [
                        'ResponseContentDisposition' => 'attachment; filename="' . $backup->file_name . '"',
                    ]
                );

                return redirect()->away($temporaryUrl);
            } catch (\Throwable $exception) {
                if (! empty($backup->storage_url)) {
                    return redirect()->away($backup->storage_url);
                }

                abort(500, 'Não foi possível gerar o link temporário do backup.');
            }
        }

        if (! empty($backup->storage_url)) {
            return redirect()->away($backup->storage_url);
        }

        abort(404, 'Localização do backup não encontrada.');
    }

    private function transformBackup($backup): array
    {
        $row = is_array($backup) ? $backup : $backup->toArray();
        $id = (string) ($row['id'] ?? $row['_id'] ?? '');

        return [
            'id' => $id,
            '_id' => $id,
            'file_name' => $row['file_name'] ?? '-',
            'status' => $row['status'] ?? 'unknown',
            'storage_disk' => $row['storage_disk'] ?? '-',
            'storage_path' => $row['storage_path'] ?? '-',
            'storage_url' => $row['storage_url'] ?? null,
            'size_bytes' => (int) ($row['size_bytes'] ?? 0),
            'size_label' => $this->formatBytes((int) ($row['size_bytes'] ?? 0)),
            'error_message' => $row['error_message'] ?? null,
            'patient_id' => $row['patient_id'] ?? null,
            'patient_chat_id' => $row['patient_chat_id'] ?? null,
            'backup_date' => $row['backup_date'] ?? null,
            'started_at' => $row['started_at'] ?? null,
            'completed_at' => $row['completed_at'] ?? null,
            'created_at' => $row['created_at'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
            'download_url' => route('panel.backups.download', ['id' => $id]),
            'is_downloadable' => ($row['status'] ?? '') === 'completed',
        ];
    }

    private function normalizeOrderBy(string $orderBy): string
    {
        $allowed = [
            'file_name',
            'status',
            'storage_disk',
            'size_bytes',
            'backup_date',
            'completed_at',
            'created_at',
            'updated_at',
        ];

        return in_array($orderBy, $allowed, true) ? $orderBy : 'created_at';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), 2, ',', '.') . ' ' . $units[$power];
    }
}

