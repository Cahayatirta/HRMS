<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Task;
use App\Models\AttendanceTask;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class AbsenWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.absen-widget';

    // protected static ?string $pollingInterval = '1'; 
    
    protected int | string | array $columnSpan = 'full';
    
    public ?array $data = [];
    public ?string $attendanceType = null;
    public ?Attendance $todayAttendance = null;
    public bool $isCheckingLocation = false;

    public function mount(): void
    {
        $this->checkTodayAttendance();
        $this->form->fill();
    }

    protected function checkTodayAttendance(): void
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if ($employee) {
            // Get the latest attendance for today
            $this->todayAttendance = $employee->attendances()
                ->whereDate('created_at', today())
                ->where('is_deleted', false)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($this->todayAttendance) {
                if ($this->todayAttendance->end_time === null) {
                    $this->attendanceType = 'check_out';
                } else {
                    // Check total work hours including all sessions today
                    $requiredHours = $employee->division->required_workhours ?? 8;
                    $totalTodayHours = $this->getTodayTotalWorkHours($employee);
                    
                    if ($totalTodayHours >= $requiredHours) {
                        $this->attendanceType = 'completed';
                    } else {
                        // Allow additional check-in if hours not met
                        $this->attendanceType = 'additional_check_in';
                    }
                }
            } else {
                $this->attendanceType = 'check_in';
            }
        }
    }

    protected function getTodayTotalWorkHours($employee): float
    {
        $todayAttendances = $employee->attendances()
            ->whereDate('created_at', today())
            ->where('is_deleted', false)
            ->whereNotNull('end_time')
            ->get();

        $totalMinutes = 0;
        
        foreach ($todayAttendances as $attendance) {
            $startTime = \Carbon\Carbon::parse($attendance->start_time);
            $endTime = \Carbon\Carbon::parse($attendance->end_time);
            $totalMinutes += $startTime->diffInMinutes($endTime);
        }

        return $totalMinutes / 60; // Convert to hours
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Check In Form
                Select::make('work_location')
                    ->label('Work Location')
                    ->options([
                        'office' => 'Office',
                        'anywhere' => 'Anywhere',
                    ])
                    ->required()
                    ->reactive()
                    ->visible(fn () => in_array($this->attendanceType, ['check_in', 'additional_check_in']))
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'office') {
                            $this->handleOfficeLocation($set);
                        } else {
                            $this->resetLocationData($set);
                        }
                    }),

                FileUpload::make('image_path')
                    ->label('Photo')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios(['1:1'])
                    ->directory('attendance-photos')
                    ->visibility('private')
                    ->required()
                    ->visible(fn (Forms\Get $get) => 
                        in_array($this->attendanceType, ['check_in', 'additional_check_in']) && $get('work_location') === 'anywhere'
                    ),

                Hidden::make('latitude'),
                Hidden::make('longitude'),

                // Check Out Form
                TextInput::make('task_link')
                    ->label('Task Link')
                    ->url()
                    ->placeholder('https://example.com/task')
                    ->visible(fn () => $this->attendanceType === 'check_out'),

                Select::make('completed_task_ids')
                    ->label('Completed Tasks')
                    ->multiple()
                    ->options(function () {
                        if ($this->attendanceType !== 'check_out') {
                            return [];
                        }

                        $user = Auth::user();
                        $employee = Employee::where('user_id', $user->id)->first();

                        if (!$employee) {
                            return [];
                        }

                        $today = now()->format('Y-m-d');

                        $tasks = Task::whereHas('employees', function ($query) use ($employee) {
                                $query->where('employee_id', $employee->id);
                            })
                            ->where('status', 'completed')
                            ->whereDate('updated_at', $today)
                            ->pluck('task_name', 'id');

                        if ($tasks->isEmpty()) {
                            return ['__no_tasks__' => 'No Completed Task Today'];
                        }

                        return $tasks;
                    })
                    ->placeholder('Select completed tasks...')
                    ->visible(fn () => $this->attendanceType === 'check_out')
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (is_array($state) && in_array('__no_tasks__', $state)) {
                            $filtered = array_filter($state, fn ($id) => $id !== '__no_tasks__');
                            $set('completed_task_ids', $filtered);
                        }
                    }),
            ])
            ->statePath('data');
    }

    protected function handleOfficeLocation($set): void
    {
        $this->isCheckingLocation = true;
        
        try {
            $attendance = new Attendance();
            $location = $attendance->GetLocationAttribute(request());
            
            $officeLatitude = -8.65;
            $officeLongitude = 115.2167;
            $radiusInMeters = 500;
            
            if ($location['latitude'] === null || $location['longitude'] === null) {
                $set('latitude', null);
                $set('longitude', null);
                
                Notification::make()
                    ->title('Location Not Detected')
                    ->body('Unable to detect your location. Please ensure stable internet connection.')
                    ->danger()
                    ->send();
            } else {
                $set('latitude', $location['latitude']);
                $set('longitude', $location['longitude']);
                
                $distance = $attendance->calculateDistance(
                    $location['latitude'], 
                    $location['longitude'],
                    $officeLatitude,
                    $officeLongitude
                );
                
                if ($distance <= $radiusInMeters) {
                    Notification::make()
                        ->title('Location Verified')
                        ->body("You are within office area (distance: " . round($distance) . " meters)")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Outside Office Area')
                        ->body("You are " . round($distance) . " meters from office. Please select 'Anywhere' or come to office.")
                        ->warning()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error detecting location: ' . $e->getMessage())
                ->danger()
                ->send();
        }
        
        $this->isCheckingLocation = false;
    }

    protected function resetLocationData($set): void
    {
        $set('latitude', null);
        $set('longitude', null);
    }

    public function checkIn(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            Notification::make()
                ->title('Error')
                ->body('Employee profile not found.')
                ->danger()
                ->send();
            return;
        }

        // Validate office location if selected
        if ($data['work_location'] === 'office') {
            if (empty($data['latitude']) || empty($data['longitude'])) {
                Notification::make()
                    ->title('Location Required')
                    ->body('Location verification is required for office check-in.')
                    ->warning()
                    ->send();
                return;
            }
        }

        // Validate photo for anywhere location
        if ($data['work_location'] === 'anywhere' && empty($data['image_path'])) {
            Notification::make()
                ->title('Photo Required')
                ->body('Photo is required for remote work check-in.')
                ->warning()
                ->send();
            return;
        }

        try {
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'start_time' => now()->format('H:i:s'),
                'work_location' => $data['work_location'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'image_path' => $data['image_path'] ?? null,
            ]);

            Notification::make()
                ->title('Check-in Successful')
                ->body('You have successfully checked in at ' . now()->format('H:i'))
                ->success()
                ->send();

            $this->checkTodayAttendance();
            $this->form->fill();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Check-in Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function checkOut(): void
    {
        if (!$this->todayAttendance) {
            Notification::make()
                ->title('Error')
                ->body('No check-in record found for today.')
                ->warning()
                ->send();
            return;
        }

        $data = $this->form->getState();

        try {
            $user = Auth::user();
            $employee = Employee::where('user_id', $user->id)->first();
            
            // Get required work hours from division
            $requiredHours = $employee->division->required_workhours ?? 8;
            
            // Calculate work duration for current session
            $startTime = \Carbon\Carbon::parse($this->todayAttendance->start_time);
            $endTime = now();
            $currentSessionHours = $startTime->diffInHours($endTime, true);
            
            // Get total work hours including previous sessions today
            $totalTodayHours = $this->getTodayTotalWorkHours($employee) + $currentSessionHours;
            
            // Check if work hours meet requirement
            if ($totalTodayHours < $requiredHours) {
                $remainingHours = $requiredHours - $totalTodayHours;
                
                Notification::make()
                    ->title('Insufficient Work Hours')
                    ->body("You need to work " . number_format($remainingHours, 1) . " more hours. Required: {$requiredHours}h, Current: " . number_format($totalTodayHours, 1) . "h")
                    ->warning()
                    ->send();
                return;
            }

            // Update attendance with end time
            $this->todayAttendance->update([
                'end_time' => now()->format('H:i:s'),
                'task_link' => $data['task_link'] ?? null,
            ]);

            // Save completed tasks if selected
            if (!empty($data['completed_task_ids'])) {
                $taskIds = array_filter($data['completed_task_ids'], fn ($id) => $id !== '__no_tasks__');
                
                foreach ($taskIds as $taskId) {
                    AttendanceTask::create([
                        'attendance_id' => $this->todayAttendance->id,
                        'task_id' => $taskId,
                    ]);
                }
            }

            Notification::make()
                ->title('Check-out Successful')
                ->body('You have successfully checked out at ' . now()->format('H:i') . '. Total work: ' . number_format($totalTodayHours, 1) . 'h')
                ->success()
                ->send();

            $this->checkTodayAttendance();
            $this->form->fill();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Check-out Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function additionalCheckIn(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            Notification::make()
                ->title('Error')
                ->body('Employee profile not found.')
                ->danger()
                ->send();
            return;
        }

        // Validate office location if selected
        if ($data['work_location'] === 'office') {
            if (empty($data['latitude']) || empty($data['longitude'])) {
                Notification::make()
                    ->title('Location Required')
                    ->body('Location verification is required for office check-in.')
                    ->warning()
                    ->send();
                return;
            }
        }

        // Validate photo for anywhere location
        if ($data['work_location'] === 'anywhere' && empty($data['image_path'])) {
            Notification::make()
                ->title('Photo Required')
                ->body('Photo is required for remote work check-in.')
                ->warning()
                ->send();
            return;
        }

        try {
            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'start_time' => now()->format('H:i:s'),
                'work_location' => $data['work_location'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'image_path' => $data['image_path'] ?? null,
            ]);

            $requiredHours = $employee->division->required_workhours ?? 8;

            Notification::make()
                ->title('Additional Check-in Successful')
                ->body('You have checked in again at ' . now()->format('H:i') . '. Please complete your remaining work hours.')
                ->success()
                ->send();

            $this->checkTodayAttendance();
            $this->form->fill();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Check-in Failed')
                ->body('Error: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getAttendanceStatus(): array
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            return [
                'status' => 'no_employee',
                'message' => 'Employee profile not found',
                'color' => 'gray'
            ];
        }

        if (!$this->todayAttendance) {
            return [
                'status' => 'not_checked_in',
                'message' => 'You haven\'t checked in today',
                'color' => 'gray'
            ];
        }

        if ($this->todayAttendance->end_time === null) {
            $startTime = \Carbon\Carbon::parse($this->todayAttendance->start_time);
            $currentDuration = $startTime->diffInHours(now(), true);
            
            return [
                'status' => 'checked_in',
                'message' => 'Checked in at ' . $this->todayAttendance->start_time . ' (Working: ' . number_format($currentDuration, 1) . 'h)',
                'color' => 'success'
            ];
        }

        // Check total work hours for the day
        $requiredHours = $employee->division->required_workhours ?? 8;
        $totalTodayHours = $this->getTodayTotalWorkHours($employee);

        if ($totalTodayHours < $requiredHours) {
            $remainingHours = $requiredHours - $totalTodayHours;
            return [
                'status' => 'insufficient_hours',
                'message' => 'Work hours insufficient: ' . number_format($totalTodayHours, 1) . 'h / ' . $requiredHours . 'h required (' . number_format($remainingHours, 1) . 'h remaining)',
                'color' => 'warning'
            ];
        }

        return [
            'status' => 'completed',
            'message' => 'Work completed: Total ' . number_format($totalTodayHours, 1) . 'h worked today',
            'color' => 'info'
        ];
    }
}