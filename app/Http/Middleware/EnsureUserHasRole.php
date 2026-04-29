<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->guest(route('login'));
        }

        $normalizedRoles = array_map(
            static fn (string $role): string => strtolower(trim($role)),
            $roles
        );

        $currentRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;
        if (! in_array(strtolower($currentRole), $normalizedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
