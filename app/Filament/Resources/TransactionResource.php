<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Payment';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->label('Order ID')
                            ->disabled(),
                        Forms\Components\Select::make('booking_id')
                            ->relationship('booking', 'id')
                            ->disabled()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Booking #{$record->id} - {$record->user->name}"),
                        Forms\Components\TextInput::make('gross_amount')
                            ->label('Amount')
                            ->disabled()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('payment_type')
                            ->label('Payment Method')
                            ->disabled(),
                        Forms\Components\Select::make('transaction_status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'expired' => 'Expired',
                                'superseded' => 'Superseded',
                            ])
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('booking.user.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_type')
                    ->label('Payment Method')
                    ->placeholder('Not selected')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('transaction_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'expired' => 'gray',
                        'superseded' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('settlement_time')
                    ->label('Payment Date')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'superseded' => 'Superseded',
                    ]),
                Tables\Filters\Filter::make('paid_today')
                    ->label('Paid Today')
                    ->query(fn ($query) => $query->where('transaction_status', 'paid')
                        ->whereDate('settlement_time', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('checkStatus')
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Check Payment Status')
                    ->modalDescription('This will check the current payment status from Midtrans API and update the local record if needed.')
                    ->action(function (Transaction $record) {
                        try {
                            $paymentService = app(PaymentService::class);
                            $oldStatus = $record->transaction_status;
                            $status = $paymentService->checkPaymentStatus($record->order_id);

                            $midtransStatus = $status['transaction_status'] ?? 'unknown';
                            $record->refresh();
                            $newStatus = $record->transaction_status;

                            $message = $oldStatus !== $newStatus
                                ? "Status updated from '{$oldStatus}' to '{$newStatus}'"
                                : "Current status: {$midtransStatus} (no change)";

                            Notification::make()
                                ->title('Status Checked')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Transaction $record) => in_array($record->transaction_status, ['pending', 'failed'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk delete for transactions
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Transaction Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_id')
                            ->label('Order ID')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('booking.id')
                            ->label('Booking ID')
                            ->formatStateUsing(fn ($state) => "#{$state}")
                            ->url(fn ($record) => BookingResource::getUrl('edit', ['record' => $record->booking_id]))
                            ->color('primary'),
                        Infolists\Components\TextEntry::make('booking.user.name')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('booking.package.name')
                            ->label('Package'),
                    ])->columns(2),

                Infolists\Components\Section::make('Payment Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('gross_amount')
                            ->label('Amount')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('payment_type')
                            ->label('Payment Method')
                            ->placeholder('Not selected')
                            ->badge(),
                        Infolists\Components\TextEntry::make('transaction_status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'expired' => 'gray',
                                'superseded' => 'info',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('status_code')
                            ->label('Status Code')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('fraud_status')
                            ->label('Fraud Status')
                            ->placeholder('-')
                            ->badge()
                            ->color(fn (?string $state): string => match ($state) {
                                'accept' => 'success',
                                'challenge' => 'warning',
                                'deny' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(3),

                Infolists\Components\Section::make('Timestamps')
                    ->schema([
                        Infolists\Components\TextEntry::make('transaction_time')
                            ->label('Transaction Time')
                            ->dateTime('d M Y H:i:s')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('settlement_time')
                            ->label('Settlement Time')
                            ->dateTime('d M Y H:i:s')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('expiry_time')
                            ->label('Expiry Time')
                            ->dateTime('d M Y H:i:s')
                            ->placeholder('-'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i:s'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d M Y H:i:s'),
                    ])->columns(3),

                Infolists\Components\Section::make('Raw Notification Data')
                    ->schema([
                        Infolists\Components\TextEntry::make('raw_notification')
                            ->label('')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'No notification data')
                            ->prose()
                            ->markdown(false),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Transactions are created through payment flow
    }

    public static function canDelete($record): bool
    {
        return false; // Transactions should not be deleted
    }
}
