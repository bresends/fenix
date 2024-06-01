<?php

namespace App\Filament\Resources;

use App\Enums\FoEnum;
use App\Enums\StatusFoEnum;
use App\Filament\Resources\FoResource\Pages;
use App\Models\Fo;
use App\Models\Military;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FoResource extends Resource
{
    protected static ?string $model = Fo::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $label = 'Fato Observado';

    protected static ?string $pluralModelLabel = 'Fatos Observados';

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Emitir FO')
                    ->disabled(auth()->user()->hasExactRoles('panel_user'))
                    ->disabledOn('edit')
                    ->columns(2)
                    ->schema([
                        Select::make('type')
                            ->options(FoEnum::class)
                            ->label('Tipo')
                            ->prefix('🏷️')
                            ->native(false)
                            ->default('Negativo')
                            ->required(),

                        DateTimePicker::make('date_issued')
                            ->prefix('⏰️')
                            ->label('Horário da Anotação')
                            ->timezone('America/Sao_Paulo')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable(['name', 'rg'])
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

                        TextInput::make('reason')
                            ->label('Descrição do fato')
                            ->prefix('📝️')
                            ->datalist([
                                'Atrasar ou Faltar Serviço/Escala',
                                'Sem Luva e Identidade ',
                                'Cabelo fora do Padrão ',
                                'Pé de Cabelo e Barba Fora do Padrão ',
                                'Uniforme Sujo ou Mal Passado ou em Desalinho (sem gorro) ',
                                'Bota/sapato/coturno não Engraxado e não Polido',
                                'Não Cumpriu o Horário para entrar em forma após 6 piques ',
                                'Uso de óculos escuros ou Telefone Celular durante o expediente sem a devida autorização.',
                            ])
                            ->required(),

                        RichEditor::make('observation')
                            ->label('Observações')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpan(2),
                    ]),

                Section::make('Ciência/Justificativa do aluno')
                    ->hiddenOn('create')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->schema([
                        RichEditor::make('excuse')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->label('Dê ciência ou justifique o FO recebido'),

                    ]),

                Section::make('Deliberação do FO (coordenação)')
                    ->hiddenOn('create')
                    ->disabled(!auth()->user()->hasRole('super_admin'))
                    ->description('Campo preenchido pela coordenação.')
                    ->schema([
                        Radio::make('status')
                            ->options(StatusFoEnum::class)
                            ->default('Em andamento')
                            ->label('Parecer'),

                        RichEditor::make('final_judgment_reason')
                            ->helperText('Campo para anotações sobre parecer do FO, ordem de serviço, etc.')
                            ->label('Observações da coordenação'),

                        Checkbox::make('paid')
                            ->helperText('FO cumprido em Ordem de Serviço e/ou arquivado.')
                            ->label('Cumprido/Arquivado'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasExactRoles('panel_user')) {
                    $query->whereHas('user', function ($query) {
                        $query->where('id', auth()->user()->id);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('FO')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.platoon')
                    ->label('Pelotão')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.rg')
                    ->label('Rg')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Emitido em'),

                TextColumn::make('type')
                    ->badge()
                    ->label('Tipo'),

                Tables\Columns\TextColumn::make('reason')
                    ->badge()
                    ->label('Descrição do fato')
                    ->limit(45)
                    ->toggleable()
                    ->color(fn(string $state): string => 'gray')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->label('Parecer'),

                Tables\Columns\IconColumn::make('excuse')
                    ->label('Ciência/Justificativa')
                    ->boolean()
                    ->alignCenter()
                    ->searchable(),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Cumprido/Arquivado')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusFoEnum::class)
                    ->label('Parecer'),
                Filter::make('paid')
                    ->label("Cumprido/Arquivado")
                    ->toggle()
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
