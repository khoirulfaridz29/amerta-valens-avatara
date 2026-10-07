<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxFlashToJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->expectsJson() && $response instanceof RedirectResponse) {
            $status = $request->session()->pull('status');
            $error = $request->session()->pull('error');

            return response()->json([
                'ok' => $error === null,
                'message' => $error ?? $status ?? 'Berhasil',
                'redirect' => $response->getTargetUrl(),
            ]);
        }

        return $response;
    }
}
