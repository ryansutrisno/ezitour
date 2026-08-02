<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Booking volume stats — total all-time, this month with delta vs last month,
 * and current pending-payment count. Shown on the admin dashboard.
 */
class BookingStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalBookings = Booking::count();

        $thisMonth = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = Booking::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $delta = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100) : ($thisMonth > 0 ? 100 : 0);
        $deltaDescription = ($delta >= 0 ? '+' : '').$delta.'% vs bulan lalu';

        $pendingCount = Booking::where('status', 'pending')->count();

        return [
            Stat::make('Total Booking', $totalBookings)
                ->description('Semua waktu')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('gray')
                ->chart([]),

            Stat::make('Booking Bulan Ini', $thisMonth)
                ->description($deltaDescription)
                ->descriptionIcon($delta >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($delta >= 0 ? 'success' : 'danger'),

            Stat::make('Pending Pembayaran', $pendingCount)
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'gray'),
        ];
    }
}
