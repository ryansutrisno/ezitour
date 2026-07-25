<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Services\PaymentService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('checkStatus')
                ->label('Check Status from Midtrans')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Check Payment Status')
                ->modalDescription('This will check the current payment status from Midtrans API and update the local record if needed.')
                ->action(function () {
                    try {
                        $paymentService = app(PaymentService::class);
                        $oldStatus = $this->record->transaction_status;
                        $status = $paymentService->checkPaymentStatus($this->record->order_id);

                        // Refresh the record to show updated data
                        $this->record->refresh();
                        $newStatus = $this->record->transaction_status;

                        $midtransStatus = $status['transaction_status'] ?? 'unknown';
                        $message = $oldStatus !== $newStatus
                            ? "Status updated from '{$oldStatus}' to '{$newStatus}'"
                            : "Current status: {$midtransStatus} (no change)";

                        Notification::make()
                            ->title('Status Checked Successfully')
                            ->body($message)
                            ->success()
                            ->send();

                        // Redirect to refresh the page with new data
                        return redirect(TransactionResource::getUrl('view', ['record' => $this->record]));
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error Checking Status')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(fn () => in_array($this->record->transaction_status, ['pending', 'failed'])),
        ];
    }
}
