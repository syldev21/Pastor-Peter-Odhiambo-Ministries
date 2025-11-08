<?php

namespace App\Http\Controllers;

use App\Filament\Widgets\OrderStatusOverview;

public function panel(Panel $panel): Panel
{
    return $panel
        ->widgets([
            OrderStatusOverview::class,
        ])
        ->resources([
            OrderResource::class,
            BookResource::class,
            TagResource::class,
        ]);
}