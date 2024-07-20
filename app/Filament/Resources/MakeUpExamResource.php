<?php

namespace App\Filament\Resources;

use App\Enums\MakeUpExamStatusEnum;
use App\Enums\StatusEnum;
use App\Filament\Resources\MakeUpExamResource\Pages;
use App\Models\ExamAppeal;
use App\Models\MakeUpExam;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MakeUpExamResource extends Resource
{
    protected static ?string $model = MakeUpExam::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $label = 'Solicitação de 2ª Chamada';

    protected static ?string $pluralModelLabel = 'Solicitações de 2ª Chamada';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Solicitar segunda chamada')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->icon('heroicon-o-pencil-square')
                    ->columns(2)
                    ->schema([
                        TextInput::make('discipline_name')
                            ->label('Nome da disciplina (conforme consta no Plano de Curso)')
                            ->placeholder('Salvamento Terrestre')
                            ->required(),

                        DatePicker::make('exam_date')
                            ->prefix('⏰️')
                            ->label('Data da avaliação não realizada')
                            ->displayFormat('d/m/y')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        Select::make('type')
                            ->options(MakeUpExamStatusEnum::class)
                            ->label('Tipo')
                            ->prefix('🏷️')
                            ->native(false)
                            ->default('Teórica')
                            ->required(),

                        DatePicker::make('date_back')
                            ->prefix('⬅️')
                            ->label('Data em que ficou apto para realizar a avaliação')
                            ->displayFormat('d/m/y')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        RichEditor::make('motive')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->hint('Atenção!')
                            ->hintIcon('heroicon-m-exclamation-triangle', tooltip: 'Liste detalhadamente o motivo')
                            ->hintColor('primary')
                            ->columnSpanFull()
                            ->placeholder('Solicito segunda chamada de prova devido a minha baixa médica conforme atestado médico nº 22 (anexado no sistema). Fui orientado(a) a permanecer em repouso e seguir um tratamento imediato, o que impedira a realização da prova.')
                            ->label('Motivo da não realização da avaliação (com detalhes)'),

                        FileUpload::make('file')
                            ->disk('r2')
                            ->visibility('private')
                            ->label('Arquivo')
                            ->columnSpanFull()
                            ->directory('makeup-exams')
                            ->openable()
                            ->downloadable()
                            ->helperText('Anexo (Imagem ou PDF) que justifique ausência. Ex: atestados médicos, convocações judiciais ou outros arquivos (se houver)')
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                    ->prepend('segunda-chamada-'),
                            ),

                    ]),

                Section::make('Deliberar 2ª Chamada (coordenação)')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->hiddenOn('create')
                    ->disabled(!auth()->user()->hasRole('super_admin'))
                    ->schema([
                        Radio::make('status')
                            ->options(StatusEnum::class)
                            ->default(StatusEnum::EM_ANDAMENTO->value)
                            ->label('Parecer'),

                        RichEditor::make('final_judgment_reason')
                            ->helperText('Campo para anotações sobre parecer.')
                            ->label('Observações da coordenação'),

                        Checkbox::make('archived')
                            ->helperText('Segunda chamada concluída')
                            ->label('Encaminhada para a SETEB/Arquivada'),
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
                    ->label('Nº'),

                TextColumn::make('user.platoon')
                    ->label('Pelotão')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime('d/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                TextColumn::make('discipline_name')
                    ->label('Disciplina'),

                TextColumn::make('type')
                    ->badge()
                    ->label('Tipo'),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->label('Parecer'),

                TextColumn::make('motive')
                    ->limit(45)
                    ->toggleable()
                    ->html()
                    ->label('Motivo'),

                IconColumn::make('archived')
                    ->label('Encaminhada para a SETEB/Arquivada')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StatusEnum::class)
                    ->label('Parecer'),
                Filter::make('archived')
                    ->label("Arquivada")
                    ->toggle()
            ])
            ->actions([
                Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn(MakeUpExam $record) => route('make-up-exam-pdf', $record))
                    ->openUrlInNewTab(),
                Action::make('archive')
                    ->label('Arquivar')
                    ->hidden(!auth()->user()->hasRole('super_admin'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->action(fn(MakeUpExam $record) => $record->update(['archived' => true]))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('archive')
                        ->label('Arquivar')
                        ->hidden(!auth()->user()->hasRole('super_admin'))
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
            'index' => Pages\ListMakeUpExams::route('/'),
            'create' => Pages\CreateMakeUpExam::route('/create'),
            'edit' => Pages\EditMakeUpExam::route('/{record}/edit'),
        ];
    }
}
