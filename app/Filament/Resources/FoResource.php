<?php

namespace App\Filament\Resources;

use App\Enums\FoEnum;
use App\Enums\FoStatusEnum;
use App\Filament\Resources\FoResource\Pages;
use App\Models\Fo;
use App\Models\Military;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
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
                            ->displayFormat('d-m-Y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('user_id')
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

                        Forms\Components\TextInput::make('reason')
                            ->label('Descrição do fato')
                            ->prefix('📝️')
                            ->datalist([
                                'Atrasar ou Faltar Serviço/Escala (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 27)',
                                'Sem Luva e Identidade (Art. 142 da NE01, RDBM 4681/96 Anexo 01 Item 85)',
                                'Cabelo fora do Padrão (Art. 133 II da NE01)',
                                'Pé de Cabelo e Barba Fora do Padrão (Art. 133 V da NE01)',
                                'Uniforme Sujo ou Mal Passado ou em Desalinho (sem gorro) (Ar. 133 VIII da NE01)',
                                'Bota/sapato/coturno não Engraxado e não Polido (Art. 133 IX da NE01)',
                                'Não Cumpriu o Horário para entrar em forma após 6 piques (Art. 133 I da NE01 (horários). Art. 30 da NE01)',
                                'Uso de óculos escuros ou Telefone Celular durante o expediente sem a devida autorização. Art. 133 XIV da NE01',
                            ])
                            ->required(),

                        Forms\Components\RichEditor::make('observation')
                            ->label('Observações')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpan(2),
                    ])
                    ->disabled(auth()->user()->hasExactRoles('panel_user'))
                    ->disabledOn('edit'),

                Section::make('Ciência/Justificativa do aluno')
                    ->schema([
                        Forms\Components\RichEditor::make('excuse')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->label('Dê ciência ou justifique o FO recebido'),

                    ])
                    ->hiddenOn('create')
                    ->disabled(fn(string $operation, Get $get): bool => $operation === 'edit' && $get('user_id') !== auth()->user()->id),

                Section::make('Deliberação do FO (coordenação)')
                    ->description('Campo preenchido pela coordenação.')
                    ->schema([
                        Radio::make('status')
                            ->options(FoStatusEnum::class)
                            ->default('Em andamento')
                            ->label('Parecer'),

                        Forms\Components\RichEditor::make('final_judgment_reason')
                            ->helperText('Campo para anotações sobre parecer do FO, ordem de serviço, etc.')
                            ->label('Observações da coordenação'),

                        Forms\Components\Toggle::make('paid')
                            ->label('Cumprido/Arquivado'),
                    ])
                    ->hiddenOn('create')
                    ->disabled(!auth()->user()->hasRole('super_admin')),
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
                    ->dateTime($format = 'd-m-y H:i')
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
                    ->searchable(),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Cumprido/Arquivado')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(FoStatusEnum::class)
                    ->label('Parecer'),
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
