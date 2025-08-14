<?php

namespace App\Filament\Resources;

// App - Resources
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use App\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Resources\EmployeeResource\RelationManagers;

// App - Models
use App\Models\Employee;
use App\Models\User;

// Fillament - Core
use Filament\Resources\Resource;

// Shield
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

// Fillament - Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

// Fillament - Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Laravel
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Spatie Permission
use Spatie\Permission\Models\Role;

class EmployeeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Human Resources';

    // Shield permission prefixes
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

    // Shield permission checks
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->can('view_any_employee');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Hanya HR dan admin yang bisa create employee
        return $user->can('create_employee') || $user->hasRole('hr');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->can('update_employee');
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->can('delete_employee') && $user->hasRole('hr');
    }

    // Filter data berdasarkan divisi untuk non-admin
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Super admin bisa lihat semua
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // HR bisa lihat semua employee
        if ($user->hasRole('hr')) {
            return $query;
        }

        // User lain hanya bisa lihat employee di divisi yang sama
        $userEmployee = $user->employee;
        if ($userEmployee && $userEmployee->division_id) {
            $query->where('division_id', $userEmployee->division_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('User')
                    ->required()
                    ->options(function () {
                        $editingId = request()->route('record');
                        if ($editingId) {
                            // Edit mode: show all users, but mark the current one as selected
                            $employee = Employee::find($editingId);
                            $users = User::where(function ($query) use ($employee) {
                                $query->whereDoesntHave('employee')
                                      ->orWhere('id', $employee->user_id);
                            })->pluck('name', 'id');
                        } else {
                            // Create mode: only users without employee
                            $users = User::doesntHave('employee')->pluck('name', 'id');
                        }

                        return $users->isNotEmpty()
                            ? $users
                            : ['' => 'Semua user sudah memiliki data employee'];
                    })
                    ->searchable()
                    ->disabled(fn (string $context): bool => $context === 'edit')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Auto-suggest role based on division
                        if ($state) {
                            $user = User::find($state);
                            if ($user && $user->employee && $user->employee->division) {
                                $divisionName = strtolower($user->employee->division->division_name);
                                $set('user_role', $divisionName);
                            }
                        }
                    }),

                Select::make('division_id')
                    ->options(\App\Models\Division::all()->pluck('division_name', 'id'))
                    ->label('Division')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        // Auto-suggest role based on division
                        if ($state) {
                            $division = \App\Models\Division::find($state);
                            if ($division) {
                                $divisionName = strtolower($division->division_name);
                                $set('user_role', $divisionName);
                            }
                        }
                    }),

                // Field untuk memilih role user
                Select::make('user_role')
                    ->label('User Role')
                    ->options(function () {
                        return Role::where('name', '!=', 'super_admin')
                            ->pluck('name', 'name')
                            ->mapWithKeys(function ($name) {
                                return [$name => ucfirst($name)];
                            });
                    })
                    ->searchable()
                    ->helperText('Role akan otomatis di-assign ke user yang dipilih')
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'hr'])),

                TextInput::make('full_name')
                    ->required()
                    ->placeholder('Masukkan nama lengkap...'),

                Select::make('gender')
                    ->label('Gender')
                    ->required()
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                DatePicker::make('birth_date')
                    ->required(),

                TextInput::make('phone_number')
                    ->numeric()
                    ->tel()
                    ->required()
                    ->placeholder('e.g. +1234567890'),

                Textarea::make('address')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Masukkan alamat...'),

                FileUpload::make('image_path')
                    ->image()
                    ->directory('employees'),

                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Profile Picture')
                    ->circular()
                    ->size(50),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('division.division_name')
                    ->label('Division')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    }),
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('division_id')
                    ->label('Division')
                    ->relationship('division', 'division_name'),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
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
                EditAction::make(),
                // ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}