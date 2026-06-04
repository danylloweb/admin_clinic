<?php

namespace App\Http\Controllers\Mobile;

use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileUploadController extends MobileController
{
    protected FileUploadService $uploadService;

    public function __construct(FileUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'nullable|file|max:20480|mimes:jpg,jpeg,png,webp,heic,heif,mp3,m4a,aac,ogg,wav',
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
            $folder = $request->input('folder', 'uploads/mobile');
            $prefix = $request->input('prefix', 'media');

            $url = $request->hasFile('file')
                ? $this->uploadService->uploadPublicFile($request->file('file'), $folder, $prefix)
                : $this->uploadService->uploadBase64File((string) $request->input('file_base64'), $folder, $prefix);

            return response()->json([
                'error' => false,
                'message' => 'Arquivo enviado com sucesso.',
                'data' => [
                    'url' => $url,
                    'type' => $request->hasFile('file') ? $request->file('file')->getClientMimeType() : 'base64',
                ],
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }
}
