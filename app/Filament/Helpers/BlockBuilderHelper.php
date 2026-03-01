<?php

namespace App\Filament\Helpers;

use Filament\Forms;

class BlockBuilderHelper
{
    public static function build(): Forms\Components\Builder
    {
        return Forms\Components\Builder::make('content')
            ->columnSpanFull()
            ->blocks([
            self::getNavBlock(),
            self::getHeroBlock(),
            self::getTextBlock(),
            self::getPortfolioBlock(),
            self::getContactBlock(),
        ]);
    }

    private static function getNavBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('nav')
            ->icon('heroicon-m-bars-3')
            ->schema([
            Forms\Components\Repeater::make('links')
            ->schema([
                Forms\Components\TextInput::make('label')->required(),
                Forms\Components\TextInput::make('url')->required(),
            ])
            ->collapsible(),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getHeroBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('hero')
            ->icon('heroicon-m-sparkles')
            ->schema([
            Forms\Components\TextInput::make('heading')->required(),
            Forms\Components\Textarea::make('subheading'),
            Forms\Components\FileUpload::make('image')
            ->image()
            ->directory('pages/hero'),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getTextBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('text_content')
            ->icon('heroicon-m-document-text')
            ->schema([
            Forms\Components\RichEditor::make('text')->required(),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getPortfolioBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('portfolio_section')
            ->icon('heroicon-m-photo')
            ->schema([
            Forms\Components\TextInput::make('section_title'),
            Forms\Components\Repeater::make('items')
            ->schema([
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\FileUpload::make('thumbnail')->image()->required(),
                Forms\Components\TextInput::make('link_url')->url(),
            ])
            ->collapsible(),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getContactBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('contact_form')
            ->icon('heroicon-m-envelope')
            ->schema([
            Forms\Components\TextInput::make('heading')->default('Contact Us'),
            Forms\Components\Textarea::make('description'),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    public static function getAppearanceSchema(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Appearance Settings (Elementor-like)')
            ->schema([
            Forms\Components\Grid::make(4)->schema([
                Forms\Components\TextInput::make('margin_top')->label('Margin T')->placeholder('e.g. 10px or 2rem'),
                Forms\Components\TextInput::make('margin_bottom')->label('Margin B'),
                Forms\Components\TextInput::make('padding_top')->label('Padding T'),
                Forms\Components\TextInput::make('padding_bottom')->label('Padding B'),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\ColorPicker::make('background_color'),
                Forms\Components\ColorPicker::make('text_color'),
            ]),
            Forms\Components\TextInput::make('custom_css_classes')
            ->label('Custom CSS Classes')
            ->placeholder('e.g. shadow-lg rounded-xl flex items-center'),
        ])
            ->collapsed();
    }

    public static function getAnimationSchema(): Forms\Components\Section
    {
        return Forms\Components\Section::make('GSAP Animation Settings')
            ->schema([
            Forms\Components\Select::make('animation_type')
            ->options([
                'none' => 'None',
                'fade' => 'Fade In',
                'slide_up' => 'Slide Up',
                'slide_left' => 'Slide Left',
                'slide_right' => 'Slide Right',
                'scale_up' => 'Scale Up',
            ])
            ->default('none')
            ->required(),
            Forms\Components\TextInput::make('duration')
            ->numeric()
            ->default(1.0)
            ->step(0.1)
            ->required(),
            Forms\Components\TextInput::make('delay')
            ->numeric()
            ->default(0)
            ->step(0.1)
            ->required(),
            Forms\Components\Select::make('easing')
            ->options([
                'power1.out' => 'Power1 Out',
                'power2.out' => 'Power2 Out',
                'power3.out' => 'Power3 Out',
                'power4.out' => 'Power4 Out',
                'back.out(1.7)' => 'Back Out',
                'elastic.out(1, 0.3)' => 'Elastic Out',
                'bounce.out' => 'Bounce Out',
                'none' => 'Linear',
            ])
            ->default('power2.out')
            ->required(),
            Forms\Components\Toggle::make('scroll_trigger')
            ->label('Trigger on Scroll')
            ->default(true),
        ])
            ->collapsed();
    }
}
