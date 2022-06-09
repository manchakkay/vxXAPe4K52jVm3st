<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $req
     * @return string|null
     */
    protected function redirectTo($req)
    {
        if (! $req->expectsJson()) {
            return route('login');
        }
    }
}
