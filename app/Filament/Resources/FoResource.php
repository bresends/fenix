<?php

namespace App\Filament\Resources;

use App\Enums\FoEnum;
use App\Enums\InfractionEnum;
use App\Filament\Resources\FoResource\Pages;
use App\Models\Fo;
use App\Models\Military;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FoResource extends Resource
{
    protected static ?string $model = Fo::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $label = 'Fato Observado';

    protected static ?string $pluralModelLabel = 'Fatos Observados';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Emitir FO')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->options(FoEnum::class)
                            ->label(__('Tipo'))
                            ->prefix('🏷️')
                            ->native(false)
                            ->default('Negativo')
                            ->required(),

                        DateTimePicker::make('date_issued')
                            ->prefix('⏰️')
                            ->label(__('Horário da Anotação'))
                            ->timezone('America/Sao_Paulo')
                            ->seconds(false)
                            ->displayFormat('d-m-Y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'id'])
                            ->label('Observado')
                            ->prefix('🔍')
                            ->preload()
                            ->required(),

                        Select::make('issuer')
                            ->label('Author')
                            ->prefix('🕵️')
                            ->options(Military::all()->pluck('name', 'id'))
                            ->required()
                            ->preload()
                            ->searchable(['name', 'id'])
                            ->label('Observador')
                            ->searchable(),

                        Forms\Components\Select::make('reason')
                            ->label('Descrição do fato')
                            ->prefix('📝️')
                            ->options(InfractionEnum::class)
                            ->required()
                            ->searchable(),
                    ])
                    ->disabled((auth()->user()->hasRole('panel_user'))),

                Section::make('Justificativa')
                    ->schema([
                        Forms\Components\RichEditor::make('excuse')
                            ->label('Justificativa do militar'),
                    ]),

                Section::make('Deliberar FO')
                    ->description('Determine se o FO será justificado.')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'Em andamento' => 'Em andamento',
                                'Justificativa Aceita' => 'Justificativa Aceita',
                                'Justificativa Negada' => 'Justificativa Negada',
                            ])
                            ->default('Aguardando Justificativa')
                            ->native(false)
                            ->label('Status'),

                        Forms\Components\RichEditor::make('final_judgment_reason')
                            ->label('Justificativa de deferimento/indeferimento'),

                        Forms\Components\Toggle::make('paid')
                            ->label('Cumprido?'),
                    ])
                    ->disabled((auth()->user()->hasRole('panel_user'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('panel_user')) {
                    $query->whereHas('user', function ($query) {
                        $query->where('id', auth()->user()->id);
                    });
                }
            })
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nome')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.rg')
                    ->label('Rg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_issued')
                    ->dateTime($format = 'd-m-Y')
                    ->sortable()
                    ->label('Data'),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Positivo' => 'success',
                        'Negativo' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->label('Descrição do fato')
                    ->limit(45)
                    ->toggleable()
                    ->color(fn (string $state): string => match ($state) {
                        default => 'gray',
                    })
                    ->searchable(),

                Tables\Columns\IconColumn::make('excuse')
                    ->label('Justificado?')
                    ->boolean()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {
                        'Em andamento' => 'warning',
                        'Justificativa Aceita' => 'success',
                        'Justificativa Negada' => 'danger',
                    })
                    ->icons([
                        'heroicon-s-exclamation-triangle' => 'Em andamento',
                        'heroicon-s-x-circle' => 'Justificativa Negada',
                        'heroicon-s-check-badge' => 'Justificativa Aceita',
                    ])
                    ->label('Status'),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Cumprido?')
                    ->boolean(),
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
            'index' => Pages\ListFos::route('/'),
            'create' => Pages\CreateFo::route('/create'),
            'edit' => Pages\EditFo::route('/{record}/edit'),
        ];
    }
}
