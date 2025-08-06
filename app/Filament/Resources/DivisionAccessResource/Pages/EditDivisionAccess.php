<?php

namespace App\Filament\Resources\DivisionAccessResource\Pages;

use App\Filament\Resources\DivisionAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDivisionAccess extends EditRecord
{
    protected static string $resource = DivisionAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
