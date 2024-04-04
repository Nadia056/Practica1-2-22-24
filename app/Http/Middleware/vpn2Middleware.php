<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class vpn2Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {  $user = $request->user();

        switch ($user->role_id) {
            case 1: // Admin
                if ($request->ip() != '192.168.1.2') {
                    return redirect()->route('login.form');
                }
                break;
            case 2: // Coordinator
                // Coordinator can login from any IP
                break;
            case 3: // Guest
                if ($request->ip() == '192.168.1.2') {
                    return redirect()->route('login.form');
                }
                break;
            default:
                return redirect()->route('login.form');
        }

        return $next($request);
    }
}
