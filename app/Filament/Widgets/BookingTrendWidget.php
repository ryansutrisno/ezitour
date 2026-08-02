<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

/**
 * Line chart of new bookings per day over the last 30 days.
 * Helps admins spot demand trends at a glance on the dashboard.
 */
class BookingTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Tren Booking 30 Hari';

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '260px';

    protected function getData(): array
    {
        $days = 30;
        $startDate = Carbon::today()->subDays($days - 1);

        // Group booking counts by date, filling gaps with zero.
        $counts = Booking::query()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $key = $date->toDateString();
            $labels[] = $date->translatedFormat('d M');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Booking',
                    'data' => $data,
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
