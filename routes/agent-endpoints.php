<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['mcp-agent'])->group(function() {
    Route::get(
        config('vibes.routes.sse.uri'),
        empty(config('vibes.routes.sse.action'))
            ? config('vibes.routes.sse.controller')
            : [config('vibes.routes.sse.controller'), config('vibes.routes.sse.action')]
    )->name(config('vibes.routes.sse.name'))
    ->middleware(config('vibes.entry_middleware'));


    Route::post(
        config('vibes.routes.messages.uri'),
        empty(config('vibes.routes.messages.action'))
            ? config('vibes.routes.messages.controller')
            : [config('vibes.routes.messages.controller'), config('vibes.routes.messages.action')]
    )->name(config('vibes.routes.messages.name'))
    ->middleware(config('vibes.messages_middleware'));
});
