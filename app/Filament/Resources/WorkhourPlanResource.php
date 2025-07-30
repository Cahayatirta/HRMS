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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
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
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DatePicker::make('plan_date')
                    ->required(),
                TimePicker::make('planned_starttime')
                    ->required(),
                TimePicker::make('planned_endtime')
                    ->required(),
                Select::make('work_location')
                    ->options([
                        'office' => 'Office',
                        'anywhere' => 'Anywhere',
                    ])
                    ->default('office')
                    ->required(),
                Toggle::make('is_deleted')
                    ->hidden(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('planned_starttime'),
                TextColumn::make('planned_endtime'),
                TextColumn::make('work_location')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Deleted Status')
                ->options([
                    'active' => 'Active',
                    'deleted' => 'Deleted',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    if ($data['value'] === 'deleted') {
                        return $query->where('is_deleted', true);
                    }
                    return $query->where('is_deleted', false);
                }),
            ])
            ->actions([
                // ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            // ->tabs([
            //     'Aktif' => fn (Builder $query) => $query->where('is_deleted', false),
            //     'Terhapus' => fn (Builder $query) => $query->where('is_deleted', true),
            // ]);
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
