<?php

namespace App\Http\Controllers;

use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadsController extends Controller
{
    protected $service;

    public function __construct(FileUploadService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,heic,heif',
            'file_base64' => 'nullable|string',
            'folder' => 'nullable|string|max:100',
            'prefix' => 'nullable|string|max:100',
        ]);

        if (!$request->hasFile('file') && !$request->filled('file_base64')) {
            return response()->json([
                'error' => true,
                'message' => 'Envie um arquivo ou file_base64.',
            ], 422);
        }

        try {
            $folder = $request->input('folder', 'uploads');
            $prefix = $request->input('prefix', 'file');

            $url = $request->hasFile('file')
                ? $this->service->uploadPublicFile($request->file('file'), $folder, $prefix)
                : $this->service->uploadBase64File((string) $request->input('file_base64'), $folder, $prefix);

            return response()->json([
                'error' => false,
                'message' => 'Arquivo enviado com sucesso.',
                'url' => $url,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
