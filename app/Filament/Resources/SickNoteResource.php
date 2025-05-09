<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\SickNoteResource\Pages;
use App\Models\SickNote;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class SickNoteResource extends Resource {
    protected static ?string $model = SickNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $label = 'Atestado Médico';

    protected static ?string $pluralModelLabel = 'Atestados Médicos';

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make('Enviar atestado médico')
                       ->icon('heroicon-o-pencil-square')
                       ->columns(2)
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
                                 ->label('Remetente')
                                 ->prefix('👨🏻‍🚒'),

                           FileUpload::make('file')
                                     ->optimize('jpg')
                                     ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                                     ->disk('s3')
                                     ->visibility('private')
                                     ->label('Atestado Médico')
                                     ->columnSpanFull()
                                     ->directory('sick-notes')
                                     ->openable()
                                     ->required()
                                     ->validationMessages([
                                         'required' => 'Favor inserir arquivo.',
                                     ])
                                     ->downloadable()
                                     ->maxSize(5000)
                                     ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                     ->getUploadedFileNameForStorageUsing(
                                         fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                             ->prepend(now()->format('Y-m-d') . '-atestado-medico-' . str_replace(' ', '_', auth()->user()->name) . '-' . now()->format('h-i-s') . '-')

                                     ),

                           DatePicker::make('date_issued')
                                     ->prefix('⏰️')
                                     ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                                     ->label('Data do atestado')
                                     ->displayFormat('d/m/y')
                                     ->native(false)
                                     ->required()
                                     ->default(now()),

                           TextInput::make('days_absent')
                                    ->prefix('🔢')
                                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                                    ->label('Quantidade de dias')
                                    ->default(1)
                                    ->required()
                                    ->minValue(1)
                                    ->numeric(),

                           Textarea::make('motive')
                                   ->required()
                                   ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                                   ->rows(5)
                                   ->helperText('Especificar motivo do atestado médico.')
                                   ->label('Motivo do atestado médico'),

                           Textarea::make('restrictions')
                                   ->required()
                                   ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                                   ->rows(5)
                                   ->helperText('Especificar restrições médicas com detalhes. Ex. Impedido de praticar corrida.')
                                   ->label('Restrições/Recomendações Médicas'),

                           RichEditor::make('observation')
                                     ->label('Observações')
                                     ->disabled(fn(string $operation, Get $get): bool => $get('received') === true && $get('archived') === true)
                                     ->dehydrated()
                                     ->columnSpanFull()
                                     ->disableToolbarButtons([
                                         'attachFiles',
                                     ]),

                           FileUpload::make('csau')
                                     ->optimize('jpg')
                                     ->disabled(fn(string $operation, Get $get): bool => $get('received') === true && $get('archived') === true)
                                     ->disk('s3')
                                     ->visibility('private')
                                     ->label('Anexo (Atestado Homologado pelo CSAU)')
                                     ->columnSpanFull()
                                     ->directory('sick-notes')
                                     ->openable()
                                     ->downloadable()
                                     ->maxSize(5000)
                                     ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                     ->getUploadedFileNameForStorageUsing(
                                         fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                                             ->prepend(now()->format('Y-m-d') . '-homologacao-csau-' . str_replace(' ', '_', auth()->user()->name) . '-' . now()->format('h-i-s') . '-')
                                     ),

                       ]),

                Section::make('Controle de dispensa médica (coordenação)')
                       ->icon('heroicon-o-chat-bubble-left-ellipsis')
                       ->disabled(!auth()
                           ->user()
                           ->hasAnyRole(['super_admin', 'admin']))
                       ->hiddenOn('create')
                       ->schema([
                           Checkbox::make('received')
                                   ->helperText('Marque se o atestado médico foi recebido pelo DABM.')
                                   ->label('Recebido/Ciente do DABM')
                                   ->live()
                                   ->afterStateUpdated(function ($state, callable $set) {
                                       if ($state !== StatusEnum::EM_ANDAMENTO->value) {
                                           $set('evaluated_by', auth()->id());
                                           $set('evaluated_at', now());
                                       }
                                   }),

                           Checkbox::make('ratified')
                                   ->helperText('Marque se o atestado médico foi homologado pelo Comando ou CSAU.')
                                   ->label('Homologado'),

                           Checkbox::make('archived')
                                   ->helperText('Marque se o atestado médico foi anexado no SEI e pode ser arquivado.')
                                   ->label('Anexado no SEI/Arquivado'),
                       ]),

                Section::make('Recepção do Atestado Médico')
                       ->hiddenOn('create')
                       ->columns(2)
                       ->hidden(fn(Get $get): bool => $get('received') === false)
                       ->icon('heroicon-o-inbox-arrow-down')
                       ->schema([
                           Select::make('evaluated_by')
                                 ->label('Recebido por')
                                 ->prefix('👨🏻‍⚖️')
                                 ->relationship('evaluator', 'name')
                                 ->disabled()
                                 ->dehydrated(),

                           DateTimePicker::make('evaluated_at')
                                         ->prefix('📆️️')
                                         ->label('Recebido em')
                                         ->seconds(false)
                                         ->displayFormat('d/m/y H:i')
                                         ->native(false)
                                         ->disabled()
                                         ->dehydrated(),
                       ]),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()
                    ->user()
                    ->hasExactRoles('panel_user')) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                          ->searchable()
                          ->label('Nº'),

                TextColumn::make('user.platoon')
                          ->badge()
                          ->label('Pelotão'),

                TextColumn::make('user.rg')
                          ->searchable()
                          ->label('Rg'),

                TextColumn::make('user.name')
                          ->searchable()
                          ->label('Nome'),

                TextColumn::make('created_at')
                          ->dateTime('d/m/y H:i')
                          ->sortable()
                          ->label('Data de envio'),

                TextColumn::make('date_issued')
                          ->dateTime('d/m/y')
                          ->sortable()
                          ->label('Data do atestado'),

                TextColumn::make('days_absent')
                          ->label('Dias afastado'),

                TextColumn::make('day_back')
                          ->dateTime('d/m/y')
                          ->label('Data de retorno')
                          ->sortable(query: function (Builder $query, string $direction): Builder {
                              return $query->orderByRaw("(date_issued + INTERVAL '1 day' * days_absent) " . $direction);
                          }),

                TextColumn::make('motive')
                          ->limit(40)
                          ->label('Motivo'),

                IconColumn::make('received')
                          ->label('Recebido/Ciente do DABM')
                          ->boolean()
                          ->alignCenter(),

                IconColumn::make('ratified')
                          ->label('Homologado')
                          ->boolean()
                          ->alignCenter(),

                IconColumn::make('archived')
                          ->label('Anexado no SEI/Arquivado')
                          ->boolean()
                          ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                            ->options(StatusEnum::class)
                            ->label('Parecer'),
                SelectFilter::make('date_issued')
                            ->label('Atestados em vigor')
                            ->options([
                                'ongoing' => 'Vigentes',
                            ])
                            ->query(function (Builder $query, array $data): Builder {
                                if ($data['value'] === 'ongoing') {
                                    return $query->whereDate('date_issued', '>=', Carbon::today()
                                                                                        ->subDays($query->first()->days_absent ?? 0));
                                }
                                return $query;
                            }),
                Filter::make('received')
                      ->label("Recebido/Ciente do DABM")
                      ->toggle(),
                Filter::make('archived')
                      ->label("Anexado no SEI/Arquivado")
                      ->toggle(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('archive')
                      ->label('Arquivar')
                      ->hidden(!auth()
                          ->user()
                          ->hasAnyRole(['super_admin', 'admin']))
                      ->icon('heroicon-o-archive-box')
                      ->color('gray')
                      ->action(fn(SickNote $record) => $record->update(['archived' => true]))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('archive')
                              ->label('Arquivar')
                              ->hidden(!auth()
                                  ->user()
                                  ->hasAnyRole(['super_admin', 'admin']))
                              ->icon('heroicon-o-archive-box')
                              ->action(fn(Collection $records) => $records->each->update(['archived' => true])),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListSickNotes::route('/'),
            'create' => Pages\CreateSickNote::route('/create'),
            'edit' => Pages\EditSickNote::route('/{record}/edit'),
        ];
    }
}
