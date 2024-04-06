<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

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
        try {
        $user = $request->user();

        if ($request->ip() !='192.168.1.2' && $user->role_id == 1) {
             Auth::logout();
            return redirect()->route('login.form')->with('error', 'Invalid Credentials,');
        }

        else if ($request->ip() == '192.168.1.2' && $user->role_id == 3) {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'Invalid Credentials,');
        }
       

        return $next($request);
    } catch (Exception $e) {
        Log::error('Error in vpn2Middleware' . $e);
        return redirect()->route('login.form');
    }catch(PDOException $e){
        Log::error('Error in vpn2Middleware' . $e);
        return redirect()->route('login.form');
    }catch(QueryException $e)
    {
        Log::error('Error in vpn2Middleware' . $e);
        return redirect()->route('login.form');
    }
}
}
