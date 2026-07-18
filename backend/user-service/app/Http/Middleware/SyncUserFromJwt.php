<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Throwable;

class SyncUserFromJwt
{
    public function handle(Request $request, Closure $next): mixed
    {
        try {
            $payload = JWTAuth::setRequest($request)->parseToken()->getPayload();
            $userId  = (int) $payload->get('sub');
            $email   = (string) ($payload->get('email') ?? '');
            $name    = (string) ($payload->get('name') ?? 'User');

            if ($userId > 0 && ! User::withTrashed()->find($userId)) {
                User::create([
                    'id'       => $userId,
                    'name'     => $name,
                    'email'    => $email,
                    'password' => bcrypt(Str::random(32)),
                    'status'   => UserStatus::Active,
                ]);
            }
        } catch (Throwable) {
            // No token or invalid token — let auth:api handle it
        }

        return $next($request);
    }
}
