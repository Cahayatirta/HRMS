<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkhourPlanResource\Pages;
use App\Filament\Resources\WorkhourPlanResource\RelationManagers;
use App\Models\WorkhourPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkhourPlanResource extends Resource
{
    protected static ?string $model = WorkhourPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                Forms\Components\DatePicker::make('plan_date')
                    ->required(),
                Forms\Components\TextInput::make('planned_starttime')
                    ->required(),
                Forms\Components\TextInput::make('planned_endtime')
                    ->required(),
                Forms\Components\TextInput::make('work_location')
                    ->required(),
                Forms\Components\Toggle::make('is_deleted')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('planned_starttime'),
                Tables\Columns\TextColumn::make('planned_endtime'),
                Tables\Columns\TextColumn::make('work_location')
                    ->searchable(),
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
            'index' => Pages\ListWorkhourPlans::route('/'),
            'create' => Pages\CreateWorkhourPlan::route('/create'),
            'edit' => Pages\EditWorkhourPlan::route('/{record}/edit'),
        ];
    }
}
