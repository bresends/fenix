<?php

namespace App\Filament\Resources;

use App\Enums\PlatoonEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Military;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'Perfis';

    protected static ?string $label = 'Perfil de Usuário';

    protected static ?string $pluralModelLabel = 'Perfis de Usuário';

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rg')
                    ->label('Militar')
                    ->options(
                        Military::all()->pluck('rg', 'rg')->mapWithKeys(function ($rg) {
                            $military = Military::firstWhere('rg', $rg);

                            return [$rg => "{$rg} - {$military->name}"];
                        })
                    )
                    ->validationMessages([
                        'unique' => 'Esse registro já existe na base de dados',
                    ])
                    ->disabled(auth()->user()->hasRole('panel_user'))
                    ->preload()
                    ->searchable()
                    ->live()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('name', Military::firstWhere('rg', $state)->name);
                    }),

                Hidden::make('name'),

                Forms\Components\TextInput::make('email')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Senha')
                    ->confirmed()
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (string $state): string => bcrypt($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),

                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Confirmação de senha')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create'),

                Select::make('platoon')
                    ->label('Pelotão')
                    ->searchable()
                    ->options(PlatoonEnum::class)
                    ->default('Alpha')
                    ->disabled(! auth()->user()->hasRole('super_admin')),

                Select::make('roles')
                    ->label('Perfis')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->disabled(! auth()->user()->hasRole('super_admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (! auth()->user()->hasRole('super_admin')) {
                    $query->where('id', auth()->user()->id);
                }
            })
            ->columns([
                TextColumn::make('platoon')
                    ->icon('heroicon-o-users')
                    ->badge()
                    ->label('Pelotão')
                    ->searchable(),
                TextColumn::make('rg')
                    ->label('Rg'),
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email'),
                TextColumn::make('roles.name')
                    ->label('Perfis')
                    ->listWithLineBreaks()
                    ->badge()
                    ->hidden(! auth()->user()->hasRole('super_admin')),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
