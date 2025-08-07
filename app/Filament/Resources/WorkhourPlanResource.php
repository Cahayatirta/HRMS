<?php

namespace App\Filament\Resources;

// Model
use App\Models\WorkhourPlan;

// Filament Resource
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

// Filament Resource Pages
use App\Filament\Resources\WorkhourPlanResource\Pages;
use App\Filament\Resources\WorkhourPlanResource\Pages\ListWorkhourPlans;
use App\Filament\Resources\WorkhourPlanResource\Pages\CreateWorkhourPlan;
use App\Filament\Resources\WorkhourPlanResource\Pages\EditWorkhourPlan;

// Filament Resource Relation Managers
use App\Filament\Resources\WorkhourPlanResource\RelationManagers;

// Filament Tables - Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;

// Filament Tables - Actions
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Filament Tables - Filters
use Filament\Tables\Filters\SelectFilter;

// Filament Forms - Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;

// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Opsional (tidak dipakai langsung, bisa dihapus jika tidak digunakan)
use Filament\Forms;
use Filament\Tables;

class WorkhourPlanResource extends Resource
{
    protected static ?string $model = WorkhourPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->relationship('employee', 'full_name')
                    // ->options(\App\Models\User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->preload(),
                DatePicker::make('plan_date')
                    ->required(),
                TimePicker::make('planned_starttime')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('planned_endtime')
                    ->seconds(false)
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
                TextColumn::make('employee.full_name')
                    ->label('Employee')
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
                    ->toggleable(isToggledHiddenByDefault: true),
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
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListWorkhourPlans::route('/'),
            'create' => CreateWorkhourPlan::route('/create'),
            'edit' => EditWorkhourPlan::route('/{record}/edit'),
        ];
    }
}
