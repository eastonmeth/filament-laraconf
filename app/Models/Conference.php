<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Wizard::make()
                ->columnSpanFull()
                ->steps([
                    Step::make('Conference Details')
                        ->schema([
                            TextInput::make('name')
                                ->columnSpanFull()
                                ->label('Conference')
                                ->default('My Conference')
                                ->required()
                                ->maxLength(60),
                            MarkdownEditor::make('description')
                                ->columnSpanFull()
                                ->required(),
                            DateTimePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            DateTimePicker::make('end_date')
                                ->native(false)
                                ->required(),
                            Fieldset::make('Status')
                                ->columns(1)
                                ->schema([
                                    Select::make('status')
                                        ->options([
                                            'draft' => 'Draft',
                                            'published' => 'Published',
                                            'archived' => 'Archived',
                                        ])
                                        ->required(),
                                    Toggle::make('is_published')
                                        ->default(true),
                                ]),
                        ]),
                    Step::make('Location')
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->enum(Region::class)
                                ->options(Region::class),
                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->createOptionForm(Venue::getForm())
                                ->editOptionForm(Venue::getForm())
                                ->relationship('venue', 'name', function (Builder $query, Get $get): Builder {
                                    return $query->where('region', $get('region'));
                                }),
                        ]),
                    Step::make('Speakers')
                        ->schema([
                            CheckboxList::make('speakers')
                                ->columnSpanFull()
                                ->searchable()
                                ->relationship('speakers', 'name')
                                ->options(Speaker::all()->pluck('name', 'id')),
                        ]),
                ]),
            Actions::make([
                Action::make('star')
                    ->label('Fill with Factory Data')
                    ->icon('heroicon-m-star')
                    ->visible(function (string $operation): bool {
                        if (! app()->environment('local') || $operation !== 'create') {
                            return false;
                        }

                        return true;
                    })
                    ->action(function (object $livewire): void {
                        $data = Conference::factory()->make()->toArray();
                        $livewire->form->fill($data);
                    }),
            ]),

            // Section::make('Conference Details')
            //     ->description('Provide some basic information about the conference.')
            //     ->icon('heroicon-o-information-circle')
            //     ->collapsible()
            //     ->columns(2)

            // Section::make('Location')
            //     ->columns(2)
        ];
    }
}
