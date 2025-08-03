<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use DiogoGPinto\GeolocateMe\Data\Coordinates;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkIn')
            ->label('Check In')
            ->withGeolocation()
            ->action(function (Coordinates $coordinates) {
                // dd($coordinates);
                if ($coordinates->hasError()) {
                    Notification::make()
                        ->danger()
                        ->title('Lokasi tidak bisa diakses')
                        ->body($coordinates->error)
                        ->send();
                    return;
                }
                $lat = $coordinates->latitude;
                $lng = $coordinates->longitude;
                $acc = $coordinates->accuracy;

                Notification::make()
                    ->success()
                    ->title('Lokasi berhasil diambil')
                    ->body("Lat: $lat, Lng: $lng, Akurasi: $acc meter")
                    ->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
