<?php

namespace App\Filament\Resources;

// Model
use App\Models\Task;

// Filament Resource
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

// Filament Resource Pages
use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Filament\Resources\TaskResource\Pages\CreateTask;

// Filament Tables - Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;

// Filament Tables - Actions
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Filament Tables - Filters
use Filament\Tables\Filters\SelectFilter;

// Filament Forms - Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Repeater;

// Laravel
use Illuminate\Database\Eloquent\Builder;

// Filament Plugin - Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Project Management';

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
        
        if (!$user) {
            return false;
        }

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

        if (!$user) {
            return false;
        }
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Role "user" tidak bisa create task
        if ($user && $user->hasRole('user')) {
            return false;
        }

        return $user && $user->can('create_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can edit specific record
     */
    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Basic permission check
        if (!$user || !$user->can('update_' . strtolower(class_basename(static::$model)))) {
            return false;
        }

        // CUSTOM LOGIC untuk role "user"
        if ($user->hasRole('user')) {
            // Cek apakah user memiliki employee record
            $userEmployee = $user->employee;
            if (!$userEmployee) {
                return false;
            }

            // Cek apakah task ini di-assign ke user yang sedang login
            $isAssignedToUser = $record->employees()->where('employee_id', $userEmployee->id)->exists();
            
            // Jika tidak di-assign ke user ini, tidak bisa edit
            if (!$isAssignedToUser) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user can delete specific record
     */
    public static function canDelete($record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Role "user" tidak bisa delete task
        if ($user && $user->hasRole('user')) {
            return false;
        }

        return $user && $user->can('delete_' . strtolower(class_basename(static::$model)));
    }

    /**
     * Check if user can delete any records (bulk delete)
     */
    public static function canDeleteAny(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        
        if ($user && $user->hasRole('super_admin')) {
            return true;
        }

        // Role "user" tidak bisa bulk delete
        if ($user && $user->hasRole('user')) {
            return false;
        }

        return $user && $user->can('delete_any_' . strtolower(class_basename(static::$model)));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('task_name')
                ->required()
                ->columnSpanFull(),

            Textarea::make('task_description')
                ->columnSpanFull(),

            DatePicker::make('deadline')
                ->required(),

            Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'issue' => 'Issue',
                    'cancelled' => 'Cancelled',
                ])
                ->default('pending')
                ->required(),

            Textarea::make('note')
                ->columnSpanFull(),

            Select::make('Employee')
                ->relationship('employees', 'full_name') 
                ->multiple()
                ->label('Assigned Employee')
                ->nullable()
                ->default(0)
                ->columnSpanFull()
                ->preload()
                ->disabled(fn () => auth()->user()->hasRole('user')), // Disable untuk role user

            Repeater::make('subtasks')
                ->relationship('subtasks')
                ->label('Subtasks')
                ->schema([
                    TextInput::make('task_name')
                        ->required()
                        ->label('Subtask Name'),

                    Select::make('Employee')
                        ->relationship('employees', 'full_name') 
                        ->multiple()
                        ->label('Assigned Employee')
                        ->nullable()
                        ->default(0)
                        ->preload()
                        ->disabled(fn () => auth()->user()->hasRole('user')), // Disable untuk role user

                    DatePicker::make('deadline')
                        ->label('Subtask Deadline'),

                    Select::make('status')
                        ->label('Subtask Status')
                        ->options([
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                            'issue' => 'Issue',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('pending')
                        ->required(),

                    Textarea::make('note')
                        ->label('Subtask Note'),

                    Textarea::make('task_description')
                        ->label('Subtask Description'),

                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery())
            ->columns([
                TextColumn::make('task_name')
                    ->searchable(),

                TextColumn::make('deadline')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'issue' => 'danger',
                        'cancelled' => 'secondary',
                        default => 'gray',
                    }),

                ImageColumn::make('employees.image_path')
                    ->circular()
                    ->stacked(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
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
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Override getEloquentQuery to filter tasks for regular users
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('parent_task_id', '=', null)
            ->where('is_deleted', '=', 0);

        $user = auth()->user();
        
        // Jika user memiliki role "user", filter hanya task yang di-assign ke mereka
        if ($user && $user->hasRole('user')) {
            $userEmployee = $user->employee;
            if ($userEmployee) {
                $query->whereHas('employees', function ($q) use ($userEmployee) {
                    $q->where('employee_id', $userEmployee->id);
                });
            } else {
                // Jika user tidak memiliki employee record, tidak tampilkan task apapun
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan RelationManager di sini jika diperlukan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}