<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\View\View;
use Filament\Actions\Action;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected $listeners = ['fillLocationFromBrowser'];

    public float|null $latitude = null;
    public float|null $longitude = null;

    public function fillLocationFromBrowser($location)
    {
        logger('Lokasi diterima:', $location);

        $this->latitude = $location['latitude'];
        $this->longitude = $location['longitude'];

        $this->form->fill([
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
        ]);

        dd('Lokasi telah diisi:', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);
    }
                 
    protected function getHeaderActions(): array
    {
        return [
            Action::make('debugLocation')
                ->label('🔍 Debug Lokasi')
                ->color('gray')
                ->action(function () {
                    // dd([
                    //     'latitude' => $this->latitude,
                    //     'longitude' => $this->longitude,
                    // ]);
                    // filament()->notify('success', "Latitude: {$this->latitude}, Longitude: {$this->longitude}");
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

}
