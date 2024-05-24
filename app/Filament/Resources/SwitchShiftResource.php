<?php

namespace App\Filament\Resources;

use App\Enums\FoStatusEnum;
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
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->schema([

                        Fieldset::make('1️⃣Primeiro Serviço')
                            ->schema([
                                DateTimePicker::make('first_shift_date')
                                    ->prefix('📆️')
                                    ->label('Data e hora (Serviço 1)')
                                    ->timezone('America/Sao_Paulo')
                                    ->seconds(false)
                                    ->displayFormat('d-m-Y H:i')
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

                        Fieldset::make('2️⃣Segundo Serviço')
                            ->schema([
                                DateTimePicker::make('second_shift_date')
                                    ->prefix('📆️️')
                                    ->label('Data e hora (Serviço 2)')
                                    ->timezone('America/Sao_Paulo')
                                    ->seconds(false)
                                    ->displayFormat('d-m-Y H:i')
                                    ->native(false)
                                    ->required()
                                    ->default(now()),

                                TextInput::make('second_shift_place')
                                    ->label('Local (Serviço 2)')
                                    ->prefix('📌')
                                    ->datalist([
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
                                'Plantão / Ronda no CAEBM',
                                'Estágio Operacional (ABT/ABTS/ASA/UR)',
                            ])
                            ->required(),

                    ])
                    ->disabled(fn (string $operation, Get $get): bool => $operation === 'edit' && $get('user_id') !== auth()->user()->id)
                    ->columns(2),

                Section::make('Motivo da troca de serviço')
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
                            ->disk('public')
                            ->visibility('public')
                            ->label('Anexar arquivo (se houver)')
                            ->columnSpan(2)
                            ->directory('switch-shift')
                            ->openable()
                            ->downloadable()
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend('troca-serviço-'),
                            ),
                    ])
                    ->columns(1),

                Section::make('Deliberar troca de serviço (coordenação)')
                    ->description('Determine se a troca de serviço será autorizada.')
                    ->schema([
                        Radio::make('status')
                            ->options(FoStatusEnum::class)
                            ->default('Em andamento')
                            ->label('Parecer'),

                        RichEditor::make('final_judgment_reason')
                            ->columnSpan(2)
                            ->helperText('Campo para anotações sobre parecer.')
                            ->label('Observações da coordenação'),

                        Checkbox::make('paid')
                            ->columnSpan(2)
                            ->helperText('O aluno gozou a dispensa e anexou documento comprobatório.')
                            ->label('Cumprida/Arquivada'),
                    ])
                    ->hiddenOn('create')
                    ->disabled(! auth()->user()->hasRole('super_admin')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasExactRoles('panel_user')) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->label('Nº'),

                TextColumn::make('requester')
                    ->label('Solicitante'),

                TextColumn::make('requester')
                    ->label('Solicitante'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->label('Tipo')
                    ->limit(45)
                    ->toggleable()
                    ->color(fn (string $state): string => 'gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                Tables\Columns\TextColumn::make('first_shift_date')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Data da troca'),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->label('Parecer'),

                TextColumn::make('motive')
                    ->limit(45)
                    ->toggleable()
                    ->html()
                    ->label('Motivo'),
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
            'index' => Pages\ListSwitchShifts::route('/'),
            'create' => Pages\CreateSwitchShift::route('/create'),
            'edit' => Pages\EditSwitchShift::route('/{record}/edit'),
        ];
    }
}
