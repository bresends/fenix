<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SickNoteResource\Pages;
use App\Models\SickNote;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SickNoteResource extends Resource
{
    protected static ?string $model = SickNote::class;

    protected static ?string $navigationIcon = 'heroicon-o-battery-50';

    protected static ?string $label = 'Atestado Médico';

    protected static ?string $pluralModelLabel = 'Atestados Médicos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('file')
                    ->disk('public')
                    ->visibility('public')
                    ->label('Arquivo')
                    ->columnSpan(2)
                    ->directory('sick-notes')
                    ->openable()
                    ->required()
                    ->downloadable()
                    ->maxSize(5000)
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file): string => (string)str($file->getClientOriginalName())
                            ->prepend('atestado-medico-'),
                    ),

                DatePicker::make('date_issued')
                    ->prefix('⏰️')
                    ->label('Data do Atestado')
                    ->timezone('America/Sao_Paulo')
                    ->displayFormat('d-m-Y')
                    ->native(false)
                    ->required()
                    ->default(now()),

                TextInput::make('days_absent')
                    ->prefix('🔢')
                    ->label('Dias ausente')
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
                    ->label('Restrições'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->hasRole('panel_user')) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->columns([

                TextColumn::make('user.platoon')
                    ->badge()
                    ->label('Pelotão'),

                TextColumn::make('userRank')
                    ->label('Posto/Grad'),

                TextColumn::make('user.rg')
                    ->label('Rg'),

                TextColumn::make('user.name')
                    ->label('Nome'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime($format = 'd-m-Y')
                    ->sortable()
                    ->label('Data de envio'),

                Tables\Columns\TextColumn::make('date_issued')
                    ->dateTime($format = 'd-m-Y')
                    ->sortable()
                    ->label('Data do atestado'),


                Tables\Columns\TextColumn::make('days_absent')
                    ->label('Dias afastado'),

                Tables\Columns\TextColumn::make('dayBack')
                    ->dateTime($format = 'd-m-Y')
                    ->sortable()
                    ->label('Data de retorno'),

                Tables\Columns\TextColumn::make('motive')
                    ->limit(40)
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
            'index' => Pages\ListSickNotes::route('/'),
            'create' => Pages\CreateSickNote::route('/create'),
            'edit' => Pages\EditSickNote::route('/{record}/edit'),
        ];
    }
}
