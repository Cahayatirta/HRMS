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
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;

// Filament - Tables
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\SelectFilter;

// App - Filament Resources
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\Pages\ListAttendances;
use App\Filament\Resources\AttendanceResource\Pages\CreateAttendance;
use App\Filament\Resources\AttendanceResource\Pages\EditAttendance;
use App\Filament\Resources\AttendanceResource\RelationManagers;

// App - Models
use App\Models\Attendance;
use App\Models\Task;


class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Human Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->relationship('employee', 'full_name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get, $livewire) {
                        if ($state) {
                            $livewire->employee_id = $state;
                            // Get completed tasks for the selected employee
                            $completedTasks = Task::whereHas('employees', function ($query) use ($state) {
                                $query->where('employees_id', $state);
                            })
                            ->where('status', 'completed')
                            ->get();

                            // dd("ini employee ID".$livewire->employee_id);

                            $taskData = $completedTasks->map(function ($task) {
                                return [
                                    'task_id' => $task->task_id,
                                    'attendance_task_id' => null, // Will be set when creating attendance_task record
                                ];
                            })->toArray();
                            
                            $set('completedTasks', $taskData);
                        } else {
                            $set('completedTasks', []);
                        }
                    }),
                TimePicker::make('start_time')
                    ->default(now())
                    ->required(),
                TimePicker::make('end_time'),
                Select::make('work_location')
                    ->required()
                    ->options([
                        'office' => 'Office',
                        'anywhere' => 'Anywhere',
                    ])
                    ->reactive()
                    ->disabled(fn ($get, $livewire) => $livewire->isCheckingLocation ?? false)
                    ->afterStateUpdated(function ($state, $set, $get, $livewire) {
                        $livewire->isCheckingLocation = true;
                        if ($state === 'office') {
                            // Memanggil function dari model Attendance
                            $attendance = new \App\Models\Attendance();
                            $location = $attendance->GetLocationAttribute(request());
                            
                            // Koordinat kantor
                            $officeLatitude = -8.65;
                            $officeLongitude = 115.2167;
                            $radiusInMeters = 500;
                            
                            if ($location['latitude'] === null || $location['longitude'] === null) {
                                // Jika tidak bisa mendapatkan lokasi
                                $set('latitude', null);
                                $set('longitude', null);
                                $set('location_status', 'unknown');
                                
                                // Tampilkan notifikasi error
                                \Filament\Notifications\Notification::make()
                                    ->title('Lokasi Tidak Terdeteksi')
                                    ->body('Tidak dapat mendeteksi lokasi Anda. Pastikan koneksi internet stabil.')
                                    ->danger()
                                    ->send();
                                    
                            } else {
                                // Set koordinat user
                                $set('latitude', $location['latitude']);
                                $set('longitude', $location['longitude']);
                                
                                // Hitung jarak ke kantor
                                $distance = $attendance->calculateDistance(
                                    $location['latitude'], 
                                    $location['longitude'],
                                    $officeLatitude,
                                    $officeLongitude
                                );
                                
                                if ($distance <= $radiusInMeters) {
                                    // User berada dalam radius kantor
                                    $set('location_status', 'in_office');
                                    
                                    \Filament\Notifications\Notification::make()
                                        ->title('Lokasi Terverifikasi')
                                        ->body("Anda berada dalam area kantor (jarak: " . round($distance) . " meter)")
                                        ->success()
                                        ->send();
                                        
                                } else {
                                    // User berada di luar radius kantor
                                    $set('location_status', 'outside_office');
                                    
                                    \Filament\Notifications\Notification::make()
                                        ->title('Lokasi Di Luar Area Kantor')
                                        ->body("Anda berada " . round($distance) . " meter dari kantor. Silakan pilih 'Anywhere' atau datang ke kantor.")
                                        ->warning()
                                        ->send();
                                }
                            }
                        } else {
                            // Jika memilih 'anywhere', reset data lokasi
                            $set('latitude', null);
                            $set('longitude', null);
                            $set('location_status', null);
                        }
                        $livewire->isCheckingLocation = false; 
                    }),
                TextInput::make('latitude')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => logger('Latitude changed:', [$state]))
                    ->disabled()
                    ->dehydrated()
                    ->hidden()
                    ,
                TextInput::make('longitude')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => logger('Longitude changed:', [$state]))
                    ->disabled()
                    ->dehydrated()
                    ->hidden()
                    ,
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('task_link'),
                Hidden::make('is_in_office_radius')->default(false),
                
                // Repeater for completed tasks
                Select::make('completed_task_ids')
                    ->label('Completed Tasks')
                    ->multiple()
                    ->options(function (Forms\Get $get) {
                        $employeeId = $get('employee_id');

                        if (!$employeeId) {
                            return [];
                        }

                        $today = now()->format('Y-m-d');

                        $tasks = Task::whereHas('employees', function ($query) use ($employeeId) {
                                $query->where('employee_id', $employeeId);
                            })
                            ->where('status', 'completed')
                            ->whereDate('updated_at', $today)
                            ->pluck('task_name', 'id');

                            // dd($tasks);
                        if ($tasks->isEmpty()) {
                            return ['__no_tasks__' => 'No Completed Task Today'];
                        }

                        return $tasks;
                    })
                    ->placeholder('Select completed tasks...')
                    ->disabled(fn (Forms\Get $get) =>
                        $get('employee_id') === null)
                    ->reactive()
                    // ->required(fn (Forms\Get $get) =>
                    //     $get('employee_id') !== null
                    // )
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (is_array($state) && in_array('__no_tasks__', $state)) {
                            // Hapus nilai dummy dari array
                            $filtered = array_filter($state, fn ($id) => $id !== '__no_tasks__');
                            $set('completed_task_ids', $filtered);
                        }
                    })
                    ->hint('Only shows tasks completed today')
                    ->columnSpanFull()
                    ->extraAttributes([
                        'title' => 'Only shows tasks completed today. Select employee first.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label('Start Time'),
                TextColumn::make('end_time')
                    ->label('End Time'),
                TextColumn::make('work_location')
                    ->label('Work Location')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'office' => 'success',
                        'anywhere' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('longitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('latitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('task_link')
                    ->label('Task Link')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attendanceTasks.task.task_name')
                    ->label('Completed Tasks')
                    ->badge()
                    ->separator(',')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('work_location')
                    ->options([
                        'office' => 'Office',
                        'anywhere' => 'Anywhere',
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
                ViewAction::make(),
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
            'index' => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'edit' => EditAttendance::route('/{record}/edit'),
        ];
    }
}