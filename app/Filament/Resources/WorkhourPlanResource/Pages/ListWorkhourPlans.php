<?php

namespace App\Filament\Resources\WorkhourPlanResource\Pages;

use App\Filament\Resources\WorkhourPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkhourPlans extends ListRecords
{
    protected static string $resource = WorkhourPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
