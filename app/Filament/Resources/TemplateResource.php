<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Template Details')->schema([
                Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
                Forms\Components\Select::make('type')
                ->options([
                    'header' => 'Header',
                    'footer' => 'Footer',
                    'global' => 'Global Block (Other)',
                ])
                ->required(),
                Forms\Components\Toggle::make('is_active')
                ->label('Set as Active Wrapper')
                ->helperText('Only one header and footer should be active at a time.')
                ->default(false),
            ])->columns(3),
            \App\Filament\Helpers\BlockBuilderHelper::build(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\BadgeColumn::make('type')
            ->colors([
                'primary' => 'header',
                'success' => 'footer',
                'warning' => 'global',
            ]),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
        ])
            ->filters([
            //
        ])
            ->actions([
            Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
