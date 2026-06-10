<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DestinationResource extends Resource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Price per person for this destination'),
                Forms\Components\TextInput::make('lat')
                    ->label('Latitude')
                    ->numeric()
                    ->placeholder('-7.797068'),
                Forms\Components\TextInput::make('long')
                    ->label('Longitude')
                    ->numeric()
                    ->placeholder('110.370529'),
                Forms\Components\TextInput::make('avg_duration')
                    ->label('Average Duration (minutes)')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(60)
                    ->suffix('minutes'),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Destination Image')
                    ->image()
                    ->imageEditor()
                    ->directory('destinations/images')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(3072) // 3MB max
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->helperText('Upload image (max 3MB, format: JPG, PNG, WEBP)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->size(60),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('avg_duration')
                    ->label('Duration')
                    ->numeric()
                    ->sortable()
                    ->suffix(' min'),
                Tables\Columns\TextColumn::make('lat')
                    ->label('Latitude')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('long')
                    ->label('Longitude')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
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
            'index' => Pages\ListDestinations::route('/'),
            'create' => Pages\CreateDestination::route('/create'),
            'edit' => Pages\EditDestination::route('/{record}/edit'),
        ];
    }
}
