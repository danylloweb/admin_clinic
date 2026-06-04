<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnsureAllowedGiftOrigin
{
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigins = config('gift.allowed_origins', []);
        $requestOrigin = $this->extractRequestOrigin($request);

        if (empty($allowedOrigins) || !$requestOrigin || !in_array($requestOrigin, $allowedOrigins, true)) {
            return $this->forbidden();
        }

        return $next($request);
    }

    private function extractRequestOrigin(Request $request): ?string
    {
        $origin = $request->headers->get('Origin');
        if (!empty($origin)) {
            return rtrim($origin, '/');
        }

        $referer = $request->headers->get('Referer');
        if (empty($referer)) {
            return null;
        }

        $parts = parse_url($referer);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $origin = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $origin .= ':' . $parts['port'];
        }

        return rtrim($origin, '/');
    }

    private function forbidden(): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => 'Origem não autorizada.',
        ], 403);
    }
}

