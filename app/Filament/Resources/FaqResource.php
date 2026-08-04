<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $modelLabel = 'FAQ';

    protected static ?string $pluralModelLabel = 'FAQ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail FAQ')
                    ->schema([
                        Forms\Components\Tabs::make('Terjemahan FAQ')
                            ->tabs([
                                Tab::make('Indonesia')
                                    ->schema([
                                        Forms\Components\TextInput::make('question.id')
                                            ->label('Pertanyaan (ID)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('answer.id')
                                            ->label('Jawaban (ID)')
                                            ->required()
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                                Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('question.en')
                                            ->label('Question (EN)')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('answer.en')
                                            ->label('Answer (EN)')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'Pembayaran' => 'Pembayaran',
                                'Perjalanan' => 'Perjalanan',
                                'Pembatalan' => 'Pembatalan',
                                'Paket' => 'Paket',
                                'Umum' => 'Umum',
                            ])
                            ->placeholder('Pilih kategori (opsional)')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('value')
                                    ->required(),
                            ])
                            ->createOptionUsing(fn (array $data): string => $data['value']),
                    ]),
                Forms\Components\Section::make('Status & Urutan')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Tampil di situs')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->helperText('Angka lebih kecil tampil lebih dulu.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderRecords('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->sortable()
                    ->limit(70)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terpublikasi')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(fn (): array => Faq::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->all()),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Terpublikasi')
                    ->boolean(),
            ])
            ->actions([
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
