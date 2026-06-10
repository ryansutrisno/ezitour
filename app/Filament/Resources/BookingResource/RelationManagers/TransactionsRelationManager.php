<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use App\Filament\Resources\TransactionResource;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Payment Transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_id')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('order_id')
            ->columns([
                Tables\Columns\TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Amount')
                    ->money('IDR'),
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
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                // No create action - transactions are created through payment flow
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => TransactionResource::getUrl('view', ['record' => $record->id])),
                Tables\Actions\Action::make('checkStatus')
                    ->label('Check Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        try {
                            $paymentService = app(PaymentService::class);
                            $status = $paymentService->checkPaymentStatus($record->order_id);
                            
                            Notification::make()
                                ->title('Status Checked')
                                ->body("Current status: {$status['transaction_status']}")
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
                    ->visible(fn ($record) => $record->transaction_status === 'pending'),
            ])
            ->bulkActions([
                // No bulk actions for transactions
            ]);
    }
}
