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
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MilitaryResource extends Resource
{
    protected static ?string $model = Military::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $label = 'Militar';

    protected static ?string $pluralModelLabel = 'Militares';

    protected static ?string $navigationGroup = 'Perfis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required(),
                TextInput::make('id')
                    ->label('Rg')
                    ->integer()
                    ->minValue(100)
                    ->maxValue(10000)
                    ->required()
                    ->live()
                    ->unique(ignoreRecord: true),
                Select::make('rank')
                    ->options(RankEnum::class)
                    ->label('Posto/Graduação')
                    ->searchable()
                    ->required()
                    ->default('Al Sd')
                    ->native(false),
                Select::make('division')
                    ->options(DivisionEnum::class)
                    ->label('Quadro')
                    ->default('QP/Combatente')
                    ->required()
                    ->native(false),
                TextInput::make('tel')
                    ->label('Telefone')
                    ->mask(RawJs::make(<<<'JS'
        $input.length >= 14 ? '(99) 99999-9999' : '(99) 9999-9999'
    JS
                    )),
                Select::make('blood_type')
                    ->options(BloodTypeEnum::class)
                    ->label('Tipo sanguíneo')
                    ->default('A+')
                    ->required()
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('rank')
                    ->label('Posto/Graduação')
                    ->sortable(),
                TextColumn::make('division')
                    ->label('Quadro'),
                TextColumn::make('id')
                    ->searchable()
                    ->label('Rg')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tel')
                    ->label('Telefone'),
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
