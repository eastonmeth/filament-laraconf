<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendeeResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class TestChart extends Widget implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.test-chart';

    public function callNotification(): Action
    {
        return Action::make('callNotification')
            ->button()
            ->color('warning')
            ->label('Send a notification')
            ->action(function (): void {
                Notification::make()
                    ->warning()
                    ->title('You sent a notification!')
                    ->body('This is a test.')
                    ->persistent()
                    ->actions([
                        NotificationAction::make('goToAttendees')
                            ->button()
                            ->color('primary')
                            ->url(AttendeeResource::getUrl('index')),
                        NotificationAction::make('undo')
                            ->link()
                            ->color('gray')
                            ->action(function (): void {
                                dd('hi!');
                            }),
                    ])
                    ->send();
            });
    }
}
