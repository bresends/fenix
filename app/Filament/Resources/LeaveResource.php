<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\LeaveResource\Pages;
use App\Models\Fo;
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
                    ->icon('heroicon-o-pencil-square')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->columns(2)
                    ->schema([
                        DateTimePicker::make('date_leave')
                            ->prefix('➡️️')
                            ->label('Data e horário de saída:')
                            ->seconds(false)
                            ->displayFormat('d/m/y H:i')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        DateTimePicker::make('date_back')
                            ->prefix('⬅️')
                            ->label('Data e horário de retorno:')
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
                            ->columnSpanFull()
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
                            ->columnSpanFull()
                            ->placeholder('2 tempos TFM e 3 tempos de APH')
                            ->helperText('Liste instruções e tempos que serão perdidos: Ex: 2 tempos TFM e 3 tempos de APH')
                            ->label('Instruções e quantidade de tempos perdidos previstos em QTS'),

                        Checkbox::make('accept_terms')
                            ->columnSpanFull()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Dê ciência',
                            ])
                            ->helperText('Conforme previsto na NE-01, existe um limite de faltas em cada disciplina. Caso exceda esse número, o discente poderá ser desligado do curso.')
                            ->label('Ciência de possibilidade de desligamento'),

                    ]),

                FileUpload::make('file')
                    ->disk('s3')
                    ->visibility('private')
                    ->label('Arquivo comprobatório')
                    ->directory('leave')
                    ->openable()
                    ->columnSpanFull()
                    ->downloadable()
                    ->maxSize(5000)
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                            ->prepend('dispensa-'),
                    )
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' &&
                            $get('user_id') !== auth()->user()->id) || $get('paid') === true),

                Section::make('Deliberar dispensa (coordenação)')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->schema([
                        Radio::make('status')
                            ->options(StatusEnum::class)
                            ->default(StatusEnum::EM_ANDAMENTO->value)
                            ->label('Parecer')
                            ->disabled(!auth()->user()->hasRole('super_admin')),

                        RichEditor::make('final_judgment_reason')
                            ->helperText('Campo para anotações sobre parecer.')
                            ->label('Observações da coordenação')
                            ->disabled(fn(Get $get): bool => (auth()->user()->hasExactRoles('panel_user') || $get('status') !== 'Em andamento')),

                        Checkbox::make('paid')
                            ->helperText('O aluno gozou a dispensa e anexou documento comprobatório.')
                            ->label('Arquivada')
                            ->disabled(!auth()->user()->hasRole('super_admin')),
                    ])
                    ->hiddenOn('create'),
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
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->numeric()
                    ->label('Nº'),

                TextColumn::make('user.platoon')
                    ->label('Pelotão')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nome')
                    ->searchable(),

                TextColumn::make('user.rg')
                    ->label('Rg')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                TextColumn::make('date_leave')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Saída'),

                TextColumn::make('date_back')
                    ->dateTime('d/m/y H:i')
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

                IconColumn::make('paid')
                    ->label('Arquivada')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusEnum::class)
                    ->label('Parecer'),
                Filter::make('paid')
                    ->label('Arquivada')
                    ->toggle(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('archive')
                    ->label('Arquivar')
                    ->hidden(!auth()->user()->hasRole('super_admin'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn(Leave $record) => $record->update(['paid' => true]))
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
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }
}
