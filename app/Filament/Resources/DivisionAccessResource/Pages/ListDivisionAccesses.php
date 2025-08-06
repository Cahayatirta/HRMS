<?php

namespace App\Filament\Resources\DivisionAccessResource\Pages;

use App\Filament\Resources\DivisionAccessResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisionAccesses extends ListRecords
{
    protected static string $resource = DivisionAccessResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
