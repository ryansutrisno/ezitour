<?php

namespace App\Filament\Widgets;

use App\Models\Package;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Top 5 packages ranked by total booking volume, with paid revenue per package.
 * Helps admins quickly see which packages drive the most business.
 */
class TopPackagesWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 5 Paket Terlaris';

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Paket')
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Total Booking')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_revenue')
                    ->label('Pendapatan (Lunas)')
                    ->money('IDR')
                    ->sortable(),
            ])
            ->defaultSort('bookings_count', 'desc');
    }

    /**
     * Packages with aggregate booking counts and paid-only revenue.
     */
    protected function getTableQuery(): Builder
    {
        return Package::query()
            ->withCount(['bookings'])
            ->withSum(['bookings as paid_revenue' => fn (Builder $q) => $q->where('status', 'paid')], 'total_amount')
            ->orderByDesc('bookings_count');
    }
}
