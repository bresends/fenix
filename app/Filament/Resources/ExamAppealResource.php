<?php

namespace App\Filament\Resources;

use App\Enums\MakeUpExamStatusEnum;
use App\Enums\PlatoonEnum;
use App\Enums\StatusExamEnum;
use App\Filament\Resources\ExamAppealResource\Pages;
use App\Models\ExamAppeal;
use App\Models\Leave;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ExamAppealResource extends Resource
{
    protected static ?string $model = ExamAppeal::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $label = 'Recurso de Prova';

    protected static ?string $pluralModelLabel = 'Recursos de Prova';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $navigationGroup = 'Documentos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Solicitar recurso')
                    ->columns(2)
                    ->icon('heroicon-o-pencil-square')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->schema([
                        Select::make('user_id')
                            ->relationship(
                                name: 'user',
                                titleAttribute: 'name',
                            )
                            ->hiddenOn('create')
                            ->disabled()
                            ->getOptionLabelFromRecordUsing(fn(User $record) => "({$record->platoon->value}) - {$record->name}")
                            ->columnSpanFull()
                            ->label('Solicitante')
                            ->prefix('👨🏻‍🚒'),

                        TextInput::make('discipline')
                            ->label('Nome da disciplina (conforme consta no Plano de Curso)')
                            ->placeholder('Salvamento Terrestre')
                            ->required(),

                        Select::make('type')
                            ->options(MakeUpExamStatusEnum::class)
                            ->label('Tipo')
                            ->prefix('🏷️')
                            ->native(false)
                            ->default(MakeUpExamStatusEnum::TEORICA->value)
                            ->required(),

                        TextInput::make('exam')
                            ->label('Prova')
                            ->placeholder('N2')
                            ->required(),

                        TextInput::make('question')
                            ->label('Questão/Item avaliado')
                            ->placeholder('12')
                            ->required(),

                        RichEditor::make('motive')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpanFull()
                            ->label('Fundamentação do recurso'),

                        RichEditor::make('bibliography')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpanFull()
                            ->label('Bibliografia'),

                        Checkbox::make('accept_terms')
                            ->columnSpanFull()
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Dê ciência',
                            ])
                            ->helperText('Estou ciente das regras e condições estabelecidas na Norma de Ensino 01 do Comando da Academia e Ensino Bombeiro Militar, em especial quanto ao que consta no Capítulo VIII – Apresentação de Recurso (art. 20 ao 24).')
                            ->label('Declaro ciência da regras e condições estabelecidas na NE 01.'),

                        FileUpload::make('file')
                            ->disk('minio')
                            ->visibility('private')
                            ->label('Anexos (se houver)')
                            ->directory('exam-appeal')
                            ->openable()
                            ->columnSpanFull()
                            ->downloadable()
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                    ->prepend('recurso-' . now()->format('Y-m-d') . '-' . auth()->user()->name . '-' . now()->format('s'))
                            ),

                    ]),

                Section::make('Deliberar recurso (coordenação)')
                    ->hiddenOn('create')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->disabled(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->schema([
                        Radio::make('status')
                            ->options(StatusExamEnum::class)
                            ->default(StatusExamEnum::EM_ANDAMENTO->value)
                            ->label('Parecer')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== StatusExamEnum::EM_ANDAMENTO->value) {
                                    $set('evaluated_by', auth()->id());
                                    $set('evaluated_at', now());
                                }
                            }),

                        RichEditor::make('final_judgment_reason')
                            ->label('Observações da coordenação')
                            ->columnSpan(2)
                            ->helperText('Campo para anotações sobre parecer.')
                            ->disabled(fn(Get $get): bool => $get('archived') === true)
                            ->dehydrated(),

                        Checkbox::make('archived')
                            ->columnSpanFull()
                            ->label('Encaminhado para a SETEB/Arquivado'),
                    ]),

                Section::make('Decisor do recurso')
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
                    ->searchable()
                    ->label('Nº'),

                TextColumn::make('user.platoon')
                    ->badge()
                    ->sortable()
                    ->label('Pelotão'),

                TextColumn::make('user.rg')
                    ->searchable()
                    ->label('Rg'),

                TextColumn::make('user.name')
                    ->searchable()
                    ->label('Nome'),

                TextColumn::make('discipline')
                    ->label('Disciplina'),

                TextColumn::make('question')
                    ->label('Questão/Item Avalidado')
                    ->limit(40),

                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                TextColumn::make('status')
                    ->badge()
                    ->label('Parecer do encaminhamento'),

                IconColumn::make('archived')
                    ->label('Encaminhado para a SETEB/Arquivado')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusExamEnum::class)
                    ->label('Parecer'),
                Filter::make('archived')
                    ->label('Encaminhado para a SETEB/Arquivado')
                    ->toggle(),
            ])
            ->actions([
                Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(ExamAppeal $record) => route('exam-appeal-pdf', $record))
                    ->openUrlInNewTab(),
                Action::make('archive')
                    ->label('Arquivar')
                    ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn(ExamAppeal $record) => $record->update(['archived' => true])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('archive')
                        ->label('Arquivar')
                        ->hidden(!auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        ->icon('heroicon-o-archive-box')
                        ->action(fn(Collection $records) => $records->each->update(['archived' => true])),
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
            'index' => Pages\ListExamAppeals::route('/'),
            'create' => Pages\CreateExamAppeal::route('/create'),
            'edit' => Pages\EditExamAppeal::route('/{record}/edit'),
        ];
    }
}
