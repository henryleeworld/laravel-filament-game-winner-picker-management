<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required(),
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('game');
    }

    public static function getNavigationLabel(): string
    {
        return __('Game');
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListGames::route('/'),
            'create'  => Pages\CreateGame::route('/create'),
            'edit'    => Pages\EditGame::route('/{record}/edit'),
            'winners' => Pages\GameWinners::route('/{record}/winners'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name')),
            ])
            ->actions([
                Tables\Actions\Action::make('Pick winners')
                    ->label(__('Pick winners'))
                    ->color('success')
                    ->url(fn(Game $record): string => self::getUrl('winners', ['record' => $record]))
            ]);
    }
}
