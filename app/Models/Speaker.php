<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'qualifications' => 'array',
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Textarea::make('bio')
                ->required()
                ->columnSpanFull(),
            TextInput::make('twitter_handle')
                ->required()
                ->maxLength(255),
            CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options([
                    'expert-on-topic' => 'Topic Expert',
                    'well-known-speaker' => 'Renowned Speaker',
                    'relevant-background' => 'Relevant Background',
                    'published-research' => 'Published Researcher',
                    'industry-experience' => 'Industry Veteran',
                    'diverse-perspective' => 'Diverse Perspective',
                    'recommendation' => 'Recommended Speaker',
                    'speaking-experience' => 'Experienced Speaker',
                ])
                ->descriptions([
                    'expert-on-topic' => 'Expert on the topic being presented',
                    'well-known-speaker' => 'Well-known professional speaker',
                    'relevant-background' => 'Relevant academic background or education in the field',
                    'published-research' => 'Published research papers or books on the topic',
                    'industry-experience' => 'Extensive industry experience in the topic area',
                    'diverse-perspective' => 'Ability to provide a diverse perspective or background',
                    'recommendation' => 'Recommendation from a colleague or industry expert',
                    'speaking-experience' => 'Prior experience speaking at conferences or events',
                ])
                ->columns(3),
        ];
    }
}
