<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthenticateWeb
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken() ?? $request->cookie('jwt_token');
            if (!$token) {
                return redirect('/login')->withErrors(['message' => 'Token ausente']);
            }

            $user = JWTAuth::setToken($token)->toUser();

            if (!$user) {
                return redirect('/login')->withErrors(['message' => 'Usuário inválido']);
            }

            $request->attributes->add(['user_jwt' => $user]);

        } catch (JWTException $e) {
            return redirect('/login')->withErrors(['message' => 'Token inválido ou expirado']);
        }

        return $next($request);
    }
}
