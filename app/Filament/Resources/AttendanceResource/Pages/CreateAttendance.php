<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\View\View;
use Filament\Actions\Action;
use Illuminate\Http\Request;
use Livewire\Attributes\On;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected $listeners = ['fillLocationFromBrowser'];

    public float|null $latitude = null;
    public float|null $longitude = null;

    public bool $isCheckingLocation = false;

    public int|null $employee_id = null;

    // #[On('fillLocationFromBrowser')]
    // public function fillLocationFromBrowser($location)
    // {
    //     dd($location);
    //     logger('Lokasi diterima:', $location);

    //     $this->latitude = $location['latitude'];
    //     $this->longitude = $location['longitude'];

    //     $this->form->fill([
    //         'latitude' => $location['latitude'],
    //         'longitude' => $location['longitude'],
    //     ]);

    // }
                 
    protected function getHeaderActions(): array
    {
        return [
            Action::make('debugLocation')
                ->label('🔍 Debug Lokasi')
                ->color('gray')
                ->action(function () {
                    // Memanggil function dari model Attendance
                    $attendance = new \App\Models\Attendance();
                    $location = $attendance->GetLocationAttribute(request());

                    // dd('Lokasi dari model:', $location);
                    if($location['latitude'] === null || $location['longitude'] === null) {
                        $this->form->fill([
                            'latitude' => "test",
                            'longitude' => "test",
                        ]);
                    }else {
                        $this->form->fill([
                            'latitude' => $location['latitude'],
                            'longitude' => $location['longitude'],
                        ]);
                    }

                    // dd($this->latitude, $this->longitude);
                    // session_start();
                    // Get session storage data
                    // $sessionStorage = [
                    //     'latitude' => session('coords.latitude'),
                    //     'longitude' => session('coords.longitude')
                    // ];

                    // Debug session storage data
                    // logger('Session storage data:', $sessionStorage);
                    // dd('Session storage:', $sessionStorage);
                    // Lakukan sesuatu dengan $location, misal dd($location)
                    // dd($location);
                }),
        ];
    }

    public function getFooter(): ?\Illuminate\View\View
    {
        logger('Latitude saat getFooter:', ['lat' => $this->latitude, 'lng' => $this->longitude]);

        return view('attendance.scripts', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }

    protected function afterCreate(): void
    {
        $attendance = $this->record;
        $completedTasks = $this->data['completedTasks'] ?? [];

        // dd($attendance->id);

        // Save attendance tasks
        foreach ($completedTasks as $taskData) {
            if (isset($taskData['task_id']) && $taskData['task_id']) {
                \App\Models\AttendanceTask::create([
                    'attendance_id' => $attendance->id,
                    'task_id' => $taskData['task_id'],
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove completedTasks from main data to prevent it from being saved to attendance table
        unset($data['completedTasks']);

        // dd($data);
        
        return $data;
    }

}
