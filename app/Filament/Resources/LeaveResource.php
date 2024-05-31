<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    protected static ?string $label = 'Dispensa';

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Solicitar dispensa')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('date_leave')
                            ->prefix('➡️️')
                            ->label('Data e horário de saída:')
                            ->timezone('America/Sao_Paulo')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        DateTimePicker::make('date_back')
                            ->prefix('⬅️')
                            ->label('Data e horário de retorno:')
                            ->timezone('America/Sao_Paulo')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        RichEditor::make('motive')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->hint('Atenção!')
                            ->hintIcon('heroicon-m-exclamation-triangle', tooltip: 'Caso a solicitação seja para horários com atividades previstas em QTS, especificar o motivo de não conseguir resolver a demanda em horário sem atividades em QTS.')
                            ->hintColor('primary')
                            ->columnSpan(2)
                            ->helperText('Caso a solicitação seja para horários com atividades previstas em QTS, especificar o motivo de não conseguir resolver a demanda em horário sem atividades em QTS.')
                            ->placeholder('Solicito dispensa devido à consulta médica agendada com antecedência e este é o único horário disponível com o médico especialista. Não há possibilidade de reagendar para outro horário semelhante dentro do período de tratamento recomendado.')
                            ->label('Motivo (com detalhamento)'),

                        RichEditor::make('missed_classes')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->hint('Atenção!')
                            ->hintColor('warning')
                            ->hintIcon('heroicon-m-exclamation-triangle', tooltip: 'Liste instruções e tempos que serão perdidos: Ex: 2 tempos TFM e 3 tempos de APH.')
                            ->columnSpan(2)
                            ->placeholder('2 tempos TFM e 3 tempos de APH')
                            ->helperText('Liste instruções e tempos que serão perdidos: Ex: 2 tempos TFM e 3 tempos de APH')
                            ->label('Instruções e quantidade de tempos perdidos previstos em QTS'),

                        Checkbox::make('accept_terms')
                            ->columnSpan(2)
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Dê ciência',
                            ])
                            ->helperText('Conforme previsto na NE-01, existe um limite de faltas em cada disciplina. Caso exceda esse número, o discente poderá ser desligado do curso.')
                            ->label('Ciência de possibilidade de desligamento'),

                        FileUpload::make('file')
                            ->disk('public')
                            ->visibility('public')
                            ->label('Arquivo comprobatório')
                            ->directory('leave')
                            ->openable()
                            ->columnSpan(2)
                            ->downloadable()
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                    ->prepend('dispensa-'),
                            ),

                    ]),

                Section::make('Deliberar dispensa (coordenação)')
                    ->description('Determine se a dispensa será autorizada.')
                    ->schema([

                        Radio::make('status')
                            ->options(StatusEnum::class)
                            ->default('Em andamento')
                            ->label('Parecer')
                            ->disabled((auth()->user()->hasRole('panel_user'))),

                        RichEditor::make('final_judgment_reason')
                            ->columnSpan(2)
                            ->helperText('Campo para anotações sobre parecer.')
                            ->label('Observações da coordenação')
                            ->disabled((auth()->user()->hasRole('panel_user'))),

                        Checkbox::make('paid')
                            ->columnSpan(2)
                            ->helperText('O aluno gozou a dispensa e anexou documento comprobatório.')
                            ->label('Cumprida/Arquivada'),
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
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->label('Nº'),

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
                    ->label('Solicitado em'),

                Tables\Columns\TextColumn::make('date_leave')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Saída'),

                Tables\Columns\TextColumn::make('date_back')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Retorno'),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->label('Parecer'),

                TextColumn::make('motive')
                    ->limit(45)
                    ->toggleable()
                    ->html()
                    ->label('Motivo'),

                Tables\Columns\IconColumn::make('paid')
                    ->label('Cumprida/Arquivada')
                    ->boolean()
                    ->alignCenter(),
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
