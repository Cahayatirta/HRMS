<?php

namespace App\Filament\Resources\WorkhourPlanResource\Pages;

use App\Filament\Resources\WorkhourPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkhourPlan extends EditRecord
{
    protected static string $resource = WorkhourPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
