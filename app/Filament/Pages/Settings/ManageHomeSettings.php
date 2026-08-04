<?php

namespace App\Filament\Pages\Settings;

use App\Settings\HomeSettings;
use Filament\Forms;
use Spatie\LaravelSettings\Settings;

class ManageHomeSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Halaman Beranda';

    protected static ?int $navigationSort = 2;

    /** @return class-string<Settings> */
    protected static function settings(): string
    {
        return HomeSettings::class;
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Bagian Hero')
                ->description('Konten utama di bagian atas halaman beranda.')
                ->schema([
                    Forms\Components\TextInput::make('heroBadge')
                        ->label('Teks Lencana Hero')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Teks kecil di atas judul (contoh: Travel partner tepercaya sejak 2019).'),
                    Forms\Components\TextInput::make('heroHeadline')
                        ->label('Judul Utama')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Bagian pertama judul (warna standar).'),
                    Forms\Components\TextInput::make('heroHeadlineAccent')
                        ->label('Aksen Judul')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Bagian kedua judul (ditampilkan dengan gradasi warna).'),
                    Forms\Components\Textarea::make('heroSubheadline')
                        ->label('Sub Judul')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('heroBadge_en')
                        ->label('Teks Lencana Hero (English)')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('heroHeadline_en')
                        ->label('Judul Utama (English)')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('heroHeadlineAccent_en')
                        ->label('Aksen Judul (English)')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('heroSubheadline_en')
                        ->label('Sub Judul (English)')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Forms\Components\Section::make('Statistik')
                ->description('Angka-angka trust yang tampil di bawah hero.')
                ->schema([
                    Forms\Components\TextInput::make('statDestinations')
                        ->label('Destinasi Wisata')
                        ->required()
                        ->maxLength(32)
                        ->helperText('Contoh: 500+'),
                    Forms\Components\TextInput::make('statTravelers')
                        ->label('Traveler Puas')
                        ->required()
                        ->maxLength(32)
                        ->helperText('Contoh: 10K+'),
                    Forms\Components\TextInput::make('statRating')
                        ->label('Rating Rata-rata')
                        ->required()
                        ->maxLength(32)
                        ->helperText('Contoh: 4.9'),
                    Forms\Components\TextInput::make('statSupport')
                        ->label('Dukungan Pelanggan')
                        ->required()
                        ->maxLength(32)
                        ->helperText('Contoh: 24/7'),
                ])
                ->columns(4),
        ];
    }
}
