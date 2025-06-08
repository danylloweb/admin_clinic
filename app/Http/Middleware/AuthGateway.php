<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AuthGateway
{

    /**
     * @param $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle($request, Closure $next)
    {
//        if (!$request->header('Authorization')) {
//            return $this->unauthorized();
//        }
//       if ($request->header('Authorization') != config('apikey.key')) {
//            return $this->unauthorized();
//        }
        return $next($request);
    }

    /**
     * @return JsonResponse
     */
    private function unauthorized(): JsonResponse
    {
        return response()->json(['error' => true, 'message' => 'Não autorizado'], 401);
    }
}
