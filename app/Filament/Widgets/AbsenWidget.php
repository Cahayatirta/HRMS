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
            
            if ($this->todayAttendance && $this->todayAttendance->end_time === null) {
                // Currently checked in
                $this->attendanceType = 'check_out';
            } else {
                // Either no attendance today or already checked out
                // Always allow check-in regardless of hours worked
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
                    ->visible(fn () => $this->attendanceType === 'check_in')
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
                        $this->attendanceType === 'check_in' && $get('work_location') === 'anywhere'
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

            // Get required hours and total worked hours
            $requiredHours = $employee->division->required_workhours ?? 8;
            $totalTodayHours = $this->getTodayTotalWorkHours($employee);
            
            // Determine notification message based on current work hours
            if ($totalTodayHours >= $requiredHours) {
                Notification::make()
                    ->title('Check-in Successful')
                    ->body('You have successfully checked in at ' . now()->format('H:i') . '. You have already worked ' . number_format($totalTodayHours, 1) . 'h today (Required: ' . $requiredHours . 'h)')
                    ->success()
                    ->send();
            } elseif ($totalTodayHours > 0) {
                $remainingHours = $requiredHours - $totalTodayHours;
                Notification::make()
                    ->title('Check-in Successful')
                    ->body('You have successfully checked in at ' . now()->format('H:i') . '. You have worked ' . number_format($totalTodayHours, 1) . 'h today, need ' . number_format($remainingHours, 1) . 'h more (Required: ' . $requiredHours . 'h)')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Check-in Successful')
                    ->body('You have successfully checked in at ' . now()->format('H:i') . '. Required work hours: ' . $requiredHours . 'h')
                    ->success()
                    ->send();
            }

            $this->checkTodayAttendance();
            $this->form->fill();
            $this->dispatch('attendance-updated');

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

            $minimumPerSession = $requiredHours / 2;

            if ($totalTodayHours < $minimumPerSession) {
                $remainingHours = $minimumPerSession - $totalTodayHours;
                
                Notification::make()
                    ->title('Insufficient Work Hours')
                    ->body("You need to work " . number_format($remainingHours, 1) . " more hours. Each work session must be at least {$minimumPerSession} hours.")
                    ->warning()
                    ->send();
                return;
            }

            // Update attendance with end time (always allow checkout)
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

            // Determine notification message based on work hours
            if ($totalTodayHours >= $requiredHours) {
                Notification::make()
                    ->title('Check-out Successful')
                    ->body('You have successfully checked out at ' . now()->format('H:i') . '. Total work today: ' . number_format($totalTodayHours, 1) . 'h (Required: ' . $requiredHours . 'h) ✓')
                    ->success()
                    ->send();
            } else {
                $remainingHours = $requiredHours - $totalTodayHours;
                Notification::make()
                    ->title('Check-out Successful')
                    ->body('You have successfully checked out at ' . now()->format('H:i') . '. Total work today: ' . number_format($totalTodayHours, 1) . 'h (Required: ' . $requiredHours . 'h). You can check-in again to complete remaining ' . number_format($remainingHours, 1) . 'h')
                    ->warning()
                    ->send();
            }

            $this->checkTodayAttendance();
            $this->form->fill();
            $this->dispatch('attendance-updated');

        } catch (\Exception $e) {
            Notification::make()
                ->title('Check-out Failed')
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

        $requiredHours = $employee->division->required_workhours ?? 8;

        if (!$this->todayAttendance) {
            return [
                'status' => 'not_checked_in',
                'message' => 'You haven\'t checked in today (Required: ' . $requiredHours . 'h)',
                'color' => 'gray'
            ];
        }

        if ($this->todayAttendance->end_time === null) {
            $startTime = \Carbon\Carbon::parse($this->todayAttendance->start_time);
            $currentDuration = $startTime->diffInHours(now(), true);
            $totalTodayHours = $this->getTodayTotalWorkHours($employee) + $currentDuration;
            
            return [
                'status' => 'checked_in',
                'message' => 'Checked in at ' . $this->todayAttendance->start_time . ' (Current session: ' . number_format($currentDuration, 1) . 'h, Total today: ' . number_format($totalTodayHours, 1) . 'h / ' . $requiredHours . 'h)',
                'color' => 'success'
            ];
        }

        // Check total work hours for the day
        $totalTodayHours = $this->getTodayTotalWorkHours($employee);

        if ($totalTodayHours < $requiredHours) {
            $remainingHours = $requiredHours - $totalTodayHours;
            return [
                'status' => 'insufficient_hours',
                'message' => 'Work hours: ' . number_format($totalTodayHours, 1) . 'h / ' . $requiredHours . 'h required (' . number_format($remainingHours, 1) . 'h remaining). You can check-in again.',
                'color' => 'warning'
            ];
        }

        return [
            'status' => 'completed',
            'message' => 'Work completed: ' . number_format($totalTodayHours, 1) . 'h / ' . $requiredHours . 'h required ✓',
            'color' => 'info'
        ];
    }
}