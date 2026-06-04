<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class MobileController extends Controller
{
    protected function paginatedResponse(LengthAwarePaginator $paginator, array $data, array $extraMeta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => array_merge([
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => $paginator->count(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                    'links' => [
                        'next' => $paginator->nextPageUrl(),
                        'previous' => $paginator->previousPageUrl(),
                    ],
                ],
            ], $extraMeta),
        ]);
    }
}

