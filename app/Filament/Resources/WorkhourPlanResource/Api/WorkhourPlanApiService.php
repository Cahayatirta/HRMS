<?php
namespace App\Filament\Resources\WorkhourPlanResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\WorkhourPlanResource;
use Illuminate\Routing\Router;


class WorkhourPlanApiService extends ApiService
{
    protected static string | null $resource = WorkhourPlanResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class,
            Handlers\WorkhourPlanHandler::class
        ];
    }
}
