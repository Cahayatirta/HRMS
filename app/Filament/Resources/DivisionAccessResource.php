<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionAccessResource\Pages;
use App\Filament\Resources\DivisionAccessResource\RelationManagers;
use App\Models\DivisionAccess;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DivisionAccessResource extends Resource
{
    protected static ?string $model = DivisionAccess::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('division_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('access_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_deleted')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('division_id')
                    ->numeric()
                    ->sortable(), 
                Tables\Columns\TextColumn::make('access_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_deleted')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDivisionAccesses::route('/'),
            'create' => Pages\CreateDivisionAccess::route('/create'),
            'edit' => Pages\EditDivisionAccess::route('/{record}/edit'),
        ];
    }
}
