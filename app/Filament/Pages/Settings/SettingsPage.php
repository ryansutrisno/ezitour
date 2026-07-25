<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Spatie\LaravelSettings\Settings;

/**
 * Base class for Filament settings pages backed by spatie/laravel-settings.
 *
 * Filament v3 no longer ships a `Filament\Pages\SettingsPage` base class, so we
 * provide our own that wires a Livewire form to a {@see Settings} class: the
 * form is populated from the settings on mount, and submitting it persists the
 * values back through the settings repository.
 */
abstract class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.settings';

    protected static ?string $navigationGroup = 'Pengaturan';

    /** @var array<string, mixed> */
    public ?array $data = [];

    /**
     * Fully-qualified class name of the backing {@see Settings} class.
     *
     * @return class-string<Settings>
     */
    abstract protected static function settings(): string;

    /**
     * Form schema rendered on the page.
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    abstract protected function formSchema(): array;

    public function mount(): void
    {
        $this->form->fill(app(static::settings())->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->formSchema())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = app(static::settings());
        $settings->fill($data);
        $settings->save();

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
