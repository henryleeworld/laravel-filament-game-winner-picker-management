<?php

namespace App\Filament\Resources\GameResource\Pages;

use App\Filament\Resources\GameResource;
use App\Models\Player;
use App\Models\Result;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class GameWinners extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = GameResource::class;

    protected static string $view = 'filament.resources.game-resource.pages.game-winners';

    public $data = [];

    /** @return Forms\Components\Component[] */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Repeater::make('players')
                ->schema([
                    Forms\Components\Select::make('name')
                        ->label(__('Name'))
                        ->options(function (callable $get) {
                            $players = $get('../../players');
                            $idsAlreadyUsed = [];

                            foreach($players as $repeater) {
                                if(! in_array($repeater['name'], $idsAlreadyUsed)
                                    && $repeater['name'] !== null
                                    && $repeater['name'] != $get('name')) {
                                    $idsAlreadyUsed[] = $repeater['name'];
                                }
                            }

                            return Player::whereNotIn('id', $idsAlreadyUsed)->pluck('name', 'id')->toArray();
                        })
                        ->reactive()
                        ->required()
                ])
                ->disableLabel()
                ->defaultItems(10)
                ->disableItemCreation()
                ->disableItemDeletion()
                ->disableItemMovement()
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    public function getTitle(): string
    {
        return __('Game Winners');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function submit()
    {
        $gameId = explode('/', parse_url(url()->previous(), PHP_URL_PATH))[3];
        $results = [];
        $players = $this->form->getState()['players'];

        foreach ($players as $key => $item) {
            $results[] = [
                'user_id' => auth()->id(),
                'game_id' => $gameId,
                'position' => $key + 1,
                'player_id' => $item['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Result::insert($results);

        Notification::make()
            ->title(__('Winners has been set'))
            ->success()
            ->send();

        $this->redirect(GameResource::getUrl());
    }
}
