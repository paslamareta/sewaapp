<?php

namespace App\Http\Middleware;

class Authenticate
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login'); // untuk web
        }

        abort(response()->json([
            'message' => 'Unauthenticated.'
        ], 401));
    }
}
