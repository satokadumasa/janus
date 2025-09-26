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
        // \Log::debug("CustomSessionCookie::handle() SERVER:" . print_r($_SERVER, true));
        $previousUrl = $_SERVER['HTTP_ORIGIN'];
        \Log::debug("CustomSessionCookie::handle() hostname[{$previousUrl}]");

        // ユーザ用API
        if (strpos($previousUrl, 'kurapital.pcrm.work')) {
            \Log::debug("CustomSessionCookie::handle() user");
            config(['session.cookie' => 'laravel_user_session']);
        }

        // パートナー用API
        if (strpos($previousUrl, 'partner.pcrm.work')) {
            \Log::debug("CustomSessionCookie::handle() partner");
            config(['session.cookie' => 'laravel_partner_session']);
        }

        // 用API
        if (strpos($previousUrl, 'houjin.pcrm.work')) {
            \Log::debug("CustomSessionCookie::handle() houjin");
            config(['session.cookie' => 'laravel_houjin_session']);
        }
        return $next($request);
    }
}
