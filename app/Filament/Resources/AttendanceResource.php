<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Toggle;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    // ->hidden()
                    ,
                TextInput::make('longitude')
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => logger('Longitude changed:', [$state]))
                    ->disabled()
                    ->dehydrated()
                    // ->hidden()
                    ,
                FileUpload::make('image_path')
                    ->image(),
                TextInput::make('task_link'),
                Hidden::make('is_in_office_radius')->default(false),
                
                // Repeater for completed tasks
                Repeater::make('completedTasks')
                    ->label('Completed Tasks')
                    ->schema([
                        Hidden::make('attendance_task_id'),
                        Select::make('task_id')
                            ->label('Task')
                            ->options(function (Forms\Get $get, $livewire) {
                                $employeeId = $get('../../employee_id');
                                // $employeeId = $livewire->employee_id;
                                // if($employeeId) {
                                //     return [$employeeId];
                                // }
                                if (!$employeeId) {
                                    return [];
                                }
                                
                                $tasks = Task::whereHas('employees', function ($query) use ($employeeId) {
                                    $query->where('employee_id', $employeeId);
                                })
                                ->where('status', 'completed')
                                ->pluck('task_name', 'id');
                                
                                return $tasks;
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $task = Task::find($state);
                                    if ($task) {
                                        $set('task_info', $task->task_description ?? 'No description');
                                    }
                                }
                            }),
                        Forms\Components\Textarea::make('task_info')
                            ->label('Task Description')
                            ->disabled()
                            ->rows(2),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->addable(true)
                    ->deletable(true)
                    ->reorderable(false)
                    ->collapsed()
                    ->itemLabel(fn (array $state): ?string => 
                        $state['task_id'] ? Task::find($state['task_id'])?->task_name : 'New Task'
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time'),
                Tables\Columns\TextColumn::make('work_location')
                    ->label('Work Location')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'office' => 'success',
                        'anywhere' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Image')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('task_link')
                    ->label('Task Link')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('attendanceTasks.task.task_name')
                    ->label('Completed Tasks')
                    ->badge()
                    ->separator(',')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_deleted')
                    ->label('Deleted')
                    ->sortable()
                    ->toggleable(),
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}