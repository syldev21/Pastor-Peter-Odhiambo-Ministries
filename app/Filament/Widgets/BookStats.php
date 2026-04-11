<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Book;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class BookStats extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $mostOrdered = OrderItem::select('book_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('book_id')
            ->orderByDesc('total')
            ->with('book')
            ->first();

        $leastOrdered = OrderItem::select('book_id', DB::raw('SUM(quantity) as total'))
            ->groupBy('book_id')
            ->orderBy('total')
            ->with('book')
            ->first();

        // ✅ Aggregate daily sales for the past 7 days
        $dailySales = OrderItem::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity * price) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ✅ All-time total sales
        $totalSales = OrderItem::sum(DB::raw('quantity * price'));

        // ✅ Today's sales
        $todaysSales = OrderItem::whereDate('created_at', now()->toDateString())
            ->sum(DB::raw('quantity * price'));

        return [
            Card::make('Total Books', Book::count())
                ->icon('heroicon-s-book-open'),
            Card::make('Featured Books', Book::where('is_featured', true)->count())
                ->icon('heroicon-s-star'),
            Card::make('Devotional Books', Book::where('is_devotional', true)->count())
                ->icon('heroicon-s-heart'),
            Card::make('Low Stock', Book::lowStock()->count())
                ->description('Books needing restock')
                ->color('danger')
                ->icon('heroicon-s-exclamation-triangle'),

            Card::make('Most Ordered', $mostOrdered?->book?->title ?? 'N/A')
                ->description("Total ordered: " . ($mostOrdered?->total ?? 0))
                ->icon('heroicon-s-trophy')
                ->color('success'),

            Card::make('Least Ordered', $leastOrdered?->book?->title ?? 'N/A')
                ->description("Total ordered: " . ($leastOrdered?->total ?? 0))
                ->icon('heroicon-s-arrow-down')
                ->color('warning'),

            // ✅ Daily sales trend chart card
            Card::make('Daily Sales Trend', $dailySales->sum('total'))
                ->description('Last 7 days')
                ->chart($dailySales->pluck('total')->toArray())
                ->color('primary')
                ->icon('heroicon-s-chart-bar'),

            // ✅ Total sales card (all-time)
            Card::make('Total Sales', $totalSales)
                ->description('All Time Sales')
                ->color('primary')
                ->icon('heroicon-s-banknotes'),

            // ✅ Today's sales card
            Card::make("Today's Sales", $todaysSales)
                ->description('Sales made today')
                ->color('success')
                ->icon('heroicon-s-currency-dollar'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}