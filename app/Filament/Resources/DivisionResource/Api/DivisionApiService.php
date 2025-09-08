<?php
namespace App\Filament\Resources\DivisionResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\DivisionResource;
use Illuminate\Routing\Router;


class DivisionApiService extends ApiService
{
    protected static string | null $resource = DivisionResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
