<?php

namespace App\Filament\Resources;

use App\Enums\FoEnum;
use App\Enums\PlatoonEnum;
use App\Enums\StatusExamEnum;
use App\Enums\StatusFoEnum;
use App\Filament\Resources\FoResource\Pages;
use App\Models\Fo;
use App\Models\Military;
use App\Models\User;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FoResource extends Resource
{
    protected static ?string $model = Fo::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $label = 'Fato Observado';

    protected static ?string $pluralModelLabel = 'Fatos Observados';

    protected static ?string $navigationGroup = 'Documentos';

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
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('FO')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.platoon')
                    ->label('Pelotão')
                    ->badge()
                    ->sortable(),

                TextColumn::make('user.rg')
                    ->label('Rg')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Emitido em'),

                TextColumn::make('type')
                    ->badge()
                    ->label('Tipo'),

                TextColumn::make('reason')
                    ->badge()
                    ->label('Descrição do fato')
                    ->limit(45)
                    ->toggleable()
                    ->color('gray'),

                TextColumn::make('excuse')
                    ->label('Ciência/Justificativa')
                    ->limit(50)
                    ->html(true),

                TextColumn::make('excuse_timestamp')
                    ->dateTime('d/m/y H:i')
                    ->label('Justificado em'),

                TextColumn::make('issuer.name')
                    ->label('Observador')
                    ->getStateUsing(function (Fo $record): ?string {
                        $military = Military::find($record->issuer);
                        return $military ? $military->sei : null;
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->label('Parecer'),

                IconColumn::make('paid')
                    ->label('Cumprido/Arquivado')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusFoEnum::class)
                    ->label('Parecer'),
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
                                fn(Builder $query, $platoon): Builder => $query->whereRelation('user', 'platoon', $platoon)
                            );
                    }),
                Filter::make('paid')
                    ->label('Cumprido/Arquivado')
                    ->toggle(),
            ])
            ->actions([
                DeleteAction::make(),
                Action::make('archive')
                    ->label('Arquivar')
                    ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn(Fo $record) => $record->update(['paid' => true]))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('archive')
                        ->label('Arquivar')
                        ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        ->icon('heroicon-o-archive-box')
                        ->action(fn(Collection $records) => $records->each->update(['paid' => true])),
                    BulkAction::make('accept')
                        ->label('Deferir')
                        ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        ->icon('heroicon-o-check')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn(Collection $records) => $records->each->update(['status' => StatusFoEnum::DEFERIDO->value, 'evaluated_by' => auth()->id(), 'evaluated_at' => now()])),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Emitir FO')
                    ->columns(2)
                    ->icon('heroicon-o-pencil-square')
                    ->disabled(auth()->user()->hasExactRoles('panel_user'))
                    ->disabled(function (string $operation, Get $get): bool {
                        $military = Military::firstWhere('rg', auth()->user()->rg);

                        // Do not allow to edit or delete if the user is not the issuer, the FO is not in progress, or the student has already justified
                        return $operation !== 'create' &&
                            (!$military || $get('issuer') !== $military->id ||
                                $get('status') !== StatusFoEnum::EM_ANDAMENTO->value ||
                                !empty($get('excuse')));
                    })
                    ->schema([
                        Select::make('type')
                            ->options(FoEnum::class)
                            ->label('Tipo')
                            ->prefix('🏷️')
                            ->native(false)
                            ->default(FoEnum::Negativo->value)
                            ->required(),

                        DateTimePicker::make('date_issued')
                            ->prefix('⏰️')
                            ->label('Horário da Anotação')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->required()
                            ->default(fn() => session()->has('dataFill') ? session()->get('dataFill')['date_issued'] : now()),

                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->orderBy('name'),
                            )
                            ->searchable(['name', 'rg'])
                            ->getOptionLabelFromRecordUsing(fn(User $record) => "({$record->platoon->value}) - {$record->name}")
                            ->label('Observado')
                            ->prefix('🔍')
                            ->preload()
                            ->required(),

                        Select::make('issuer')
                            ->prefix('🕵️')
                            ->options(Military::all()->pluck('name', 'id'))
                            ->default(function () {
                                $military = Military::firstWhere('name', auth()->user()->name);

                                return $military ? $military->id : null;
                            })
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->label('Observador'),

                        TextInput::make('reason')
                            ->default(fn() => session()->has('dataFill') ? session()->get('dataFill')['reason'] : null)
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
                            ->columnSpanFull()
                            ->default(fn() => session()->has('dataFill') ? session()->get('dataFill')['observation'] : null)
                            ->label('Observações')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                    ]),

                Section::make('Ciência/Justificativa do aluno')
                    ->icon('heroicon-o-check')
                    ->hiddenOn('create')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->schema([
                        RichEditor::make('excuse')
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->dehydrated()
                            ->label('Dê ciência ou justifique o FO recebido'),

                        DateTimePicker::make('excuse_timestamp')
                            ->hidden(fn(Get $get): bool => empty($get('excuse')))
                            ->prefix('📆️️')
                            ->label('Justificado em')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(),
                    ]),


                Section::make('Deliberação do FO (coordenação)')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->hiddenOn('create')
                    ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->schema([
                        Radio::make('status')
                            ->options(StatusFoEnum::class)
                            ->default(StatusFoEnum::EM_ANDAMENTO->value)
                            ->label('Parecer')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== StatusFoEnum::EM_ANDAMENTO->value) {
                                    $set('evaluated_by', auth()->id());
                                    $set('evaluated_at', now());
                                }
                            }),

                        RichEditor::make('final_judgment_reason')
                            ->helperText('Campo para anotações sobre parecer do FO, ordem de serviço, etc.')
                            ->disabled(fn(Get $get): bool => $get('paid') === true)
                            ->dehydrated()
                            ->label('Observações da coordenação'),

                        Checkbox::make('paid')
                            ->helperText('FO cumprido em Ordem de Serviço e/ou arquivado.')
                            ->label('Cumprido/Arquivado'),
                    ]),

                Section::make('Decisor do Fato Observado')
                    ->hiddenOn('create')
                    ->columns(2)
                    ->hidden(fn(Get $get): bool => $get('status') === StatusExamEnum::EM_ANDAMENTO->value)
                    ->icon('heroicon-o-scale')
                    ->schema([
                        Select::make('evaluated_by')
                            ->label('Deliberado por')
                            ->prefix('👨🏻‍⚖️')
                            ->relationship('evaluator', 'name')
                            ->disabled()
                            ->dehydrated(),

                        DateTimePicker::make('evaluated_at')
                            ->prefix('📆️️')
                            ->label('Deliberado em')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(),
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
