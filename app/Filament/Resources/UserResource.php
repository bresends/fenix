<?php

namespace App\Filament\Resources;

use App\Enums\PlatoonEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Military;
use App\Models\User;
use Filament\Forms;
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
                    ->options(Military::all()->pluck('id', 'id'))
                    ->disabled((auth()->user()->hasRole('panel_user')))
                    ->preload()
                    ->searchable()
                    ->live()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $set('name', Military::firstWhere('id', $state)->name);
                    }),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->label('Nome'),

                Forms\Components\TextInput::make('email')
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('platoon')
                    ->label('Pelotão')
                    ->searchable()
                    ->options(PlatoonEnum::class)
                    ->disabled(auth()->user()->hasRole('panel_user')),

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

                Select::make('roles')
                    ->label('Perfis')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->hidden(auth()->user()->hasRole('panel_user')),
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
                    ->label('Pelotão')
                    ->searchable(),
                TextColumn::make('rg')
                    ->label('Rg'),
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email'),
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
