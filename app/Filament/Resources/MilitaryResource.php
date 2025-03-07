<?php

namespace App\Filament\Resources;

use App\Enums\DivisionEnum;
use App\Enums\PlatoonEnum;
use App\Enums\RankEnum;
use App\Filament\Resources\MilitaryResource\Pages;
use App\Models\Military;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MilitaryResource extends Resource {
    protected static ?string $model = Military::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $label = 'Militar';

    protected static ?string $pluralModelLabel = 'Militares';

    protected static ?string $navigationGroup = 'Perfis';

    public static function table(Table $table): Table {
        return $table
            ->defaultSort('sort', 'desc')
            ->reorderable('sort', auth()
                ->user()
                ->hasRole('super_admin'))
            ->persistSearchInSession()
            ->columns([

                TextColumn::make('rank')
                          ->label('Posto/Grad.')
                          ->badge()
                          ->sortable(),

                TextColumn::make('division')
                          ->badge()
                          ->sortable()
                          ->label('Quadro'),

                TextColumn::make('platoon')
                          ->label('Pelotão')
                          ->badge()
                          ->sortable(query: function (Builder $query, string $direction): Builder {
                              return $query
                                  ->join('users', 'militaries.rg', '=', 'users.rg') // Adjust this join based on your schema
                                  ->orderBy('users.platoon', $direction)
                                  ->select('militaries.*');
                          }),

                TextColumn::make('rg')
                          ->searchable()
                          ->label('Rg')
                          ->sortable(),

                TextColumn::make('name')
                          ->label('Nome')
                          ->searchable()
                          ->toggleable()
                          ->sortable(),

                TextColumn::make('sei')
                          ->label('SEI')
                          ->copyable()
                          ->toggleable()
                          ->copyMessage('Copiado para área de trasferência')
                          ->copyMessageDuration(1000),
            ])
            ->filters([
                Filter::make('user')
                      ->form([
                          Select::make('platoon')
                                ->label('Pelotão')
                                ->options(PlatoonEnum::class)
                      ])
                      ->query(function (Builder $query, array $data): Builder {
                          return $query
                              ->when(
                                  $data['platoon'],
                                  fn(Builder $query, $platoon): Builder => $query->where('platoon', $platoon)
                              );
                      })
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make('name')
                         ->label('Nome')
                         ->rules([
                             fn(): Closure => function (string $attribute, $name, Closure $fail) {
                                 $contains_upper = array_filter(explode(' ', $name), fn($word) => ctype_upper($word));
                                 if (empty($contains_upper)) {
                                     $fail('Insira o nome de guerra do militar em caixa alta. Ex. João BATISTA Silveira.');
                                 }
                             },
                         ])
                         ->required(),

                TextInput::make('rg')
                         ->label('Rg')
                         ->placeholder('4140')
                         ->integer()
                         ->minValue(100)
                         ->required()
                         ->live()
                         ->unique(ignoreRecord: true),

                Select::make('rank')
                      ->options(RankEnum::class)
                      ->label('Posto/Graduação')
                      ->searchable()
                      ->required()
                      ->default(RankEnum::AL_SD->value)
                      ->native(false),

                Select::make('division')
                      ->options(DivisionEnum::class)
                      ->label('Quadro')
                      ->default(DivisionEnum::QP_COMBATENTE->value)
                      ->required()
                      ->native(false),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListMilitaries::route('/'),
            'create' => Pages\CreateMilitary::route('/create'),
            'edit' => Pages\EditMilitary::route('/{record}/edit'),
        ];
    }
}
