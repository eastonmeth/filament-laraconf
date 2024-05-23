<?php

namespace App\Livewire;

use App\Models\Attendee;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ConferenceSignupPage extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    public int $conferenceId;

    public int $price = 50000;

    public function mount()
    {
        $this->conferenceId = 1;
    }

    public function signUpAction(): Action
    {
        return Action::make('signUp')
            ->slideOver()
            ->form([
                Placeholder::make('total_price')
                    ->hiddenLabel()
                    ->content(function (Get $get): string {
                        return '$'.count($get('attendees')) * 500;
                    }),
                Repeater::make('attendees')
                    ->schema(Attendee::getForm()),
            ])
            ->action(function (array $data): void {
                collect($data['attendees'])->each(function (array $data): void {
                    Attendee::create([
                        'conference_id' => $this->conferenceId,
                        'ticket_cost' => $this->price,
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'is_paid' => true,
                    ]);
                });
            })
            ->after(function (): void {
                Notification::make()
                    ->success()
                    ->title('Success!')
                    ->body(new HtmlString('You have successfully signed up for the conference!'))
                    ->send();
            });
    }

    public function render()
    {
        return view('livewire.conference-signup-page');
    }
}
