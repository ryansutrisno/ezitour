<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\Pages;
use App\Filament\Resources\CarResource\RelationManagers;
use App\Models\Car;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->placeholder('e.g., SUV, Sedan, MPV'),
                Forms\Components\TextInput::make('license_plate')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50)
                    ->suffix('passengers'),
                Forms\Components\TextInput::make('base_price_per_day')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(0),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Car Image')
                    ->image()
                    ->imageEditor()
                    ->directory('cars/images')
                    ->disk('public')
                    ->visibility('public')
                    ->maxSize(2048) // 2MB max
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                    ->helperText('Upload foto mobil (max 2MB, format: JPG, PNG, WEBP)')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image')
                    ->size(60)
                    ->defaultImageUrl(url('/images/default-car.png')),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('license_plate')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->sortable()
                    ->suffix(' passengers'),
                Tables\Columns\TextColumn::make('base_price_per_day')
                    ->label('Price/Day')
                    ->money('IDR')
                    ->sortable(),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
