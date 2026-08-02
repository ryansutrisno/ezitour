<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Ulasan';

    protected static ?string $pluralModelLabel = 'Ulasan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Ulasan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pengguna')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabledOn('edit'),
                        Forms\Components\Select::make('package_id')
                            ->label('Paket')
                            ->relationship('package', 'name')
                            ->searchable()
                            ->required()
                            ->disabledOn('edit'),
                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->required()
                            ->options([
                                '5' => '5 — Sangat Puas',
                                '4' => '4 — Puas',
                                '3' => '3 — Cukup',
                                '2' => '2 — Kurang',
                                '1' => '1 — Kecewa',
                            ]),
                        Forms\Components\Textarea::make('comment')
                            ->label('Komentar')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Status Moderasi')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved')
                            ->label('Tampil di situs publik')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('package.name')
                    ->label('Paket')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (Review $record): string => str_repeat('★', $record->rating).str_repeat('☆', 5 - $record->rating))
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Komentar')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        '5' => '5 bintang',
                        '4' => '4 bintang',
                        '3' => '3 bintang',
                        '2' => '2 bintang',
                        '1' => '1 bintang',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approved')
                    ->label('Disetujui')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record): bool => ! $record->is_approved)
                    ->action(fn (Review $record) => $record->update(['is_approved' => true]))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('unpublish')
                    ->label('Cabut')
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->visible(fn (Review $record): bool => $record->is_approved)
                    ->action(fn (Review $record) => $record->update(['is_approved' => false]))
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
