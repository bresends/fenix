<?php

namespace App\Filament\Resources;

use App\Enums\StatusEnum;
use App\Filament\Resources\SickNoteResource\Pages;
use App\Models\SickNote;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SickNoteResource extends Resource
{
    protected static ?string $model = SickNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-arrow-down';

    protected static ?string $label = 'Atestado Médico';

    protected static ?string $pluralModelLabel = 'Atestados Médicos';

    protected static ?string $navigationGroup = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Enviar atestado médico')
                    ->disabledOn('edit')
                    ->disabled(fn(string $operation, Get $get): bool => ($operation === 'edit' && $get('user_id') !== auth()->user()->id) || $get('received') === true)
                    ->columns(2)
                    ->schema([
                        FileUpload::make('file')
                            ->disk('s3')
                            ->visibility('private')
                            ->label('Arquivo')
                            ->columnSpan(2)
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
                                    ->prepend('atestado-medico-' . now()->format('Y-m-d') . '-' . auth()->user()->name),
                            ),

                        DatePicker::make('date_issued')
                            ->prefix('⏰️')
                            ->label('Data do atestado')
                            ->displayFormat('d/m/y')
                            ->native(false)
                            ->required()
                            ->default(now()),

                        TextInput::make('days_absent')
                            ->prefix('🔢')
                            ->label('Quantidade de dias')
                            ->default(1)
                            ->required()
                            ->minValue(1)
                            ->numeric(),

                        Textarea::make('motive')
                            ->required()
                            ->rows(5)
                            ->helperText('Especificar motivo do atestado médico.')
                            ->label('Motivo do atestado médico'),

                        Textarea::make('restrictions')
                            ->required()
                            ->rows(5)
                            ->helperText('Especificar restrições médicas com detalhes. Ex. Impedido de praticar corrida.')
                            ->label('Restrições/Recomendações Médicas'),


                    ]),
                Section::make('Controle de dispensa médica')
                    ->schema([
                        Checkbox::make('received')
                            ->columnSpan(2)
                            ->helperText('Marque se o atestado médico foi recebido pelo DABM.')
                            ->label('Recebido/Ciente do DABM'),

                        Checkbox::make('archived')
                            ->columnSpan(2)
                            ->helperText('Marque se o atestado médico foi anexado no SEI e pode ser arquivado.')
                            ->label('Anexado no SEI/Arquivado'),
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

                TextColumn::make('dayBack')
                    ->dateTime('d/m/y')
                    ->sortable()
                    ->label('Data de retorno'),

                TextColumn::make('motive')
                    ->limit(40)
                    ->label('Motivo'),

                IconColumn::make('received')
                    ->label('Recebido/Ciente do DABM')
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
                Filter::make('received')
                    ->label("Recebido/Ciente do DABM")
                    ->toggle(),
                Filter::make('archived')
                    ->label("Anexado no SEI/Arquivado")
                    ->toggle()
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
            'index' => Pages\ListSickNotes::route('/'),
            'create' => Pages\CreateSickNote::route('/create'),
            'edit' => Pages\EditSickNote::route('/{record}/edit'),
        ];
    }
}
