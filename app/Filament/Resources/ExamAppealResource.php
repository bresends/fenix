<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Enums\MakeUpExamStatusEnum;
use App\Filament\Resources\ExamAppealResource\Pages;
use App\Models\ExamAppeal;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                    ->disabled(fn (string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('status') !== 'Em andamento')
                    ->schema([
                        TextInput::make('discipline')
                            ->label('Nome da disciplina (conforme consta no Plano de Curso)')
                            ->placeholder('Salvamento Terrestre')
                            ->required(),

                        Select::make('type')
                            ->options(MakeUpExamStatusEnum::class)
                            ->label('Tipo')
                            ->prefix('🏷️')
                            ->native(false)
                            ->default('Teórica')
                            ->required(),

                        TextInput::make('exam')
                            ->label('Prova')
                            ->placeholder('N2')
                            ->required(),

                        TextInput::make('question')
                            ->label('Questão')
                            ->placeholder('12')
                            ->required()
                            ->numeric(),

                        RichEditor::make('motive')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpan(2)
                            ->label('Fundamentação do recurso'),

                        RichEditor::make('bibliography')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                            ])
                            ->columnSpan(2)
                            ->label('Bibliografia'),

                        Checkbox::make('accept_terms')
                            ->columnSpan(2)
                            ->accepted()
                            ->validationMessages([
                                'accepted' => 'Dê ciência',
                            ])
                            ->helperText('Estou ciente das regras e condições estabelecidas na Norma de Ensino 01 do Comando da Academia e Ensino Bombeiro Militar, em especial quanto ao que consta no Capítulo VIII – Apresentação de Recurso (art. 20 ao 24).')
                            ->label('Declaro ciência da regras e condições estabelecidas na NE 01.'),

                        FileUpload::make('file')
                            ->disk('public')
                            ->visibility('public')
                            ->label('Anexos (se houver)')
                            ->directory('exam-appeal')
                            ->openable()
                            ->columnSpan(2)
                            ->downloadable()
                            ->maxSize(5000)
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend('recurso-'),
                            ),

                    ]),

                Section::make('Deliberar recurso (coordenação)')
                    ->description('Determine se o recurso será autorizada.')
                    ->hiddenOn('create')
                    ->disabled(! auth()->user()->hasRole('super_admin'))
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

                        Checkbox::make('archived')
                            ->columnSpan(2)
                            ->label('Arquivado'),
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
                    ->label('Nome'),

                TextColumn::make('discipline')
                    ->label('Disciplina')
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Questão')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime($format = 'd/m/y H:i')
                    ->sortable()
                    ->label('Solicitado em'),

                TextColumn::make('status')
                    ->badge()
                    ->searchable()
                    ->label('Parecer'),
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
            'index' => Pages\ListExamAppeals::route('/'),
            'create' => Pages\CreateExamAppeal::route('/create'),
            'edit' => Pages\EditExamAppeal::route('/{record}/edit'),
        ];
    }
}
