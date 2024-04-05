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
    {  
        $user = $request->user();
        if ($request->ip() !='192.168.1.2' && $user->role_id == 1) {
            return redirect()->route('login.form')->with('error', 'Invalid Credentials,');
        }
        else if ($request->ip() == '192.168.1.2' && $user->role_id == 3) {
            return redirect()->route('login.form')->with('error', 'Invalid Credentials,');
        }
       

        return $next($request);
    }
}
