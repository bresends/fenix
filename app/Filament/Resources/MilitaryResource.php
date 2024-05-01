<?php

namespace App\Filament\Resources;

use App\Enums\BloodTypeEnum;
use App\Enums\DivisionEnum;
use App\Enums\RankEnum;
use App\Filament\Resources\MilitaryResource\Pages;
use App\Models\Military;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilitaryResource extends Resource
{
    protected static ?string $model = Military::class;

    protected static ?string $label = 'Militar';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Militares';

    protected static ?string $navigationGroup = 'Gestão de Militares';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(__('Nome'))->required(),
                TextInput::make('rg')
                    ->integer()
                    ->minValue(100)
                    ->maxValue(10000)
                    ->required()
                    ->live()
                    ->unique(),
                TextInput::make('email')->email()->required(),
                Select::make('rank')
                    ->options(RankEnum::class)->label(__('Posto/Graduação')),
                Select::make('division')
                    ->options(DivisionEnum::class)->label(__('Quadro')),
                Select::make('blood_type')
                    ->options(BloodTypeEnum::class)->label(__('Tipo sanguíneo')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rank')->label('Posto/Graduação')->sortable(),
                TextColumn::make('division')->label('Quadro'),
                TextColumn::make('rg')->searchable()->label('RG')->sortable(),
                TextColumn::make('name')->label('Nome')->searchable()->label('Nome')->sortable(),
                TextColumn::make('email'),
                TextColumn::make('blood_type')->label('Tipo sanguíneo'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMilitaries::route('/'),
            'create' => Pages\CreateMilitary::route('/create'),
            'edit' => Pages\EditMilitary::route('/{record}/edit'),
        ];
    }
}
