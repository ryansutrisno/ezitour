<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Revenue stats — total revenue from paid bookings (all time) and this month.
 * Currency is formatted as IDR for quick scanning on the admin dashboard.
 */
class RevenueStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $totalRevenue = (float) Booking::where('status', 'paid')->sum('total_amount');

        $thisMonthRevenue = (float) Booking::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return [
            Stat::make('Total Pendapatan', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari semua booking berstatus lunas')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Pendapatan Bulan Ini', 'Rp '.number_format($thisMonthRevenue, 0, ',', '.'))
                ->description('Booking lunas bulan '.now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
