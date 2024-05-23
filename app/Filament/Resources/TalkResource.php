<?php

namespace App\Filament\Resources;

use App\Enums\TalkLength;
use App\Enums\TalkStatus;
use App\Filament\Resources\TalkResource\Pages;
use App\Filament\Resources\TalkResource\Pages\ListTalks;
use App\Models\Talk;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class TalkResource extends Resource
{
    protected static ?string $model = Talk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema(Talk::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistFiltersInSession()
            ->filtersTriggerAction(function (Action $action) {
                return $action->button()->label('Filters');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->description(function (Talk $record): string {
                        return Str::of($record->abstract)->limit(50);
                    }),
                Tables\Columns\ImageColumn::make('speaker.avatar')
                    ->label('Speaker Avatar')
                    ->circular()
                    ->defaultImageUrl(function (Talk $record): string {
                        return 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name='.urlencode($record->speaker->name);
                    }),
                Tables\Columns\TextColumn::make('speaker.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('new_talk')->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(function (TalkStatus $state): string {
                        return $state->getColor();
                    }),
                Tables\Columns\IconColumn::make('length')
                    ->icon(function (TalkLength $state): string {
                        return match ($state) {
                            TalkLength::LIGHTNING => 'heroicon-o-bolt',
                            TalkLength::NORMAL => 'heroicon-o-megaphone',
                            TalkLength::KEYNOTE => 'heroicon-o-key',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('new_talk'),
                Tables\Filters\SelectFilter::make('speaker')
                    ->relationship('speaker', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('has_avatar')
                    ->label('Only Show Speakers With Avatars')
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('speaker', function (Builder $query): void {
                            $query->whereNotNull('avatar');
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->visible(function (Talk $record): bool {
                            return $record->status !== TalkStatus::APPROVED;
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Talk $record): void {
                            $record->approve();
                        })->after(function (): void {
                            Notification::make()
                                ->success()
                                ->title('This talk was approved.')
                                ->body('The speaker has been notified and the talk has been added to the conference schedule.')
                                ->send();
                        }),
                    Tables\Actions\Action::make('reject')
                        ->visible(function (Talk $record): bool {
                            return $record->status !== TalkStatus::REJECTED;
                        })
                        ->requiresConfirmation()
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->action(function (Talk $record): void {
                            $record->reject();
                        })->after(function (): void {
                            Notification::make()
                                ->danger()
                                ->title('This talk was rejected.')
                                ->body('The speaker has been notified and the talk has been rejected.')
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->approve();
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->tooltip('This will export all records currently visible in the table. Your selected filters will affect what is exported.')
                    ->action(function (ListTalks $livewire): void {
                        dd($livewire->getFilteredTableQuery());
                    }),
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
            'index' => Pages\ListTalks::route('/'),
            'create' => Pages\CreateTalk::route('/create'),
            // 'edit' => Pages\EditTalk::route('/{record}/edit'),
        ];
    }
}
