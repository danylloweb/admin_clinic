<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthGateway
{

    /**
     * @param $request
     * @param Closure $next
     * @return JsonResponse|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('jwt_token');
        if (empty($token)) {
            return $this->unauthorized();
        }

        try {
            $user = Cache::store('redis')->tags('logins')->remember($token, 12000, function () use ($token) {
                return JWTAuth::setToken($token)->toUser();
            });
            if (!$user) {
                return $this->unauthorized();
            }


            $request->attributes->add(['user_jwt' => $user]);
        } catch (JWTException $exception) {
            return $this->unauthorized();
        }

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
