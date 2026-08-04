<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Spatie\LaravelSettings\Settings;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Umum';

    protected static ?int $navigationSort = 1;

    /** @return class-string<Settings> */
    protected static function settings(): string
    {
        return GeneralSettings::class;
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Identitas Situs')
                ->description('Informasi dasar yang tampil di seluruh situs.')
                ->schema([
                    Forms\Components\TextInput::make('siteName')
                        ->label('Nama Situs')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Contoh: EziTour'),
                    Forms\Components\TextInput::make('tagline')
                        ->label('Tagline')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Slogan singkat situs.'),
                    Forms\Components\Textarea::make('footerTagline')
                        ->label('Tagline Footer')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull()
                        ->helperText('Paragraf singkat di bagian footer.'),
                    Forms\Components\Textarea::make('footerTagline_en')
                        ->label('Tagline Footer (English)')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
