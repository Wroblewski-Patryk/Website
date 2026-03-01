<?php

namespace App\Filament\Helpers;

use Filament\Forms;

class BlockBuilderHelper
{
    public static function build(): Forms\Components\Builder
    {
        return Forms\Components\Builder::make('content')
            ->columnSpanFull()
            ->blocks(self::getAllBlocks())
            ->collapsible();
    }

    public static function getAllBlocks(): array
    {
        return [
            // Structural
            self::getColumnsBlock(),
            self::getSectionBlock(),
            self::getSpacingBlock(),

            // Content
            self::getHeadingBlock(),
            self::getTextBlock(),
            self::getImageBlock(),
            self::getButtonBlock(),

            // Legacy / Niche
            self::getNavBlock(),
            self::getHeroBlock(),
            self::getPortfolioBlock(),
            self::getContactBlock(),
        ];
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

    private static function getHeadingBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('heading')
            ->icon('heroicon-m-h1')
            ->schema([
            Forms\Components\TextInput::make('text')->required(),
            Forms\Components\Select::make('level')
            ->options([
                'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6'
            ])->default('h2')->required(),
            Forms\Components\Select::make('alignment')
            ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
            ->default('left'),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getImageBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('image_block')
            ->icon('heroicon-m-photo')
            ->schema([
            Forms\Components\FileUpload::make('image')->image()->required(),
            Forms\Components\TextInput::make('alt_text'),
            Forms\Components\Select::make('object_fit')
            ->options(['cover' => 'Cover', 'contain' => 'Contain', 'fill' => 'Fill'])
            ->default('cover'),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getButtonBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('button_block')
            ->icon('heroicon-m-cursor-arrow-rays')
            ->schema([
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\TextInput::make('url')->url()->required(),
            Forms\Components\Select::make('style')
            ->options(['primary' => 'Primary', 'secondary' => 'Secondary', 'outline' => 'Outline'])
            ->default('primary'),
            Forms\Components\Select::make('alignment')
            ->options(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'])
            ->default('left'),
            self::getAppearanceSchema(),
            self::getAnimationSchema(),
        ]);
    }

    private static function getSpacingBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('spacing_block')
            ->icon('heroicon-m-arrows-up-down')
            ->schema([
            Forms\Components\TextInput::make('height')
            ->label('Height (e.g. 50px, 4rem, 10vh)')
            ->required()
            ->default('50px'),
            Forms\Components\Toggle::make('show_divider')->default(false),
        ]);
    }

    private static function getColumnsBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('grid_columns')
            ->icon('heroicon-m-view-columns')
            ->schema([
            Forms\Components\Select::make('columns')
            ->options(['1' => '1 Column', '2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns'])
            ->default('2')->required(),
            Forms\Components\Select::make('gap')
            ->options(['none' => 'None', 'small' => 'Small', 'medium' => 'Medium', 'large' => 'Large'])
            ->default('medium'),
            Forms\Components\Builder::make('column_content')
            ->label('Column Content Blocks')
            ->blocks([
                self::getHeadingBlock(),
                self::getTextBlock(),
                self::getImageBlock(),
                self::getButtonBlock()
            ]),
            self::getAppearanceSchema(),
        ]);
    }

    private static function getSectionBlock(): Forms\Components\Builder\Block
    {
        return Forms\Components\Builder\Block::make('section_wrapper')
            ->icon('heroicon-m-stop')
            ->label('Section (Container)')
            ->schema([
            Forms\Components\Toggle::make('full_width')->label('Full Width Background')->default(true),
            Forms\Components\Builder::make('content')
            ->label('Inside Section Content')
            ->blocks([
                self::getColumnsBlock(),
                self::getHeadingBlock(),
                self::getTextBlock(),
                self::getImageBlock(),
                self::getButtonBlock(),
                self::getSpacingBlock()
            ]),
            self::getAppearanceSchema(),
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
