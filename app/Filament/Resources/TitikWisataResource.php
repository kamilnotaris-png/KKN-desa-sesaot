<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TitikWisataResource\Pages;
use App\Filament\Resources\TitikWisataResource\RelationManagers;
use App\Models\TitikWisata;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TitikWisataResource extends Resource
{
    protected static ?string $model = TitikWisata::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Titik Wisata';

    protected static ?string $modelLabel = 'titik wisata';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatis terisi dari nama, dipakai di URL halaman detail.'),
                Forms\Components\Select::make('kategori')
                    ->options(TitikWisata::KATEGORI)
                    ->required(),
                Forms\Components\TextInput::make('dusun')
                    ->required(),
                Forms\Components\Textarea::make('deskripsi')
                    ->helperText('Deskripsi singkat untuk daftar/kartu titik wisata.')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('cerita_lokal')
                    ->helperText('Cerita/narasi lokal yang ditampilkan di halaman detail & dibacakan di video.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric()
                    ->step('0.0000001')
                    ->helperText('Hasil drop pin GPS survei lapangan.'),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric()
                    ->step('0.0000001'),
                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->directory('titik-wisata')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_youtube_url')
                    ->label('Link Video YouTube')
                    ->url()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('urutan')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif / tampil di peta publik')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label(''),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->formatStateUsing(fn (string $state) => TitikWisata::KATEGORI[$state] ?? $state)
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('dusun')
                    ->searchable(),
                Tables\Columns\TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options(TitikWisata::KATEGORI),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktif'),
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
            'index' => Pages\ListTitikWisatas::route('/'),
            'create' => Pages\CreateTitikWisata::route('/create'),
            'edit' => Pages\EditTitikWisata::route('/{record}/edit'),
        ];
    }
}
