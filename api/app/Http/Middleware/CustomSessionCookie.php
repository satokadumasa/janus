<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomSessionCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();
        $hostname = gethostname();
        \Log::debug("CustomSessionCookie::handle() path[{$path}]");
        $previousUrl = $_SERVER['HTTP_ORIGIN'];
        \Log::debug("CustomSessionCookie::handle() hostname[{$previousUrl}]");

        // ユーザ用API
        if (strpos($previousUrl, 'janus.september-rain.com')) {
            \Log::debug("CustomSessionCookie::handle() user");
            config(['session.cookie' => 'laravel_user_session']);
        }

        // ADMIN用API
        if (strpos($previousUrl, 'janusadmin.september-rain.com')) {
            \Log::debug("CustomSessionCookie::handle() partner");
            config(['session.cookie' => 'laravel_admin_session']);
        }

        return $next($request);
    }
}
