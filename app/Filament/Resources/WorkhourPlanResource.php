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

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class WorkhourPlanResource extends Resource
{
    protected static ?string $model = WorkhourPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Human Resources';

    /**
     * Shield permission prefixes
     */
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    /**
     * Check if user can view any records
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        
        // Super admin can access everything
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Check specific permission
        return $user && $user->can('view_any_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can create records
     */
    public static function canCreate(): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }
        
        return $user && $user->can('create_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can edit specific record
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Basic permission check
        if (!$user || !$user->can('update_' . strtolower(class_basename(static::$model)))) {
            return false;
        }

        // CUSTOM LOGIC untuk role "user"
        if ($user->hasRole('user')) {
            // User hanya bisa edit workhour plan milik sendiri
            $userEmployee = $user->employee;
            if (!$userEmployee) {
                return false;
            }
            
            // Cek apakah record ini milik user yang sedang login
            return $record->employee_id === $userEmployee->id;
        }

        return true;
    }

    /**
     * Check if user can delete specific record
     */
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // CUSTOM LOGIC untuk role "user"
        if ($user->hasRole('user')) {
            // User hanya bisa delete workhour plan milik sendiri
            $userEmployee = $user->employee;
            if (!$userEmployee) {
                return false;
            }
            
            // Cek apakah record ini milik user yang sedang login
            return $record->employee_id === $userEmployee->id;
        }

        return $user && $user->can('delete_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can delete any records (bulk delete)
     */
    public static function canDeleteAny(): bool
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        return $user && $user->can('delete_any_' . strtolower(class_basename(static::$model)));
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isUserRole = $user && $user->hasRole('user');
        
        return $form
            ->schema([
                Select::make('employee_id')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->default(function () use ($isUserRole, $user) {
                        // dd($isUserRole, $user);
                        // Jika user role, set default ke employee yang sedang login
                        if ($isUserRole && $user->employee) {
                            return $user->employee->id;
                        }
                        return null;
                    })
                    ->disabled($isUserRole) // Disable field untuk user role
                    ->dehydrated($isUserRole), // Tetap submit value meski disabled
                    
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
            ->query(static::getEloquentQuery())
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
                EditAction::make()
                    ->visible(fn ($record) => static::canEdit($record)),
                // ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Override getEloquentQuery to filter workhour plans for regular users
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        
        // Jika user memiliki role "user", filter hanya workhour plan milik mereka
        if ($user && $user->hasRole('user')) {
            $userEmployee = $user->employee;
            if ($userEmployee) {
                $query->where('employee_id', $userEmployee->id);
            } else {
                // Jika user tidak memiliki employee record, tidak tampilkan data apapun
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
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