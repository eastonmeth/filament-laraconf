<?php

namespace App\Filament\Speakers\Resources;

use App\Filament\Speakers\Resources\AttendeeResource\Pages;
use App\Filament\Speakers\Resources\AttendeeResource\RelationManagers;
use App\Models\Attendee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendeeResource extends Resource
{
    protected static ?string $model = Attendee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListAttendees::route('/'),
            'create' => Pages\CreateAttendee::route('/create'),
            'edit' => Pages\EditAttendee::route('/{record}/edit'),
        ];
    }
}
