<?php
namespace App\Filament\Resources\ClientResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\ClientResource;
use Illuminate\Routing\Router;


class ClientApiService extends ApiService
{
    protected static string | null $resource = ClientResource::class;

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
