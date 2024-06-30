<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\SwitchShiftResource\Pages;
use App\Models\Military;
use App\Models\SwitchShift;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SwitchShiftResource extends Resource
{
    protected static ?string $model = SwitchShift::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $label = 'Troca de serviço';

    protected static ?string $pluralModelLabel = 'Trocas de serviço';

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Solicitar troca de serviço')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->columns(2)
                    ->schema([

                        Fieldset::make('Serviço em que será substituído')
                            ->schema([
                                DateTimePicker::make('first_shift_date')
                                    ->prefix('📆️')
                                    ->label('Data e hora (Serviço 1)')
                                    ->seconds(false)
                                    ->displayFormat('d/m/y H:i')
                                    ->native(false)
                                    ->required()
                                    ->default(now()),

                                TextInput::make('first_shift_place')
                                    ->label('Local (Serviço 1)')
                                    ->prefix('📌')
                                    ->datalist([
                                        '1º BBM',
                                        '2º BBM',
                                        '8º BBM',
                                        'BSE',
                                        'QCG',
                                    ])
                                    ->required(),

                                TextInput::make('first_shift_receiving_military')
                                    ->label('Substituído (Serviço 1)')
                                    ->default(function () {
                                        return Military::firstWhere('rg', auth()->user()->rg)->name;
                                    })
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Select::make('first_shift_paying_military')
                                    ->label('Substituto (Serviço 1)')
                                    ->options(Military::all()->pluck('name', 'name'))
                                    ->required()
                                    ->preload()
                                    ->searchable(['name', 'id'])
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, ?string $state) {
                                        $set('second_shift_receiving_military', $state);
                                    }),
                            ]),

                        Fieldset::make('Serviço em que será substituto')
                            ->schema([
                                DateTimePicker::make('second_shift_date')
                                    ->prefix('📆️️')
                                    ->label('Data e hora (Serviço 2)')
                                    ->seconds(false)
                                    ->displayFormat('d/m/y H:i')
                                    ->native(false)
                                    ->required()
                                    ->default(now()),

                                TextInput::make('second_shift_place')
                                    ->label('Local (Serviço 2)')
                                    ->prefix('📌')
                                    ->datalist([
                                        'CAEBM',
                                        '1º BBM',
                                        '2º BBM',
                                        '8º BBM',
                                        'BSE',
                                        'QCG',
                                    ])
                                    ->required(),

                                TextInput::make('second_shift_receiving_military')
                                    ->label('Substituído (Serviço 2)')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('second_shift_paying_military')
                                    ->label('Substituto (Serviço 2)')
                                    ->default(function () {
                                        return Military::firstWhere('rg', auth()->user()->rg)->name;
                                    })
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ]),

                        TextInput::make('type')
                            ->label('Tipo da escala')
                            ->datalist([
                                'Aluno Adjunto',
                                'Cadete de Dia',
                                'Plantão/Ronda no CAEBM',
                                'Estágio Operacional (ABT/ABTS/ASA/UR)',
                            ])
                            ->required(),

                    ]),
                Section::make('Motivo da troca de serviço')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->schema([
                        RichEditor::make('motive')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->hint('Atenção!')
                            ->hintIcon('heroicon-m-exclamation-triangle', tooltip: 'Liste os motivos da troca de serviço com detalhamento.')
                            ->hintColor('primary')
                            ->columnSpan(2)
                            ->label('Motivo (com detalhamento)'),

                        FileUpload::make('file')
                            ->disk('r2')
                            ->visibility('private')
                            ->label('Anexar arquivo (se houver)')
                            ->columnSpan(2)
                            ->directory('switch-shift')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                    ->prepend('troca-serviço-'),
                            ),
                    ]),

                Section::make('Ciência do 2º aluno envolvido')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('first_shift_paying_military'
                            ) !== auth()->user()->name) || $get('status') !== 'Em andamento')
                    ->hiddenOn('create')
                    ->columns(2)
                    ->schema([
                        Checkbox::make('accepted')
                            ->label('Aceito a presente solicitação de troca de serviço.')
                            ->required(),
                    ]),

                Section::make('Deliberar troca de serviço (coordenação)')
                    ->hiddenOn('create')
                    ->disabled(!auth()->user()->hasRole('super_admin'))
                    ->description('Determine se a troca de serviço será autorizada.')
                    ->schema([
                        Radio::make('status')
                            ->options(StatusEnum::class)
                            ->default(StatusEnum::EM_ANDAMENTO->value)
                            ->label('Parecer'),

                        RichEditor::make('final_judgment_reason')
                            ->columnSpan(2)
                            ->helperText('Campo para anotações sobre parecer.')
                            ->label('Observações da coordenação'),

                        Checkbox::make('paid')
                            ->columnSpan(2)
                            ->label('Informado às OBMs/Arquivado'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasExactRoles('panel_user')) {
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('first_shift_paying_military', auth()->user()->name);
                }
            })
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->label('Nº'),

                TextColumn::make('user.platoon')
                    ->badge()
                    ->label('Pelotão'),

                TextColumn::make('user.rg')
                    ->label('Rg'),

                TextColumn::make('user.name')
                    ->label('Solicitante'),

                TextColumn::make('type')
                    ->badge()
                    ->label('Tipo')
                    ->limit(45)
                    ->toggleable()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                TextColumn::make('first_shift_date')
                    ->dateTime('d/m/y')
                    ->sortable()
                    ->label('Data da troca'),

                IconColumn::make('accepted')
                    ->label('Aceita pelo substituto')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->label('Parecer'),

                TextColumn::make('motive')
                    ->limit(45)
                    ->toggleable()
                    ->html()
                    ->label('Motivo'),

                IconColumn::make('paid')
                    ->label('Informado às OBMs/Arquivado')
                    ->boolean()
                    ->alignCenter(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusEnum::class)
                    ->label('Parecer'),
                Filter::make('paid')
                    ->label("Anexado no SEI/Arquivado")
                    ->toggle()
            ])
            ->actions([
                EditAction::make(),
                Action::make('archive')
                    ->label('Arquivar')
                    ->hidden(!auth()->user()->hasRole('super_admin'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn(SwitchShift $record) => $record->update(['paid' => true]))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('archive')
                        ->label('Arquivar')
                        ->hidden(!auth()->user()->hasRole('super_admin'))
                        ->icon('heroicon-o-archive-box')
                        ->action(fn(Collection $records) => $records->each->update(['paid' => true])),
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
            'index' => Pages\ListSwitchShifts::route('/'),
            'create' => Pages\CreateSwitchShift::route('/create'),
            'edit' => Pages\EditSwitchShift::route('/{record}/edit'),
        ];
    }
}
