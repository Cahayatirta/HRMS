<?php
namespace App\Filament\Resources\ServiceTypeResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\ServiceTypeResource;
use Illuminate\Routing\Router;


class ServiceTypeApiService extends ApiService
{
    protected static string | null $resource = ServiceTypeResource::class;

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
