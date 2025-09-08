<?php
namespace App\Filament\Resources\AccessResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AccessResource;
use Illuminate\Routing\Router;


class AccessApiService extends ApiService
{
    protected static string | null $resource = AccessResource::class;

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
