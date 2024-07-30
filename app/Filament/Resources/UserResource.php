<?php

namespace App\Filament\Resources;

use App\Enums\BloodTypeEnum;
use App\Enums\PlatoonEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Military;
use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
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
                Tabs::make('Tabs')
                    ->persistTab()
                    ->columnSpan('full')
                    ->contained(false)
                    ->tabs([
                        Tab::make('Login e senha')
                            ->icon('heroicon-m-identification')
                            ->columns(2)
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        Select::make('rg')
                                            ->label('Militar')
                                            ->prefixIcon('heroicon-m-user-circle')
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

                                        TextInput::make('email')
                                            ->required()
                                            ->email()
                                            ->prefixIcon('heroicon-m-at-symbol')
                                            ->unique(ignoreRecord: true),

                                        TextInput::make('password')
                                            ->label('Senha')
                                            ->confirmed()
                                            ->password()
                                            ->revealable()
                                            ->dehydrateStateUsing(fn(string $state): string => bcrypt($state))
                                            ->dehydrated(fn(?string $state): bool => filled($state))
                                            ->required(fn(string $operation): bool => $operation === 'create'),

                                        TextInput::make('password_confirmation')
                                            ->label('Confirmação de senha')
                                            ->password()
                                            ->revealable()
                                            ->required(fn(string $operation): bool => $operation === 'create'),

                                        Select::make('platoon')
                                            ->label('Pelotão')
                                            ->searchable()
                                            ->required()
                                            ->options(PlatoonEnum::class)
                                            ->default('Alpha')
                                            ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                                        Select::make('roles')
                                            ->label('Perfis')
                                            ->relationship('roles', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'admin'])),
                                    ]),
                            ]),

                        Tab::make('Informações Pessoais')
                            ->icon('heroicon-m-user')
                            ->columns(2)
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('phone_number')
                                            ->prefixIcon('heroicon-m-phone')
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

                                        TextInput::make('address')
                                            ->helperText('Rua, Número/Quadra/Lote, Bairro, Cidade/UF')
                                            ->prefixIcon('heroicon-m-home-modern')
                                            ->label('Endereço'),
                                    ]),
                            ]),

                        Tab::make('Contato de emergência')
                            ->icon('heroicon-m-phone-arrow-down-left')
                            ->columns(2)
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('emergency_contact_name')
                                            ->prefixIcon('heroicon-m-identification')
                                            ->label('Nome do contato de emergência'),

                                        TextInput::make('emergency_contact_relationship')
                                            ->prefixIcon('heroicon-m-link')
                                            ->label('Parentesco'),

                                        TextInput::make('emergency_contact_phone_number')
                                            ->prefixIcon('heroicon-m-phone')
                                            ->label('Telefone')
                                            ->mask(RawJs::make(<<<'JS'
        $input.length >= 14 ? '(99) 99999-9999' : '(99) 9999-9999'
    JS
                                            )),

                                        TextInput::make('emergency_contact_address')
                                            ->prefixIcon('heroicon-m-home-modern')
                                            ->helperText('Rua, Número/Quadra/Lote, Bairro, Cidade/UF')
                                            ->label('Endereço do contato de emergência'),
                                    ]),
                            ]),

                        Tab::make('Veículo')
                            ->icon('heroicon-m-truck')
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        Select::make('vehicle_type')
                                            ->label('Tipo')
                                            ->default('Carro')
                                            ->options([
                                                'Carro' => 'Carro',
                                                'Moto' => 'Moto',
                                            ]),
                                        TextInput::make('vehicle_brand')
                                            ->datalist([
                                                'Volkswagen',
                                                'Fiat',
                                                'Ford',
                                                'Toyota',
                                                'Chevrolet',
                                                'Nissan',
                                                'Honda',
                                                'Hyundai',

                                            ])
                                            ->label('Marca'),
                                        TextInput::make('vehicle_model')
                                            ->label('Modelo'),
                                        TextInput::make('vehicle_color')
                                            ->label('Cor'),
                                        TextInput::make('vehicle_licence_plate')
                                            ->label('Placa'),
                                    ]),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
                    $query->where('id', auth()->user()->id);
                }
            })
            ->defaultSort('platoon', 'asc')
            ->columns([
                TextColumn::make('platoon')
                    ->icon('heroicon-o-users')
                    ->badge()
                    ->sortable()
                    ->label('Pelotão'),

                TextColumn::make('rg')
                    ->sortable()
                    ->searchable()
                    ->label('Rg'),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Nome'),

                TextColumn::make('email'),

                TextColumn::make('roles.name')
                    ->label('Perfis')
                    ->listWithLineBreaks()
                    ->badge()
                    ->sortable()
                    ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin'])),
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
