<?php

namespace App\Filament\Resources;

use App\Enums\FoEnum;
use App\Enums\InfractionEnum;
use App\Filament\Resources\FoResource\Pages;
use App\Models\Fo;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FoResource extends Resource
{
    protected static ?string $model = Fo::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

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
                        Forms\Components\Select::make('military_id')
                            ->relationship('military', 'name')
                            ->searchable(['name', 'rg'])
                            ->label(__('Observado'))
                            ->prefix('🔍')
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('issuer')
                            ->label(__('Observador'))
                            ->relationship('military', 'name')
                            ->prefix('🕵️')
                            ->required()
                            ->preload()
                            ->searchable(['name', 'rg']),
                        Forms\Components\Select::make('reason')
                            ->label(__('Descrição do fato'))
                            ->prefix('📝️')
                            ->options(InfractionEnum::class)
                            ->required()
                            ->searchable(),
                    ])->disabled((auth()->user()->hasRole('panel_user'))),

                Section::make('Justificativa')
                    ->schema([
                        Forms\Components\RichEditor::make('excuse')
                            ->label(__('Justificativa do militar')),
                    ]),

                Section::make('Deliberar FO')
                    ->description('Determine se o FO será justificado.')
                    ->schema([
                        Forms\Components\Toggle::make('final_judgment')
                            ->label(__('Justificativa aceita?')),
                        Forms\Components\RichEditor::make('final_judgment_reason')
                            ->label(__('Justificativa de deferimento/indeferimento')),
                        Forms\Components\Toggle::make('paid')
                            ->label(__('Cumprido?')),
                    ])->disabled((auth()->user()->hasRole('panel_user'))),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('panel_user')) {
                    $query->whereHas('military', function ($query) {
                        $query->where('rg', auth()->user()->rg);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('date_issued')
                    ->dateTime($format = 'd-m-y')
                    ->sortable()
                    ->label('Data'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo'),
                Tables\Columns\TextColumn::make('military.rank')
                    ->label('Posto/Graduação')
                    ->sortable(),
                Tables\Columns\TextColumn::make('military.rg')
                    ->label('Rg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('military.name')
                    ->label('Nome')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Descrição do fato')
                    ->searchable(),
                Tables\Columns\IconColumn::make('excuse')
                    ->label('Justificado?')
                    ->boolean()
                    ->searchable(),
                Tables\Columns\IconColumn::make('final_judgment')
                    ->label('Deliberado?')
                    ->boolean(),
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
