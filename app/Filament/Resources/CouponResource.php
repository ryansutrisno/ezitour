<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'Kode Promo';

    protected static ?string $pluralModelLabel = 'Kode Promo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Promo')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Promo')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Huruf besar otomatis. Contoh: LIBURAN50')
                            ->formatStateUsing(fn ($state): ?string => $state ? strtoupper($state) : null)
                            ->dehydrateStateUsing(fn ($state): ?string => $state ? strtoupper($state) : null),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Diskon')
                            ->required()
                            ->options([
                                'percentage' => 'Persentase (%)',
                                'fixed' => 'Nominal Tetap (Rp)',
                            ])
                            ->live(),
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix(fn (Forms\Get $get): string => $get('type') === 'percentage' ? '%' : '')
                            ->prefix(fn (Forms\Get $get): string => $get('type') === 'fixed' ? 'Rp' : '')
                            ->helperText(fn (Forms\Get $get): ?string => $get('type') === 'percentage' ? 'Persentase diskon (0-100).' : 'Nominal diskon tetap dalam Rupiah.'),
                        Forms\Components\TextInput::make('min_spend')
                            ->label('Minimal Belanja')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->helperText('Kosongkan untuk tanpa minimum.'),
                        Forms\Components\TextInput::make('max_discount')
                            ->label('Maksimum Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->nullable()
                            ->helperText('Hanya untuk tipe Persentase. Batas maksimum diskon.'),
                    ])->columns(2),
                Forms\Components\Section::make('Batas Penggunaan')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit_per_coupon')
                            ->label('Batas Total Penggunaan')
                            ->numeric()
                            ->nullable()
                            ->helperText('Kosongkan untuk tanpa batas.'),
                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label('Batas per Pengguna')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])->columns(2),
                Forms\Components\Section::make('Periode Berlaku')
                    ->schema([
                        Forms\Components\DateTimePicker::make('valid_from')
                            ->label('Berlaku Dari')
                            ->nullable(),
                        Forms\Components\DateTimePicker::make('valid_until')
                            ->label('Berlaku Sampai')
                            ->nullable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'percentage' ? 'Persentase' : 'Tetap')
                    ->color(fn (string $state): string => $state === 'percentage' ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->formatStateUsing(fn (Coupon $record): string => $record->type === 'percentage'
                        ? $record->value.'%'
                        : 'Rp '.number_format((float) $record->value, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage')
                    ->label('Penggunaan')
                    ->formatStateUsing(fn (Coupon $record): string => $record->usage_limit_per_coupon
                        ? "{$record->times_used} / {$record->usage_limit_per_coupon}"
                        : "{$record->times_used} / ∞"),
                Tables\Columns\TextColumn::make('validity')
                    ->label('Periode')
                    ->formatStateUsing(function (Coupon $record): string {
                        if (! $record->valid_from && ! $record->valid_until) {
                            return 'Selamanya';
                        }

                        $from = $record->valid_from?->format('d M Y') ?? '...';
                        $until = $record->valid_until?->format('d M Y') ?? '...';

                        return "{$from} — {$until}";
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal Tetap',
                    ]),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make()
                    ->label('Duplikat')
                    ->excludeAttributes(['code', 'times_used'])
                    ->beforeReplica(function (Coupon $replica): void {
                        $replica->code = $replica->code.'-COPY';
                        $replica->times_used = 0;
                    }),
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
