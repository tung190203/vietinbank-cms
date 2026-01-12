<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;

class Affiliate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sref = $request->get('sref');
        $user = User::where('state', 1)->where('id', $sref)->first();
        if ($user) {
            setcookie('sref', $user->id, time() + 365 * 24 * 60 * 60);
        }
        return $next($request);
    }
}
