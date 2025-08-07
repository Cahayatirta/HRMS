<?php

namespace App\Filament\Resources;

// Laravel - Eloquent
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// Filament - Core
use Filament\Resources\Resource;

// Filament - Forms
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

// Filament - Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;

// App - Models
use App\Models\Access;
use App\Models\Division;
use App\Models\DivisionAccess;

// App - Filament Resources
use App\Filament\Resources\DivisionResource\Pages;
use App\Filament\Resources\DivisionResource\Pages\CreateDivision;
use App\Filament\Resources\DivisionResource\Pages\EditDivision;
use App\Filament\Resources\DivisionResource\Pages\ListDivisions;
use App\Filament\Resources\DivisionResource\RelationManagers;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('division_name')
                    ->required(),
                TextInput::make('required_workhours')
                    ->required()
                    ->numeric()
                    ->default(8),

                Grid::make(2)
                    ->schema([
                        // Client Access
                        Select::make('client_access')
                            ->label('Client Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%client%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'client');
                            })
                            ->placeholder('Select client permissions')
                            ->helperText('Select CRUD operations for client management'),

                        // service Access
                        Select::make('service_access')
                            ->label('Service Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%service')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'service');
                            })
                            // ->preload()
                            ->placeholder('Select service permissions')
                            ->helperText('Select CRUD operations for service management'),

                        // service type Access
                        Select::make('service_type_access')
                            ->label('Service Type Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%service type%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'service type');
                            })
                            // ->preload()
                            ->placeholder('Select service type permissions')
                            ->helperText('Select CRUD operations for service type management'),

                        // division Access
                        Select::make('division_access')
                            ->label('Division Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%division%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'division');
                            })
                            // ->preload()
                            ->placeholder('Select division permissions')
                            ->helperText('Select CRUD operations for division management'),
                        
                        // Employee Access
                        Select::make('employee_access')
                            ->label('Employee Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%employee%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'employee');
                            })
                            // ->preload()
                            ->placeholder('Select employee permissions')
                            ->helperText('Select CRUD operations for employee management'),
                        
                        // workhour plan Access
                        Select::make('workhour_plan_access')
                            ->label('Workhour Plan Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%workhour plan%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'workhour plan');
                            })
                            // ->preload()
                            ->placeholder('Select workhour plan permissions')
                            ->helperText('Select CRUD operations for workhour plan management'),

                        // attendance Access
                        Select::make('attendance_access')
                            ->label('Attendance Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%attendance%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'attendance');
                            })
                            // ->preload()
                            ->placeholder('Select attendance permissions')
                            ->helperText('Select CRUD operations for attendance management'),
                        
                        // metting Access
                        Select::make('metting_access')
                            ->label('Metting Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%metting%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'metting');
                            })
                            // ->preload()
                            ->placeholder('Select metting permissions')
                            ->helperText('Select CRUD operations for metting management'),

                        // task Access
                        Select::make('task_access')
                            ->label('Task Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%task%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'task');
                            })
                            // ->preload()
                            ->placeholder('Select task permissions')
                            ->helperText('Select CRUD operations for task management'),

                        // user Access
                        Select::make('user_access')
                            ->label('User Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%user%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'user');
                            })
                            // ->preload()
                            ->placeholder('Select user permissions')
                            ->helperText('Select CRUD operations for user management'),

                        // access Access
                        Select::make('access_access')
                            ->label('Access Access')
                            ->multiple()
                            ->options(function () {
                                return Access::where('access_name', 'like', '%access%')
                                    ->where('is_deleted', false)
                                    ->pluck('access_name', 'id');
                            })
                            ->default(function (Get $get) {
                                return static::getExistingAccess($get('id'), 'access');
                            })
                            // ->preload()
                            ->placeholder('Select access permissions')
                            ->helperText('Select CRUD operations for access management'),

                        
                    ]),
            ]);
    }

    protected static function getExistingAccess($divisionId, $category): array
    {
        if (!$divisionId) return [];

        return DivisionAccess::where('id', $divisionId)
            ->whereHas('access', function ($query) use ($category) {
                $query->where('access_name', 'like', "%{$category}%");
            })
            ->pluck('access_id')
            ->toArray();
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('division_name')
                    ->searchable(),
                TextColumn::make('required_workhours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('accesses_count')
                    ->label('Jumlah Akses')
                    ->sortable(),
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
                EditAction::make(),
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
            'index' => ListDivisions::route('/'),
            'create' => CreateDivision::route('/create'),
            'edit' => EditDivision::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('accesses'); // ← Wajib untuk akses jumlah akses
    }
}
