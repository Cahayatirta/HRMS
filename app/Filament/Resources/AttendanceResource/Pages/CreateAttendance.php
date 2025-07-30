<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\View\View;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected $listeners = ['fillLocationFromBrowser'];

    public function fillLocationFromBrowser($location)
    {
        logger('Lokasi diterima:', $location);

        $this->form->fill([
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
        ]);
    }                   

    public function getFooter(): ?\Illuminate\View\View
    {
        return view('attendance.scripts');
    }

}
