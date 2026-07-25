<?php

namespace App\Filament\Pages\Settings;

use App\Settings\AboutSettings;
use Filament\Forms;
use Spatie\LaravelSettings\Settings;

class ManageAboutSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'Halaman Tentang';

    protected static ?int $navigationSort = 4;

    /** @return class-string<Settings> */
    protected static function settings(): string
    {
        return AboutSettings::class;
    }

    protected function formSchema(): array
    {
        return [
            Forms\Components\Section::make('Statistik Mini')
                ->description('Angka ringkas di bagian "Cerita Kami".')
                ->schema([
                    Forms\Components\TextInput::make('foundedYear')
                        ->label('Tahun Berdiri')
                        ->required()
                        ->maxLength(8)
                        ->helperText('Contoh: 2019'),
                    Forms\Components\TextInput::make('provincesCovered')
                        ->label('Provinsi Terjangkau')
                        ->required()
                        ->maxLength(8)
                        ->helperText('Contoh: 34'),
                    Forms\Components\TextInput::make('partnersCount')
                        ->label('Jumlah Mitra Lokal')
                        ->required()
                        ->maxLength(8)
                        ->helperText('Contoh: 200+'),
                ])
                ->columns(3),
            Forms\Components\Section::make('Visi & Misi')
                ->description('Teks visi dan poin-poin misi perusahaan.')
                ->schema([
                    Forms\Components\Textarea::make('visionText')
                        ->label('Teks Visi')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('missionPoints')
                        ->label('Poin Misi')
                        ->schema([
                            Forms\Components\Textarea::make('point')
                                ->label('Poin')
                                ->required()
                                ->rows(2),
                        ])
                        ->addable()
                        ->reorderable()
                        ->defaultItems(3)
                        ->columnSpanFull()
                        ->helperText('Setiap poin akan tampil sebagai item daftar dengan ikon centang.'),
                ]),
        ];
    }
}
