<?php

namespace App\Filament\Pages\Settings;

use App\Settings\ContactSettings;
use Filament\Forms;
use Spatie\LaravelSettings\Settings;

class ManageContactSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationLabel = 'Kontak';

    protected static ?int $navigationSort = 3;

    /** @return class-string<Settings> */
    protected static function settings(): string
    {
        return ContactSettings::class;
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Kontak Utama')
                ->description('Informasi kontak yang ditampilkan di footer dan halaman kontak.')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->required()
                        ->email()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->required()
                        ->maxLength(64)
                        ->helperText('Contoh: +62 812 3456 7890'),
                    Forms\Components\TextInput::make('whatsapp')
                        ->label('Nomor WhatsApp')
                        ->tel()
                        ->maxLength(64)
                        ->nullable()
                        ->helperText('Opsional. Format internasional tanpa tanda kurung.'),
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->rows(2)
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Forms\Components\Section::make('Media Sosial')
                ->description('Tautan profil media sosial. Biarkan kosong bila belum tersedia.')
                ->schema([
                    Forms\Components\TextInput::make('instagramUrl')
                        ->label('URL Instagram')
                        ->url()
                        ->nullable()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-camera'),
                    Forms\Components\TextInput::make('facebookUrl')
                        ->label('URL Facebook')
                        ->url()
                        ->nullable()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-users'),
                    Forms\Components\TextInput::make('twitterUrl')
                        ->label('URL Twitter / X')
                        ->url()
                        ->nullable()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-at-symbol'),
                ])
                ->columns(1),
        ];
    }
}
