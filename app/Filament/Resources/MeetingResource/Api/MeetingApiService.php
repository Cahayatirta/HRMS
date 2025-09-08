<?php
namespace App\Filament\Resources\MeetingResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\MeetingResource;
use Illuminate\Routing\Router;


class MeetingApiService extends ApiService
{
    protected static string | null $resource = MeetingResource::class;

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
