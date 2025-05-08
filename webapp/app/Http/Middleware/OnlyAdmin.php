<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class OnlyAdmin{
    public function handle(Request $request, Closure $next): Response {
        // Check if the user is admin
        if(auth()->user() && auth()->user->isAdmin()){
            return $next($request);
        }

        // Deny or redirect access in case of not admin
        return redirect('/')->with('error', 'Unauthorized Access.');
    }
}
