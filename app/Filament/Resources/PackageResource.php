<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Terjemahan Paket')
                    ->tabs([
                        Tab::make('Indonesia')
                            ->schema([
                                Forms\Components\TextInput::make('name.id')
                                    ->label('Nama Paket (ID)')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null
                                    ),
                                Forms\Components\Textarea::make('description.id')
                                    ->label('Deskripsi (ID)')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('Package Name (EN)')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description.en')
                                    ->label('Description (EN)')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('URL-friendly version of the name'),
                Forms\Components\TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0),
                Forms\Components\Section::make('Tier Harga Khusus')
                    ->description('Kosongkan jika paket hanya punya harga tunggal. Tier akan otomatis diterapkan saat jumlah peserta melewati batas minimum.')
                    ->schema([
                        Forms\Components\Repeater::make('priceTiers')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Tier')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('contoh: Promo Rombongan 10+ pax')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('min_pax')
                                    ->label('Min. Peserta')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1),
                                Forms\Components\TextInput::make('max_pax')
                                    ->label('Maks. Peserta')
                                    ->numeric()
                                    ->nullable()
                                    ->minValue(1)
                                    ->helperText('Kosongkan untuk tanpa batas atas (open-ended, contoh: "20+ pax")'),
                                Forms\Components\TextInput::make('price_per_pax')
                                    ->label('Harga per Pax')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp')
                                    ->minValue(0),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->orderable('sort_order')
                            ->defaultItems(0)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Forms\Components\Section::make('Klasifikasi Paket')
                    ->description('Digunakan untuk filter faceted search di halaman publik paket wisata.')
                    ->schema([
                        Forms\Components\Select::make('region')
                            ->label('Wilayah')
                            ->options(fn (): array => Package::query()->whereNotNull('region')->whereNot('region', '')->distinct()->orderBy('region')->pluck('region', 'region')->all())
                            ->searchable()
                            ->placeholder('Pilih wilayah (opsional)')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('value')
                                    ->required(),
                            ])
                            ->createOptionUsing(fn (array $data): string => $data['value'])
                            ->columnSpan(1),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options(fn (): array => Package::query()->whereNotNull('category')->whereNot('category', '')->distinct()->orderBy('category')->pluck('category', 'category')->all())
                            ->searchable()
                            ->placeholder('Pilih kategori (opsional)')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('value')
                                    ->required(),
                            ])
                            ->createOptionUsing(fn (array $data): string => $data['value'])
                            ->columnSpan(1),
                        Forms\Components\Select::make('duration_days')
                            ->label('Durasi (hari)')
                            ->options(array_combine(range(1, 14), range(1, 14)))
                            ->placeholder('Pilih durasi (opsional)')
                            ->helperText('Total hari perjalanan (1-14).'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Forms\Components\Section::make('Thumbnail Image')
                    ->description('Upload an image or provide an image URL')
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail_url')
                            ->label('Upload Thumbnail')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                            ])
                            ->directory('packages/thumbnails')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(3072) // 3MB max
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                            ->helperText('Upload image (max 3MB) OR leave empty and use URL below')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('thumbnail_url_external')
                            ->label('Or use External URL')
                            ->url()
                            ->placeholder('https://example.com/image.jpg')
                            ->helperText('If you prefer to use an external image URL (for development/seeding)')
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // If external URL is provided and no file uploaded, use the URL
                                if ($state && ! $get('thumbnail_url')) {
                                    $set('thumbnail_url', $state);
                                }
                            }),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('destination_id')
                            ->relationship('destination', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('sequence_order')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0),
                    ])
                    ->orderColumn('sequence_order')
                    ->defaultItems(1)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->size(80)
                    ->defaultImageUrl(url('/images/default-package.png')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Package $record): string => collect([
                        $record->region,
                        $record->category,
                        $record->duration_days ? $record->duration_days.' hari' : null,
                    ])->filter()->join(' • ')),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Destinations')
                    ->counts('items')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('region')
                    ->label('Wilayah')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('duration_days')
                    ->label('Durasi')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state): ?string => filled($state) ? $state.' hari' : null)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('region')
                    ->label('Wilayah')
                    ->options(fn (): array => Package::query()->whereNotNull('region')->whereNot('region', '')->distinct()->orderBy('region')->pluck('region', 'region')->all()),
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn (): array => Package::query()->whereNotNull('category')->whereNot('category', '')->distinct()->orderBy('category')->pluck('category', 'category')->all()),
                Tables\Filters\Filter::make('duration_days')
                    ->form([
                        Forms\Components\Select::make('bucket')
                            ->label('Durasi')
                            ->options([
                                '1-3' => '1-3 hari',
                                '4-7' => '4-7 hari',
                                '8-14' => '8+ hari',
                            ])
                            ->placeholder('Semua'),
                    ])
                    ->query(function (Tables\Filters\Filter $filter, Builder $query): Builder {
                        $bucket = $filter->getState()['bucket'] ?? null;
                        if (! $bucket) {
                            return $query;
                        }
                        [$min, $max] = array_pad(explode('-', (string) $bucket), 2, null);

                        return $query->when(filled($max), fn (Builder $q) => $q->whereBetween('duration_days', [(int) $min, (int) $max]))
                            ->when(! filled($max), fn (Builder $q) => $q->where('duration_days', '>=', (int) $min));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPackages::route('/'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}
