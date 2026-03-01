<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use App\Models\Page as PageModel;
use App\Models\Template as TemplateModel;
use App\Models\Setting;
use Filament\Notifications\Notification;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Ustawienia';
    protected static ?string $title = 'Ustawienia Globalne';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    protected function getSettingsKeys(): array
    {
        return [
            'home_page_slug' => 'home',
            'blog_page_slug' => 'blog',
            'global_header_id' => null,
            'global_footer_id' => null,
            'color_primary' => '#000000',
            'color_secondary' => '#ffffff',
            'font_heading' => 'Titillium Web',
            'font_body' => 'Titillium Web',
        ];
    }

    public function mount(): void
    {
        $settings = Setting::whereIn('key', array_keys($this->getSettingsKeys()))
            ->pluck('value', 'key')
            ->toArray();

        $fillData = [];
        foreach ($this->getSettingsKeys() as $key => $default) {
            $fillData[$key] = $settings[$key] ?? $default;
        }

        $this->form->fill($fillData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            Tabs::make('Settings')
            ->tabs([
                Tabs\Tab::make('Strona główna i Blog')
                ->schema([
                    Select::make('home_page_slug')
                    ->label('Strona główna (Wybierz z listy)')
                    ->options(PageModel::pluck('title', 'slug'))
                    ->required(),
                    Select::make('blog_page_slug')
                    ->label('Strona z artykułami (Wybierz z listy)')
                    ->options(PageModel::pluck('title', 'slug'))
                    ->required(),
                ]),
                Tabs\Tab::make('Szablony Główne')
                ->schema([
                    Select::make('global_header_id')
                    ->label('Domyślny Nagłówek (Header)')
                    ->options(TemplateModel::where('type', 'header')->pluck('name', 'id'))
                    ->nullable(),
                    Select::make('global_footer_id')
                    ->label('Domyślna Stopka (Footer)')
                    ->options(TemplateModel::where('type', 'footer')->pluck('name', 'id'))
                    ->nullable(),
                ]),
                Tabs\Tab::make('Personalizacja (Brand)')
                ->schema([
                    ColorPicker::make('color_primary')->label('Kolor Główny (Primary)'),
                    ColorPicker::make('color_secondary')->label('Kolor Dodatkowy (Secondary)'),
                    TextInput::make('font_heading')->label('Font Nagłówków (np. Inter)'),
                    TextInput::make('font_body')->label('Font Tekstu Główny (np. Roboto)'),
                ])->columns(2),
            ])->columnSpanFull()
        ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($this->getSettingsKeys() as $key => $default) {
            if (isset($data[$key])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $data[$key]]);
            }
        }

        Notification::make()
            ->title('Zapisano ustawienia')
            ->success()
            ->send();
    }
}
