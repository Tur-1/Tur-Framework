<?php

namespace TurFramework\Http\Middleware;

use TurFramework\Http\Request;

interface Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request);
}
