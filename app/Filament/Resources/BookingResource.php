<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->disabled() // Admin usually doesn't change user
                    ->dehydrated(),
                Forms\Components\Select::make('package_id')
                    ->relationship('package', 'name')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DatePicker::make('travel_date')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('participants')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('pickup_location')
                    ->required()
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->label('Payment Date')
                    ->disabled()
                    ->visible(fn ($record) => $record?->payment_date !== null),
                Forms\Components\Section::make('Assignment')
                    ->description('Assign Car and Driver for this trip')
                    ->schema([
                        Forms\Components\Select::make('car_id')
                            ->relationship('car', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('driver_id')
                            ->relationship('driver', 'name')
                            ->preload()
                            ->searchable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('package.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('travel_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participants')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('latestTransaction.payment_type')
                    ->label('Payment Method')
                    ->placeholder('-')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Driver')
                    ->placeholder('Not Assigned'),
                Tables\Columns\TextColumn::make('car.name')
                    ->label('Car')
                    ->placeholder('Not Assigned'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('paid_today')
                    ->label('Paid Today')
                    ->query(fn ($query) => $query->where('status', 'paid')
                        ->whereDate('payment_date', today())),
                Tables\Filters\Filter::make('unpaid')
                    ->label('Unpaid')
                    ->query(fn ($query) => $query->where('status', 'pending')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
