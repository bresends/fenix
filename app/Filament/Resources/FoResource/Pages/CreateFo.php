<?php

namespace App\Filament\Resources\FoResource\Pages;

use App\Filament\Resources\FoResource;
use App\Models\Fo;
use App\Models\User;
use App\Notifications\NewFoNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateFo extends CreateRecord
{
    protected static string $resource = FoResource::class;

    public function createAnother(): void
    {
        session()->flash('dataFill', $this->data);
        parent::createAnother();
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCreateAnotherFormAction(),
            $this->getCancelFormAction(),
            Action::make('repeat')
                ->label('Preencher com último')
                ->action(function () {
                    $latestFo = Fo::latest()->first();
                    if ($latestFo) {
                        $this->form->fill([
                            'type' => $latestFo->type,
                            'issuer' => $latestFo->issuer,
                            'date_issued' => $latestFo->date_issued,
                            'reason' => $latestFo->reason,
                            'observation' => $latestFo->observation,
                        ]);

                        Notification::make()
                            ->title('Preenchido com os dados do último FO')
                            ->success()
                            ->send();
                    }
                })
                ->color('gray'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $fo = $this->record;

        $user = $fo->user;

        if ($user) {
            $user->notify(new NewFoNotification($fo));  // Pass the FO to the notification
        }
    }
}
