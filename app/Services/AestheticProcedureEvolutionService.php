<?php

namespace App\Services;

use App\Entities\AestheticProcedureEvolution;
use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByPatientIdCriteria;
use App\Repositories\AestheticProcedureEvolutionRepository;
use Prettus\Repository\Exceptions\RepositoryException;
use Illuminate\Support\Str;

class AestheticProcedureEvolutionService extends AppService
{
    protected $repository;
    private FileUploadService $fileUploadService;

    public function __construct(
        AestheticProcedureEvolutionRepository $repository,
        FileUploadService $fileUploadService
    )
    {
        $this->repository = $repository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @throws RepositoryException
     */
    public function all($limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByPatientIdCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

    public function create(array $data, bool $skipPresenter = false)
    {
        $payload = $this->prepareSignaturesForStorage($data);

        return parent::create($payload, $skipPresenter);
    }

    public function update(array $data, $id, bool $skipPresenter = false)
    {
        /** @var AestheticProcedureEvolution|null $existing */
        $existing = $this->repository->skipPresenter()->find($id);

        if (! $existing) {
            throw new \RuntimeException('Atendimento nao encontrado para atualizar assinaturas.');
        }

        $payload = $this->prepareSignaturesForStorage($data, $existing);

        return parent::update($payload, $id, $skipPresenter);
    }

    private function prepareSignaturesForStorage(array $data, ?AestheticProcedureEvolution $existing = null): array
    {
        foreach (['patient_signature', 'professional_signature'] as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            $rawValue = $data[$field];

            if (! is_string($rawValue)) {
                continue;
            }

            $value = trim($rawValue);

            if ($value === '') {
                $data[$field] = null;
                continue;
            }

            if (! Str::startsWith($value, 'data:image/')) {
                // Keep already stored URLs or legacy values untouched.
                $data[$field] = $value;
                continue;
            }

            $uploadedUrl = $this->fileUploadService->uploadBase64File(
                $value,
                'aesthetic-procedure-evolutions/signatures',
                $field
            );

            $oldUrl = $existing ? (string) ($existing->{$field} ?? '') : '';
            if ($oldUrl !== '' && Str::startsWith($oldUrl, ['http://', 'https://']) && $oldUrl !== $uploadedUrl) {
                $this->deleteFileS3($oldUrl);
            }

            $data[$field] = $uploadedUrl;
        }

        return $data;
    }
}

