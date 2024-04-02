<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class roleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role_id)
    {
        
        $user=$request->user();
       if ($user->role_id==$role_id) {

           return $next($request);
       }

         else{
            switch ($user->role_id) {
                case 1:
                    return redirect()->route('AdminHome', ['id' => $user->id]);
                    break;
                case 2:
                    return redirect()->route('CoordHome', ['id' => $user->id]);
                    break;
                case 3:
                    return redirect()->route('GuestHome', ['id' => $user->id]);
                    break;
                default:
                    return redirect()->route('login.form');
                    break;
            }
         }
        
        
    }
}