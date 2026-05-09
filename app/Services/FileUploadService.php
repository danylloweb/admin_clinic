<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadService extends AppService
{
    public function uploadPublicFile(UploadedFile $file, string $folder = 'uploads', string $prefix = 'file'): string
    {
        $content = file_get_contents($file->getRealPath());

        if ($content === false) {
            throw new \RuntimeException('Nao foi possivel ler o arquivo enviado.');
        }

        $folder = trim($folder, '/');
        $prefix = Str::slug($prefix ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'file');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');

        $fileName = sprintf(
            '%s/%s-%s-%s.%s',
            $folder,
            now()->format('YmdHis'),
            Str::random(12),
            $prefix,
            $extension
        );

        return $this->putFileS3($fileName, $content);
    }

    public function uploadBase64File(string $base64, string $folder = 'uploads', string $prefix = 'file'): string
    {
        if (!preg_match('/^data:(?<mime>[\w\/\-\.\+]+);base64,(?<content>.+)$/', $base64, $matches)) {
            throw new \InvalidArgumentException('Arquivo em base64 inválido.');
        }

        $content = base64_decode($matches['content'], true);
        if ($content === false) {
            throw new \InvalidArgumentException('Não foi possível decodificar o arquivo base64.');
        }

        $extension = $this->mimeToExtension((string) $matches['mime']);
        $folder = trim($folder, '/');
        $prefix = Str::slug($prefix ?: 'file');

        $fileName = sprintf(
            '%s/%s-%s-%s.%s',
            $folder,
            now()->format('YmdHis'),
            Str::random(12),
            $prefix,
            $extension
        );

        return $this->putFileS3($fileName, $content);
    }

    private function mimeToExtension(string $mime): string
    {
        return match (strtolower($mime)) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/heic' => 'heic',
            'image/heif' => 'heif',
            'application/pdf' => 'pdf',
            default => 'bin',
        };
    }
}
