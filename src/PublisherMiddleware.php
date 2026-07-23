<?php

namespace BahriCanli\Publisher;

use Closure;
use Illuminate\Http\Request;

class PublisherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = config('bahricanli-publisher.token', '');

        if (empty($token)) {
            return response()->json(['error' => 'CM_PUBLISHER_TOKEN tanımlı değil.'], 500);
        }

        $incoming = $request->header('X-Content-Manager-Token');
        if ($incoming === null) {
            $incoming = $request->input('_cm_token', '');
        }

        if (! hash_equals($token, (string) $incoming)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
