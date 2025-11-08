<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Books', Book::count()),
            Card::make('Featured Books', Book::where('is_featured', true)->count()),
            Card::make('Devotional Books', Book::where('is_devotional', true)->count()),
            Stat::make('Total Books', Book::count()),
            Stat::make('Low Stock', Book::lowStock()->count())
                ->description('Books needing restock')
                ->color('danger'),
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}