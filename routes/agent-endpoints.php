<?php

use Illuminate\Support\Facades\Route;
use ProjectSaturnStudios\Vibes\Http\Middleware\ScaffoldSSEConnection;

Route::middleware(['mcp-agent'])->group(function() {
    Route::get(
        config('vibes.routes.sse.uri'),
        empty(config('vibes.routes.sse.action'))
            ? config('vibes.routes.sse.controller')
            : [config('vibes.routes.sse.controller'), config('vibes.routes.sse.action')]
    )->name(config('vibes.routes.sse.name'))->middleware([ScaffoldSSEConnection::class]);

    Route::post(
        config('vibes.routes.messages.uri'),
        empty(config('vibes.routes.messages.action'))
            ? config('vibes.routes.messages.controller')
            : [config('vibes.routes.messages.controller'), config('vibes.routes.messages.action')]
    )->name(config('vibes.routes.messages.name'));//->middleware([FreshenUpThePlaceRealQuick::class]);
});
