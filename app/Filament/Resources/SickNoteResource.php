<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SickNoteResource\Pages;
use App\Models\SickNote;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
                    ->label('Arquivo')
                    ->columnSpan(2)
                    ->directory('sick-notes')
                    ->openable()
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                            ->prepend('atestado-'),
                    ),
                DatePicker::make('date_issued')
                    ->prefix('⏰️')
                    ->label(__('Data do Atestado'))
                    ->timezone('America/Sao_Paulo')
                    ->displayFormat('d-m-Y')
                    ->native(false)
                    ->required()
                    ->default(now()),
                TextInput::make('days_absent')
                    ->prefix('🔢')
                    ->label('Dias ausente')
                    ->default(1)
                    ->minValue(1)
                    ->numeric(),
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
                TextColumn::make('user.rg')->label('Rg'),
                TextColumn::make('user.name')->label('Nome'),
                Tables\Columns\TextColumn::make('date_issued')
                    ->dateTime($format = 'd-m-Y')
                    ->sortable()
                    ->label('Data do Atestado'),
                Tables\Columns\TextColumn::make('days_absent')
                    ->label('Dias Afastado'),
                Tables\Columns\IconColumn::make('file')
                    ->label('Contém arquivo?')
                    ->boolean()
                    ->searchable(),
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
